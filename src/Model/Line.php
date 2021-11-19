<?php

namespace App\Model;

class Line
{
    private function __construct(private int $lineNumber, private string $content)
    {
    }

    public static function fromLineIndexAndContent(int $lineIndex, string $content): self
    {
        return new self($lineIndex + 1, $content);
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}