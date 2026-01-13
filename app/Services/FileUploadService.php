<?php

namespace App\Services;

class FileUploadService
{
    public function upload($file, string $folder): string
    {
        return $file->store($folder, 'public');
    }
}
