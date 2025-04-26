<?php

namespace App\Parser;

class Node
{
    public function __construct(
        public Token $token,
    ) {
    }

    public function __toString(): string
    {
        return $this->token->value;
    }

    public function evaluate(array $context): float|string|int
    {
        if ('STRING' === $this->token->type) {
            // interpolate values
            /*
            if (preg_match_all('/#\{(\w+)\}/', $this->token->value, $matches)) {
                foreach ($matches[1] as $var) {
                    if (!isset($context[$var])) {
                        throw new \RuntimeException("Undefined variable: {$var}");
                    }

                    return str_replace('{'.$var.'}', $context[$var], $this->token->value);
                }
            }*/

            return $this->token->value;
        }
        // look up a value from a variable
        if ('IDENTIFIER' === $this->token->type) {
            if ('RANDOM' === $this->token->value) {
                return rand(0, 100);
            }
            if (!isset($context[$this->token->value])) {
                throw new \RuntimeException("Undefined variable: {$this->token->value}");
            }

            return $context[$this->token->value];
        }

        // This method should be implemented in subclasses
        return $this->token->value;
    }
}
