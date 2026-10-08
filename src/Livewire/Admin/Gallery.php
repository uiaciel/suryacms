<?php

namespace Uiaciel\SuryaCms\Livewire\Admin;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\PdfToImage\Enums\OutputFormat;
use Spatie\PdfToImage\Pdf;
use Uiaciel\SuryaCMS\Models\Gallery as ModelsGallery;

class Gallery extends Component
{
    use WithFileUploads;

    public $selected_id;

    public $filename;

    public $filealt_text;

    public $filedescription;

    public $fileimage_path; // single upload (edit)

    public $existing_file_path;

    public $filecategory;

    public $filestatus = 'Publish';

    public $cover_path;

    public $coverPreview;

    public $mime_type;

    public $isEdit = false;

    // ===== MULTIPLE UPLOAD PROPERTIES =====
    public $uploadFiles = [];          // array of TemporaryUploadedFile

    public $uploadCategory;            // kategori global untuk semua file

    public $uploadStatus = 'Publish';  // status global untuk semua file

    public $uploadAltText;             // alt text default (fallback ke nama file)

    protected function rules()
    {
        return [
            'filename' => 'required|string|max:255',
            'filedescription' => 'nullable|string',
            'fileimage_path' => $this->isEdit
                ? 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024'
                : 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            'filecategory' => 'nullable|string|max:255',
            'filestatus' => 'in:Publish,Draft',
            'filealt_text' => 'nullable|string|max:255',
        ];
    }

    // ===== RULES UNTUK MULTIPLE UPLOAD =====
    protected function multipleUploadRules()
    {
        return [
            'uploadFiles' => 'required|array|min:1|max:20',
            'uploadFiles.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp,pdf|max:30024',
            'uploadCategory' => 'nullable|string|max:255',
            'uploadStatus' => 'in:Publish,Draft',
            'uploadAltText' => 'nullable|string|max:255',
        ];
    }

    // ===== MESSAGES =====
    protected function messages()
    {
        return [
            'uploadFiles.required' => 'Pilih minimal satu file untuk diunggah.',
            'uploadFiles.max' => 'Maksimal 20 file dalam satu kali upload.',
            'uploadFiles.*.mimes' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, WEBP, SVG, atau PDF.',
            'uploadFiles.*.max' => 'Ukuran file maksimal 30MB per file.',
        ];
    }

    // Auto-fill nama file jika field name masih kosong saat upload (single)
    public function updatedFileimagePath()
    {
        $this->validateOnly('fileimage_path');

        if ($this->fileimage_path && empty($this->filename)) {
            $originalName = $this->fileimage_path->getClientOriginalName();
            $this->filename = pathinfo($originalName, PATHINFO_FILENAME);
        }
    }

    // ===== VALIDASI REALTIME UNTUK MULTIPLE UPLOAD =====
    public function updatedUploadFiles()
    {
        $this->validateOnly('uploadFiles.*');
    }

    // ===== HAPUS SATU FILE DARI ANTRIAN MULTIPLE =====
    public function removeUploadFile($index)
    {
        if (isset($this->uploadFiles[$index])) {
            unset($this->uploadFiles[$index]);
            $this->uploadFiles = array_values($this->uploadFiles); // reindex
        }
    }

    // ===== BERSIHKAN ANTRIAN MULTIPLE =====
    public function clearUploadQueue()
    {
        $this->uploadFiles = [];
        $this->uploadCategory = null;
        $this->uploadAltText = null;
        $this->uploadStatus = 'Publish';
    }

    public function resetFields()
    {
        $this->reset([
            'selected_id',
            'filename',
            'filealt_text',
            'filedescription',
            'fileimage_path',
            'existing_file_path',
            'filecategory',
            'cover_path',
            'coverPreview',
            'mime_type',
            'isEdit',
            'uploadFiles',
            'uploadCategory',
            'uploadAltText',
        ]);
        $this->filestatus = 'Publish';
        $this->uploadStatus = 'Publish';
    }

