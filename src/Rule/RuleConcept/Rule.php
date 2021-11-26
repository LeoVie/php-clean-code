<?php

namespace App\Rule\RuleConcept;

interface Rule
{
    public const CLASS_NODE_AWARE = 'ClassNodeAware';
    public const FILE_CODE_AWARE = 'FileCodeAware';
    public const TOKEN_SEQUENCE_AWARE = 'TokenSequenceAware';
    public const NAME_NODE_AWARE = 'NameNodeAware';
    public const LINES_AWARE = 'LinesAware';

    public function getName(): string;
}