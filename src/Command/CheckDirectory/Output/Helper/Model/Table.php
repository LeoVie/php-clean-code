<?php

namespace App\Command\CheckDirectory\Output\Helper\Model;

class Table
{
    /** @var string[][] */
    private array $rows = [];

    /** @param string[] $header */
    private function __construct(
        /** @psalm-readonly */
        private array $header
    )
    {
    }

    /** @param string[] $header */
    public static function create(array $header): self
    {
        return new self($header);
    }

    /** @param string[] $row */
    public function addRow(array $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    /** @return string[][] */
    public function getRows(): array
    {
        return $this->rows;
    }

    /** @return string[] */
    public function getHeader(): array
    {
        return $this->header;
    }
}