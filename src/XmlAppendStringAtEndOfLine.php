<?php

class XmlAppendStringAtEndOfLine implements FixerInterface
{

    private int $column;
    private string $append;

    public function __construct(int $column, string $append)
    {
        $this->column = $column;
        $this->append = $append;
    }

    public function apply(string $string): string
    {
        return $string . $this->append;
    }
}