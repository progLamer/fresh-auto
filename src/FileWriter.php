<?php
declare(strict_types=1);

class FileWriter implements FileWriterInterface
{

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        file_put_contents($this->filename, '');
    }

    public function write(string $content)
    {
        file_put_contents($this->filename, $content, FILE_APPEND);
    }
}