<?php

namespace App\Wrapper;

use PhpParser\Lexer;

class LineAndColumnLexerWrapper
{
    public function __construct(private Lexer $lexer)
    {
    }

    public function getLexer(): Lexer
    {
        return $this->lexer;
    }
}