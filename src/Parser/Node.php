<?php

namespace App\Parser;

class Node
{
    public function __construct(
        public Token $token
    ) {
    }

    public function __toString(): string
    {

        return $this->token->value;
    }

    public function evaluate(): float
    {
        // This method should be implemented in subclasses
        return $this->token->value;
    }


}