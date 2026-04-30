<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Service untuk manajemen asset gambar menggunakan Intervention Image v3.
 *
 * Semua file disimpan via Storage facade (disk 'public') sehingga
 * mudah dipindah dari local ke S3 di masa depan — cukup ubah FILESYSTEM_DISK.
 *
 * File binary TIDAK PERNAH disimpan di database. Yang disimpan di DB
 * hanya path relatif (JSON untuk multi-variant).
 */
class ImageService
{
    private ImageManager $manager;
    private string $disk;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->disk    = 'public';
    }

    /**
     * Upload dan resize gambar produk menjadi 3 variant:
     *
     *  - thumbnail : 300×300 WebP (untuk card catalog)
     *  - medium    : 600×600 WebP (untuk detail produk)
     *  - original  : 1200×1200 JPG (untuk zoom/fullscreen)
     *
     * @param  UploadedFile  $file  File gambar yang diupload
     * @return array{thumbnail: string, medium: string, original: string}  Path relatif dari storage
     */
    public function uploadProductImage(UploadedFile $file): array
    {
        $baseName  = Str::uuid();
        $directory = 'products/' . now()->format('Y/m');

        $image = $this->manager->read($file->getPathname());

        // ── Thumbnail: 300×300 WebP ─────────────────────────────────────
        $thumbnailPath = "{$directory}/{$baseName}_thumb.webp";
        $thumbnail = clone $image;
        $thumbnail->coverDown(300, 300);
        Storage::disk($this->disk)->put(
            $thumbnailPath,
            $thumbnail->toWebp(quality: 80)->toString()
        );

        // ── Medium: 600×600 WebP ────────────────────────────────────────
        $mediumPath = "{$directory}/{$baseName}_medium.webp";
        $medium = clone $image;
        $medium->coverDown(600, 600);
        Storage::disk($this->disk)->put(
            $mediumPath,
            $medium->toWebp(quality: 85)->toString()
        );

        // ── Original: 1200×1200 JPG ─────────────────────────────────────
        $originalPath = "{$directory}/{$baseName}_original.jpg";
        $original = clone $image;
        $original->scaleDown(1200, 1200);
        Storage::disk($this->disk)->put(
            $originalPath,
            $original->toJpeg(quality: 90)->toString()
        );

        return [
            'thumbnail' => $thumbnailPath,
            'medium'    => $mediumPath,
            'original'  => $originalPath,
        ];
    }

    /**
     * Upload dan resize foto bukti serah terima pesanan.
     *
     * Hasil: maks 1920×1080 JPG.
     *
     * @param  UploadedFile  $file  File foto yang diupload
     * @return string  Path relatif dari storage
     */
    public function uploadHandoverPhoto(UploadedFile $file): string
    {
        $baseName  = Str::uuid();
        $directory = 'handovers/' . now()->format('Y/m');
        $path      = "{$directory}/{$baseName}.jpg";

        $image = $this->manager->read($file->getPathname());
        $image->scaleDown(1920, 1080);

        Storage::disk($this->disk)->put(
            $path,
            $image->toJpeg(quality: 85)->toString()
        );

        return $path;
    }

    /**
     * Hapus gambar produk (semua variant) dari storage.
     *
     * @param  array|string|null  $imageData  Path atau JSON array dari path
     */
    public function deleteProductImages(array|string|null $imageData): void
    {
        if (empty($imageData)) {
            return;
        }

        // Jika string JSON, decode dulu
        if (is_string($imageData)) {
            $imageData = json_decode($imageData, true) ?? [];
        }

        foreach (['thumbnail', 'medium', 'original'] as $variant) {
            if (! empty($imageData[$variant])) {
                Storage::disk($this->disk)->delete($imageData[$variant]);
            }
        }
    }

    /**
     * Hapus satu file dari storage.
     *
     * @param  string|null  $path  Path relatif
     */
    public function deleteFile(?string $path): void
    {
        if (! empty($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /**
     * Dapatkan URL publik dari path storage.
     *
     * @param  string|null  $path  Path relatif dari storage
     * @return string|null  Full URL
     */
    public function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }
}