    // ===== SIMPAN BANYAK FILE SEKALIGUS =====
    public function saveMultipleGallery()
    {
        $this->validate($this->multipleUploadRules());

        $successCount = 0;
        $failedFiles = [];
        $pdfCoverWarnings = [];
        $webpConvertedCount = 0;

        foreach ($this->uploadFiles as $file) {
            $originalFilename = $file->getClientOriginalName();
            try {
                $extension = strtolower($file->getClientOriginalExtension());
                $time = time().'_'.Str::random(4);
                $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));
                $displayName = pathinfo($originalFilename, PATHINFO_FILENAME);

                $gallery = new ModelsGallery;
                $gallery->name = $displayName;
                $gallery->alt_text = $this->uploadAltText ?: $displayName;
                $gallery->description = null;
                $gallery->category = $this->uploadCategory;
                $gallery->status = $this->uploadStatus;

                // ====== KONVERSI GAMBAR KE WEBP ======
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $converted = $this->convertToWebp($file, $slugName, $time);

                    if ($converted) {
                        $gallery->image_path = $converted['path'];
                        $gallery->cover_path = $converted['path'];
                        $gallery->file_size = $converted['size'];
                        $gallery->mime_type = 'image';
                        $webpConvertedCount++;
                    } else {
                        // Fallback: simpan file asli jika konversi gagal
                        $filename = "{$slugName}_{$time}.{$extension}";
                        $path = $file->storeAs('galleries', $filename, 'public');
                        $gallery->image_path = $path;
                        $gallery->cover_path = $path;
                        $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                        $gallery->mime_type = 'image';
                    }
                } elseif ($extension === 'svg') {
                    // SVG tidak dikonversi (vector-based)
                    $filename = "{$slugName}_{$time}.svg";
                    $path = $file->storeAs('galleries', $filename, 'public');
                    $gallery->image_path = $path;
                    $gallery->cover_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'image';
                } elseif ($extension === 'pdf') {
                    // PDF: simpan asli + generate cover (cover dikonversi ke webp)
                    $filename = "{$slugName}_{$time}.pdf";
                    $path = $file->storeAs('galleries', $filename, 'public');

                    $gallery->image_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'pdf';

                    $cover = $this->generatePdfCover($path, $originalFilename, $time);
                    $gallery->cover_path = $cover;
                    if (! $cover) {
                        $pdfCoverWarnings[] = $displayName;
                    }
                }

