<?php
declare(strict_types=1);

interface FileWriterInterface
{
    public function write(string $content);
}