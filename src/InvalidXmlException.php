<?php
declare(strict_types=1);

class InvalidXmlException extends Exception
{
    private ?int $fileLine;
    private ?int $fileColumn;

    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null,
        ?int $xmlLine = 0,
        ?int $xmlColumn = 0
    ) {
        parent::__construct($message, $code, $previous);
        $this->fileLine = $xmlLine;
        $this->fileColumn = $xmlColumn;
    }

    public function getFileLine(): ?int
    {
        return $this->fileLine;
    }

    public function getFileColumn(): ?int
    {
        return $this->fileColumn;
    }
}