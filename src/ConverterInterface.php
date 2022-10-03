<?php
declare(strict_types=1);

interface ConverterInterface
{
    /**
     * @throws InvalidXmlException
     * @param string $xmlString
     * @param bool|null $isEnd
     * @return string
     */
    public function convert(string $xmlString, ?bool $isEnd = false): string;

    public function addFix(int $line, FixerInterface $fixer);

    public function reset();

    public function canFix(InvalidXmlException $e): bool;
}