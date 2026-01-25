<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;
use Uiaciel\SuryaCms\Models\Gallery;
use ZipArchive;

class AdminController extends Controller
{
    public function tinymce(Request $request)
    {
        try {
            // Validate the incoming file
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            ]);

            if (! $request->hasFile('file')) {
                return response()->json([
                    'error' => 'No file uploaded',
                ], 400);
            }

            $manager = new ImageManager(new Driver);

            // Get original file name and create a slug for it
            $originName = $request->file('file')->getClientOriginalName();
            // Sanitize the file name for URL-friendly slug
            $slugName = \Illuminate\Support\Str::slug(pathinfo($originName, PATHINFO_FILENAME));
            $timestamp = now()->format('YmdHis');
            $fileName = "{$timestamp}_{$slugName}.webp";

            // Read the image, encode it to WebP with 70% quality
            $convertedImage = $manager->read($request->file('file')->getRealPath())->encode(new WebpEncoder(quality: 70));

            // Store the WebP image in the public disk under 'images' directory
            Storage::disk('public')->put('images/'.$fileName, $convertedImage->__toString());

            // Create a new Gallery entry for the uploaded image
            $gallery = new Gallery;
            $gallery->name = pathinfo($originName, PATHINFO_FILENAME);
            $gallery->description = 'Uploaded via TinyMCE';
            $gallery->image_path = 'images/'.$fileName;
            $gallery->category = 'POST';
            $gallery->status = 'Publish';
            $gallery->is_tinymce_upload = true; // Mark as uploaded via TinyMCE
            $gallery->save();

            // Return the URL of the stored image - MUST return location key for TinyMCE
            return response()->json([
                'location' => Storage::url('images/'.$fileName),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed: '.implode(', ', array_merge(...array_values($e->errors()))),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('TinyMCE upload error: '.$e->getMessage(), [
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
            'image_path' => 'required|image|max:30024', // Max 1MB
            'category' => 'nullable|string|max:255',
            'status' => 'in:Publish,Draft',
        ]);

        try {
            $gallery = new Gallery;
            $gallery->name = $request->name;
            $gallery->description = $request->description;
            $gallery->category = $request->category;
            $gallery->status = $request->status;

            if ($request->hasFile('image_path')) {
                $manager = new ImageManager(new Driver);

                $timestamp = now()->format('YmdHis');
                $slugTitle = str_replace(' ', '_', strtolower($request->name));
                $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

                // Read and encode the image to WebP format
                $convertedImage = $manager->read($request->file('image_path')->getRealPath())->encode(new WebpEncoder(quality: 70));

                // Save the WebP image to the storage
                Storage::disk('public')->put('galleries/'.$fileName, $convertedImage->__toString());

                // Set the image path for the gallery
                $gallery->image_path = 'galleries/'.$fileName;
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
            'image_path' => 'image|max:30024', // Max 1MB
            'category' => 'nullable|string|max:255',
            'status' => 'in:Publish,Draft',
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

            if ($request->hasFile('image_path')) {
                // Hapus gambar lama jika ada
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }

                $manager = new ImageManager(new Driver);

                $timestamp = now()->format('YmdHis');
                $slugTitle = str_replace(' ', '_', strtolower($request->name));
                $fileName = "{$timestamp}_gallery_{$slugTitle}.webp";

                // Read and encode the image to WebP format
                $convertedImage = $manager->read($request->file('image_path')->getRealPath())->encode(new WebpEncoder(quality: 70));

                // Save the WebP image to the storage
                Storage::disk('public')->put('galleries/'.$fileName, $convertedImage->__toString());

                // Set the image path for the gallery
                $gallery->image_path = 'galleries/'.$fileName;
            }

            $gallery->save();

            return redirect()->back()->with('message', 'Gallery updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update gallery: '.$e->getMessage());
        }
    }
}
