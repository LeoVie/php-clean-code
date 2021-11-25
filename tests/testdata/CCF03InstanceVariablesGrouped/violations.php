<?php

class Foo
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    private int $age = 10;

    public function foo(): int
    {
        return 1;
    }

    private string $bla = 'abc';
}