<?php

namespace App\Parser;

class Token
{
    public function __construct(
        public readonly string $type,
        public readonly string $value,
        public readonly int $position
    ) {
    }
}