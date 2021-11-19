<?php

class Foo
{
    private string $name;
    private int $age = 10;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}