                $gallery->save();
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Multiple Upload Error: '.$e->getMessage(), [
                    'file' => $originalFilename ?? 'unknown',
                ]);
                $failedFiles[] = $originalFilename ?? 'unknown';
            }
        }

        $this->clearUploadQueue();

        // Notifikasi hasil
        if ($successCount > 0) {
            $msg = "{$successCount} file berhasil diunggah.";
            if ($webpConvertedCount > 0) {
                $msg .= " ({$webpConvertedCount} dikonversi ke WebP)";
            }
            $this->dispatch('notify', type: 'success', message: $msg);
        }

        if (! empty($failedFiles)) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Gagal mengunggah: '.implode(', ', $failedFiles)
            );
        }

        if (! empty($pdfCoverWarnings)) {
            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'Cover PDF gagal dibuat untuk: '.implode(', ', $pdfCoverWarnings)
            );
        }
    }

    public function saveGallery()
    {
        $this->validate();

        try {
            $gallery = new ModelsGallery;
            $gallery->name = $this->filename;
            $gallery->description = $this->filedescription;
            $gallery->category = $this->filecategory;
            $gallery->status = $this->filestatus;
            $gallery->alt_text = $this->filealt_text ?: $this->filename;

            if ($this->fileimage_path) {
                $file = $this->fileimage_path;
                $originalFilename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $time = time();
                $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));

                // ====== KONVERSI GAMBAR KE WEBP ======
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $converted = $this->convertToWebp($file, $slugName, $time);

                    if ($converted) {
                        $gallery->image_path = $converted['path'];
                        $gallery->cover_path = $converted['path'];
                        $gallery->file_size = $converted['size'];
                        $gallery->mime_type = 'image';
                    } else {
                        // Fallback: simpan asli
                        $filename = "{$slugName}_{$time}.{$extension}";
                        $path = $file->storeAs('galleries', $filename, 'public');
                        $gallery->image_path = $path;
                        $gallery->cover_path = $path;
                        $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                        $gallery->mime_type = 'image';
                    }
                } elseif ($extension === 'svg') {
                    $filename = "{$slugName}_{$time}.svg";
                    $path = $file->storeAs('galleries', $filename, 'public');
                    $gallery->image_path = $path;
                    $gallery->cover_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'image';
                } elseif ($extension === 'pdf') {
                    $filename = "{$slugName}_{$time}.pdf";
                    $path = $file->storeAs('galleries', $filename, 'public');

                    $gallery->image_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'pdf';

                    $generatedCover = $this->generatePdfCover($path, $originalFilename, $time);
                    $gallery->cover_path = $generatedCover;
                }
            }

            $gallery->save();

            $this->resetFields();
            $this->dispatch('notify', type: 'success', message: 'Gallery created successfully.');
        } catch (\Exception $e) {
            Log::error('Save Gallery Error: '.$e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to save gallery: '.$e->getMessage());
        }
    }

    public function editGallery($id)
    {
        $gallery = ModelsGallery::findOrFail($id);

        $this->selected_id = $gallery->id;
        $this->filename = $gallery->name;
        $this->filealt_text = $gallery->alt_text;
        $this->filedescription = $gallery->description;
        $this->filecategory = $gallery->category;
        $this->filestatus = $gallery->status;
        $this->existing_file_path = $gallery->image_path;
        $this->cover_path = $gallery->cover_path;
        $this->mime_type = $gallery->mime_type;
        $this->isEdit = true;
    }

    public function updateGallery()
    {
        $this->validate();

        try {
            $gallery = ModelsGallery::findOrFail($this->selected_id);
            $gallery->name = $this->filename;
            $gallery->description = $this->filedescription;
            $gallery->category = $this->filecategory;
            $gallery->status = $this->filestatus;
            $gallery->alt_text = $this->filealt_text ?: $this->filename;

            if ($this->fileimage_path) {
                // Hapus file lama
                if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
                if (
                    $gallery->cover_path && $gallery->cover_path !== $gallery->image_path
                    && Storage::disk('public')->exists($gallery->cover_path)
                ) {
                    Storage::disk('public')->delete($gallery->cover_path);
                }

                $file = $this->fileimage_path;
                $originalFilename = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $time = time();
                $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));

                // ====== KONVERSI GAMBAR KE WEBP ======
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $converted = $this->convertToWebp($file, $slugName, $time);

                    if ($converted) {
                        $gallery->image_path = $converted['path'];
                        $gallery->cover_path = $converted['path'];
                        $gallery->file_size = $converted['size'];
                        $gallery->mime_type = 'image';
                    } else {
                        $filename = "{$slugName}_{$time}.{$extension}";
                        $path = $file->storeAs('galleries', $filename, 'public');
                        $gallery->image_path = $path;
                        $gallery->cover_path = $path;
                        $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                        $gallery->mime_type = 'image';
                    }
                } elseif ($extension === 'svg') {
                    $filename = "{$slugName}_{$time}.svg";
                    $path = $file->storeAs('galleries', $filename, 'public');
                    $gallery->image_path = $path;
                    $gallery->cover_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'image';
                } elseif ($extension === 'pdf') {
                    $filename = "{$slugName}_{$time}.pdf";
                    $path = $file->storeAs('galleries', $filename, 'public');

                    $gallery->image_path = $path;
                    $gallery->file_size = round($file->getSize() / 1024, 2).' KB';
                    $gallery->mime_type = 'pdf';
                    $gallery->cover_path = $this->generatePdfCover($path, $originalFilename, $time);
                }
            }

            $gallery->save();

            $this->resetFields();
            $this->dispatch('notify', type: 'success', message: 'Gallery updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Gallery Error: '.$e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update gallery: '.$e->getMessage());
        }
    }

    public function regenerateCover($id = null)
    {
        $galleryId = $id ?? $this->selected_id;
        if (! $galleryId) {
            return;
        }

        $gallery = ModelsGallery::findOrFail($galleryId);

        if ($gallery->mime_type !== 'pdf' || ! $gallery->image_path) {
            $this->dispatch('notify', type: 'warning', message: 'File is not a PDF.');

            return;
        }

        $time = time();
        $originalFilename = basename($gallery->image_path);

        if ($gallery->cover_path && Storage::disk('public')->exists($gallery->cover_path)) {
            Storage::disk('public')->delete($gallery->cover_path);
        }

        $newCoverPath = $this->generatePdfCover($gallery->image_path, $originalFilename, $time);

        if ($newCoverPath) {
            $gallery->cover_path = $newCoverPath;
            $gallery->save();
            $this->cover_path = $newCoverPath;
            $this->dispatch('notify', type: 'success', message: 'PDF cover re-generated successfully.');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Failed to re-generate PDF cover.');
        }
    }

    public function deleteGallery($id)
    {
        try {
            $gallery = ModelsGallery::findOrFail($id);

            if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            if (
                $gallery->cover_path && $gallery->cover_path !== $gallery->image_path
                && Storage::disk('public')->exists($gallery->cover_path)
            ) {
                Storage::disk('public')->delete($gallery->cover_path);
            }

            $gallery->delete();
            $this->dispatch('notify', type: 'success', message: 'Gallery deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete gallery.');
        }
    }

    private function generatePdfCover(string $pdfPath, string $originalFilename, $time): ?string
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            $slugName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));

            // Step 1: Generate temporary JPG cover dari PDF
            $tempJpgRelative = "galleries/covers/{$slugName}_{$time}_cover.jpg";
            $tempJpgFull = Storage::disk('public')->path($tempJpgRelative);

            if (! file_exists(dirname($tempJpgFull))) {
                mkdir(dirname($tempJpgFull), 0755, true);
            }

            (new Pdf($pdfFullPath))
                ->selectPage(1)
                ->format(OutputFormat::Jpg)
                ->save($tempJpgFull);

            // Step 2: Konversi JPG cover ke WebP
            if (function_exists('imagewebp') && file_exists($tempJpgFull)) {
                $image = @imagecreatefromjpeg($tempJpgFull);
                if ($image) {
                    $webpRelative = "galleries/covers/{$slugName}_{$time}_cover.webp";
                    $webpFull = Storage::disk('public')->path($webpRelative);

                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);

                    $success = imagewebp($image, $webpFull, 85);
                    imagedestroy($image);

                    if ($success) {
                        // Hapus file JPG sementara
                        @unlink($tempJpgFull);
                        $this->coverPreview = Storage::url($webpRelative);

                        return $webpRelative;
                    }
                }
            }

            // Fallback: pakai JPG jika konversi WebP gagal
            $this->coverPreview = Storage::url($tempJpgRelative);

            return $tempJpgRelative;
        } catch (\Exception $e) {
            Log::warning('PDF cover image generation failed', [
                'error' => $e->getMessage(),
                'pdf_path' => $pdfPath,
            ]);

            $this->dispatch('notify', type: 'warning', message: 'PDF uploaded, but cover image generation failed.');

            return null;
        }
    }

    private function convertToWebp($file, string $slugName, string $time): ?array
    {
        try {
            $sourcePath = $file->getRealPath();

            // Cek apakah ekstensi GD/Imagick tersedia
            if (! function_exists('imagewebp')) {
                Log::warning('GD library dengan WebP support tidak tersedia.');

                return null;
            }

            // Buat image resource dari file sumber
            $image = $this->createImageResource($sourcePath, $file->getClientOriginalExtension());

            if (! $image) {
                Log::warning('Gagal membuat image resource untuk WebP conversion.', [
                    'file' => $file->getClientOriginalName(),
                ]);

                return null;
            }

            // Handle transparansi (PNG/GIF/WebP dengan alpha)
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // Tentukan kualitas WebP (0-100, 82 adalah sweet spot untuk kualitas vs ukuran)
            $quality = 82;

            // Tentukan path output
            $webpFilename = "{$slugName}_{$time}.webp";
            $webpRelativePath = "galleries/{$webpFilename}";
            $webpFullPath = Storage::disk('public')->path($webpRelativePath);

            // Pastikan direktori ada
            if (! file_exists(dirname($webpFullPath))) {
                mkdir(dirname($webpFullPath), 0755, true);
            }

            // Konversi dan simpan sebagai WebP
            $success = imagewebp($image, $webpFullPath, $quality);
            imagedestroy($image);

            if (! $success || ! file_exists($webpFullPath)) {
                Log::warning('imagewebp() gagal menyimpan file.', [
                    'file' => $file->getClientOriginalName(),
                ]);

                return null;
            }

            // Hitung ukuran file hasil konversi
            $fileSize = filesize($webpFullPath);
            $fileSizeFormatted = $fileSize >= 1024 * 1024
                ? round($fileSize / 1024 / 1024, 2).' MB'
                : round($fileSize / 1024, 2).' KB';

            return [
                'path' => $webpRelativePath,
                'size' => $fileSizeFormatted,
            ];
        } catch (\Exception $e) {
            Log::error('WebP Conversion Error: '.$e->getMessage(), [
                'file' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

    /**
     * Buat image resource dari berbagai format sumber.
     */
    private function createImageResource(string $path, string $extension)
    {
        $extension = strtolower($extension);

        return match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'gif' => @imagecreatefromgif($path),
            'webp' => @imagecreatefromwebp($path),
            default => null,
        };
    }

    public function render()
    {
        return view('suryacms::livewire.admin.gallery', [
            'galleries' => ModelsGallery::latest()->get(),
            'isEdit' => $this->isEdit,
        ])->layout('suryacms::layouts.app');
    }
}
