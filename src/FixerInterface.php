<?php

interface FixerInterface
{
    public function apply(string $string): string;
}