<?php

declare(strict_types=1);

namespace App\Model\CodePosition;

use Safe\Exceptions\StringsException;

class CodePositionRange
{
    public static function create(CodePosition $start, CodePosition $end): self
    {
        return new self($start, $end);
    }

    private function __construct(private CodePosition $start, private CodePosition $end)
    {
    }

    public function getStart(): CodePosition
    {
        return $this->start;
    }

    public function getEnd(): CodePosition
    {
        return $this->end;
    }

    public function countOfLines(): int
    {
        return $this->getEnd()->getLine() - $this->getStart()->getLine();
    }

    /** @throws StringsException */
    public function toString(): string
    {
        return \Safe\sprintf(
            '%s - %s (%s lines)',
            $this->getStart()->toString(),
            $this->getEnd()->toString(),
            $this->countOfLines()
        );
    }
}