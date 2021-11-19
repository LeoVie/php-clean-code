<?php

class Foo
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    private int $age = 10;
}