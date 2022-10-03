<?php
declare(strict_types=1);

interface FileReaderInterface
{
    public function readLine(?int $bufferSize): ?string;

    public function reset();
}