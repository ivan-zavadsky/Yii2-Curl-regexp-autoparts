<?php

namespace app\services;

class FileCacher implements ICacher
{
    private string $fileName = '../runtime/';
    private string $extension = '.txt';
    public function has(string $key): bool
    {
        return file_exists($this->getFullFileName($key));
    }

    public function get(string $key): string
    {
        return file_get_contents($this->getFullFileName($key));
    }

    public function set(string $key, string $value): void
    {
        file_put_contents($this->getFullFileName($key), $value);
    }

    private function getFullFileName(string $key): string
    {
        return $this->fileName . $key . $this->extension;
    }
}