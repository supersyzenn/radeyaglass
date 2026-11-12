<?php

namespace Tests\Support;

use Illuminate\Http\UploadedFile;

trait Photo
{
    private array $temporaryPhotos = [];

    private function photo(string $name = 'produk.png'): UploadedFile
    {
        return $this->upload($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII='));
    }

    private function upload(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'radeya-photo-');
        file_put_contents($path, $content);
        $this->temporaryPhotos[] = $path;
        return new UploadedFile($path, $name, null, null, true);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryPhotos as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        parent::tearDown();
    }
}
