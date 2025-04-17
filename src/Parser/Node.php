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

    public function evaluate(array $context): float|string|int
    {
        // look up a value from a variable
        if ($this->token->type === 'IDENTIFIER') {
            if (!isset($context[$this->token->value])) {
                throw new \RuntimeException("Undefined variable: {$this->token->value}");
            }
            return $context[$this->token->value];
        }

        // This method should be implemented in subclasses
        return $this->token->value;
    }


}