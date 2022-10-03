<?php
declare(strict_types=1);

class FileReader implements FileReaderInterface
{
    private string $filename;
    private $resource;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->resource = fopen($this->filename, 'r');
    }

    public function __destruct()
    {
        fclose($this->resource);
    }

    public function readLine(?int $bufferSize = null): ?string
    {
        return fgets($this->resource, $bufferSize) ?: null;
    }

    public function reset()
    {
        fseek($this->resource, 0);
    }
}