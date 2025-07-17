<?php

namespace App\Services;

use App\Enums\FileSystemDiskEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageManagementService
{
    protected function publicUploadsPath($folder = '')
    {
        return '../public_html/' . $folder;
    }

    public function uploadImage(UploadedFile $file, array $options = [])
    {
        $currentImagePath = $options['currentImagePath'] ?? null;
        $disk = $options['disk'] ?? FileSystemDiskEnum::PUBLIC->value;
        $folder = $options['folder'] ?? null;

        if ($disk === FileSystemDiskEnum::PUBLIC->value) {
            if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }

            if (!$currentImagePath) {
                $imagePath = null;
                $this->destroyImage($imagePath);
            }

            $imagePath = $file->store($folder, 'public');
            return $imagePath;

        } elseif ($disk === FileSystemDiskEnum::IDCLOUDHOST->value) {
            $directory = $this->publicUploadsPath($folder);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $fileName = time() . '.' . $file->extension();

            if ($currentImagePath && File::exists($this->publicUploadsPath($currentImagePath))) {
                File::delete($this->publicUploadsPath($currentImagePath));
            }

            $file->move($directory, $fileName);
            return $folder . '/' . $fileName;
        }

        return null;
    }

    public function destroyImage($currentImagePath, $disk = FileSystemDiskEnum::PUBLIC->value)
    {
        if ($disk === FileSystemDiskEnum::PUBLIC->value) {
            if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
                return true;
            }
        } elseif ($disk === FileSystemDiskEnum::IDCLOUDHOST->value) {
            if ($currentImagePath && File::exists($this->publicUploadsPath($currentImagePath))) {
                File::delete($this->publicUploadsPath($currentImagePath));
                return true;
            }
        }

        return false;
    }
}