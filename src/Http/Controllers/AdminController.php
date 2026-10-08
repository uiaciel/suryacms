<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;
use Spatie\PdfToImage\Pdf;
use Uiaciel\SuryaCms\Models\Gallery;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Setting;
use ZipArchive;

class AdminController extends Controller
{
    public function manifest()
    {

        $setting = Setting::first();
        $adminPrefix = config('suryacms.admin_prefix', 'admin');

        return response()->json([

            'name' => $setting->url,
            'short_name' => $setting->url,
            'start_url' => '/'.$adminPrefix,
            'scope' => '/'.$adminPrefix,
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#0f172a',
            'icons' => [
                [
                    'src' => asset($setting->logo),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
        ]);
    }

    public function tinymce(Request $request)
    {
        try {
            // 1. Validasi File (Gambar & PDF)
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            ]);

            if (! $request->hasFile('file')) {
                return response()->json([
                    'error' => 'No file uploaded',
                ], 400);
            }

            $file = $request->file('file');
            $originName = $file->getClientOriginalName();
            $baseName = pathinfo($originName, PATHINFO_FILENAME);
            $slugName = Str::slug($baseName);
            $timestamp = now()->format('YmdHis');
            $extension = strtolower($file->getClientOriginalExtension());

            // Inisialisasi Model Gallery
            $gallery = new Gallery;
            $gallery->name = $baseName;
            $gallery->alt_text = $baseName;
            $gallery->description = 'Uploaded via TinyMCE Editor';
            $gallery->category = $request->input('category', 'POST');
            $gallery->status = 'Publish';
            $gallery->is_tinymce_upload = true;
            $gallery->file_size = round($file->getSize() / 1024, 2).' KB';

            if ($extension === 'pdf') {
                // A. LOGIKA UPLOAD PDF
                $pdfFileName = "{$timestamp}_{$slugName}.pdf";
                Storage::disk('public')->putFileAs('galleries', $file, $pdfFileName);

                $gallery->image_path = 'galleries/'.$pdfFileName;
                $gallery->mime_type = 'pdf';

                // Generate Cover Image dari Halaman 1 PDF
                $pdfPath = storage_path('app/public/'.$gallery->image_path);
                $tempCoverPath = storage_path('app/public/galleries/temp_'.$timestamp.'.jpg');

                if (! file_exists(storage_path('app/public/galleries/covers'))) {
                    mkdir(storage_path('app/public/galleries/covers'), 0755, true);
                }

                // Render halaman 1 ke temp image
                $pdf = new Pdf($pdfPath);
                $pdf->selectPage(1)->saveExtraPageAsPage($tempCoverPath);

                // Convert temp cover image ke WebP
                $manager = new ImageManager(new Driver);
                $coverFileName = "{$timestamp}_cover_{$slugName}.webp";
                $convertedCover = $manager->read($tempCoverPath)->encode(new WebpEncoder(quality: 70));

                Storage::disk('public')->put('galleries/covers/'.$coverFileName, $convertedCover->__toString());
                $gallery->cover_path = 'galleries/covers/'.$coverFileName;

                // Hapus temp image
                if (file_exists($tempCoverPath)) {
                    unlink($tempCoverPath);
                }

            } else {
                // B. LOGIKA UPLOAD GAMBAR
                $manager = new ImageManager(new Driver);
                $fileName = "{$timestamp}_{$slugName}.webp";

                $convertedImage = $manager->read($file->getRealPath())->encode(new WebpEncoder(quality: 70));

                Storage::disk('public')->put('galleries/'.$fileName, $convertedImage->__toString());

                $gallery->image_path = 'galleries/'.$fileName;
                $gallery->cover_path = 'galleries/'.$fileName;
                $gallery->mime_type = 'image';
            }

            $gallery->save();

            // 2. Return Response JSON
            // Properti 'location' wajib ada untuk bawaan TinyMCE image plugin
            return response()->json([
                'location' => Storage::url($gallery->image_path),
                'cover_location' => Storage::url($gallery->cover_path),
                'mime_type' => $gallery->mime_type,
                'title' => $gallery->name,
                'media_id' => $gallery->id,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed: '.implode(', ', array_merge(...array_values($e->errors()))),
            ], 422);
        } catch (\Exception $e) {
            Log::error('TinyMCE upload error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'File upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function gallery()
    {
        $titlePage = 'Gallery';
        $categoryGallery = Gallery::distinct()->pluck('category');
        $galleries = Gallery::all();

        return view('suryacms::livewire.admin.gallery', [
            'titlePage' => $titlePage,
            'galleries' => $galleries,
            'categoryGallery' => $categoryGallery,

        ]);
    }

    public function uploadTheme(Request $request)
    {
        // Validasi input
        $request->validate([
            'theme_zip' => 'required|file|mimes:zip|max:102400', // max 100MB
        ]);

        // Ambil file upload
        $file = $request->file('theme_zip');

        // Siapkan folder sementara
        $tmpPath = storage_path('Uiaciel\SuryaCms/temp');
        File::ensureDirectoryExists($tmpPath);

        $fileName = uniqid('theme_').'.zip';
        $filePath = $tmpPath.'/'.$fileName;

        // Pindahkan ke folder temp
        $file->move($tmpPath, $fileName);

        // Lokasi tujuan ekstrak (folder tema)
        $extractPath = base_path('resources/views/frontend/');

        // Buka file zip
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();

            // Hapus file zip setelah ekstrak
            File::delete($filePath);

            return back()->with('message', '✅ Theme uploaded and extracted successfully.');
        } else {
            // Jika gagal membuka zip
            return back()->with('error', '❌ Failed to open ZIP file. Check the archive integrity.');
        }
    }

    public function saveGallery(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            'category' => 'nullable|string|max:255',
            'status' => 'in:Publish,Draft',
            'alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $gallery = new Gallery;
            $gallery->name = $request->name;
            $gallery->description = $request->description;
            $gallery->category = $request->category;
            $gallery->status = $request->status ?? 'Publish';
            $gallery->alt_text = $request->alt_text ?? $request->name;

            if ($request->hasFile('image_path')) {
                $file = $request->file('image_path');
                $timestamp = now()->format('YmdHis');
                $slugTitle = str_replace(' ', '_', strtolower($request->name));
                $extension = strtolower($file->getClientOriginalExtension());

                // Simpan metadata awal
                $gallery->file_size = round($file->getSize() / 1024, 2).' KB';

                if ($extension === 'pdf') {
                    // 1. Simpan File PDF
                    $pdfFileName = "{$timestamp}_gallery_{$slugTitle}.pdf";
                    Storage::disk('public')->putFileAs('galleries', $file, $pdfFileName);
                    $gallery->image_path = 'galleries/'.$pdfFileName;
                    $gallery->mime_type = 'pdf';

                    // 2. Generate Cover Image dari Halaman 1 PDF
                    $pdfPath = storage_path('app/public/'.$gallery->image_path);
                    $tempCoverPath = storage_path('app/public/galleries/temp_'.$timestamp.'.jpg');

                    // Pastikan folder target ada
                    if (! file_exists(storage_path('app/public/galleries'))) {
                        mkdir(storage_path('app/public/galleries'), 0755, true);
                    }

                    // Render halaman 1 PDF ke temp image
                    $pdf = new Pdf($pdfPath);
                    $pdf->selectPage(1)->saveExtraPageAsPage($tempCoverPath);

                    // Convert temp cover image ke WebP
                    $manager = new ImageManager(new Driver);
                    $coverFileName = "{$timestamp}_cover_{$slugTitle}.webp";
                    $convertedCover = $manager->read($tempCoverPath)->encode(new WebpEncoder(quality: 70));

                    Storage::disk('public')->put('galleries/covers/'.$coverFileName, $convertedCover->__toString());
                    $gallery->cover_path = 'galleries/covers/'.$coverFileName;

                    // Hapus temp image
                    if (file_exists($tempCoverPath)) {
                        unlink($tempCoverPath);
                    }

                } else {
                    // Jika File adalah Gambar
                    $manager = new ImageManager(new Driver);
                    $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

                    $convertedImage = $manager->read($file->getRealPath())->encode(new WebpEncoder(quality: 70));

                    Storage::disk('public')->put('galleries/'.$fileName, $convertedImage->__toString());

                    $gallery->image_path = 'galleries/'.$fileName;
                    $gallery->cover_path = 'galleries/'.$fileName; // Gambar asli berfungsi juga sebagai cover
                    $gallery->mime_type = 'image';
                }
            }

            $gallery->save();

            return redirect()->back()->with('message', 'Gallery created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create gallery: '.$e->getMessage());
        }
    }

    public function editGallery(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            'category' => 'nullable|string|max:255',
            'status' => 'in:Publish,Draft',
            'alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $gallery = Gallery::find($id);

            if (! $gallery) {
                return redirect()->back()->with('error', 'Gallery not found.');
            }

            $gallery->name = $request->name;
            $gallery->description = $request->description;
            $gallery->category = $request->category;
            $gallery->status = $request->status;
            $gallery->alt_text = $request->alt_text ?? $request->name;

            if ($request->hasFile('image_path')) {
                // Hapus File Utama Lama jika Ada
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }

                // Hapus File Cover Lama jika Ada dan beda lokasi dari image_path
                if ($gallery->cover_path && $gallery->cover_path !== $gallery->image_path && Storage::disk('public')->exists($gallery->cover_path)) {
                    Storage::disk('public')->delete($gallery->cover_path);
                }

                $file = $request->file('image_path');
                $timestamp = now()->format('YmdHis');
                $slugTitle = str_replace(' ', '_', strtolower($request->name));
                $extension = strtolower($file->getClientOriginalExtension());

                $gallery->file_size = round($file->getSize() / 1024, 2).' KB';

                if ($extension === 'pdf') {
                    // Upload PDF Baru
                    $pdfFileName = "{$timestamp}_gallery_{$slugTitle}.pdf";
                    Storage::disk('public')->putFileAs('galleries', $file, $pdfFileName);
                    $gallery->image_path = 'galleries/'.$pdfFileName;
                    $gallery->mime_type = 'pdf';

                    // Generate Cover Baru
                    $pdfPath = storage_path('app/public/'.$gallery->image_path);
                    $tempCoverPath = storage_path('app/public/galleries/temp_'.$timestamp.'.jpg');

                    $pdf = new Pdf($pdfPath);
                    $pdf->selectPage(1)->saveExtraPageAsPage($tempCoverPath);

                    $manager = new ImageManager(new Driver);
                    $coverFileName = "{$timestamp}_cover_{$slugTitle}.webp";
                    $convertedCover = $manager->read($tempCoverPath)->encode(new WebpEncoder(quality: 70));

                    Storage::disk('public')->put('galleries/covers/'.$coverFileName, $convertedCover->__toString());
                    $gallery->cover_path = 'galleries/covers/'.$coverFileName;

                    if (file_exists($tempCoverPath)) {
                        unlink($tempCoverPath);
                    }
                } else {
                    // Upload Gambar Baru
                    $manager = new ImageManager(new Driver);
                    $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

                    $convertedImage = $manager->read($file->getRealPath())->encode(new WebpEncoder(quality: 70));

                    Storage::disk('public')->put('galleries/'.$fileName, $convertedImage->__toString());

                    $gallery->image_path = 'galleries/'.$fileName;
                    $gallery->cover_path = 'galleries/'.$fileName;
                    $gallery->mime_type = 'image';
                }
            }

            $gallery->save();

            return redirect()->back()->with('message', 'Gallery updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update gallery: '.$e->getMessage());
        }
    }

    public function pagebuilder()
    {
        $titlePage = 'Page Builder';

        return view('suryacms::livewire.admin.page-builder.index', [
            'titlePage' => $titlePage,
            'pagesbuilder' => Page::whereNotNull('html')->get(),
        ]);
    }
}
