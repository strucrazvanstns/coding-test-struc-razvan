<?php
declare(strict_types=1);

namespace App\Service\File;

use Exception;

class FileManager
{
    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    protected function validateFile()
    {
        if (!file_exists($this->filePath)) {
            throw new Exception("File '{$this->filePath}' does not exist.");
        }
    }

    protected function openFile()
    {
        $this->validateFile();
        $handle = fopen($this->filePath, "r");
        if ($handle === false) {
            throw new Exception("Cannot open file '{$this->filePath}'.");
        }
        return $handle;
    }

    public function closeFile($handle)
    {
        if (is_resource($handle)) {
            fclose($handle);
        }
    }
}