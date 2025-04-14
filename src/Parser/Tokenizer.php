<?php

namespace App\Parser;

class Tokenizer
{
    private int $position = 0;
    public function __construct(private readonly string $expression) {
    }

    public function next() : Token
    {
        while ($this->position < strlen($this->expression)) {
            $char = $this->expression[$this->position];
            if (ctype_space($char)) {
                $this->position++;
                continue;
            }
            if (is_numeric($char)) {
                return $this->number();
            }
            if ($char === '+' || $char === '-' || $char === '*' || $char === '/') {
                return $this->operator();
            }

            throw new \RuntimeException("Unexpected character: $char at position $this->position");

        }

        return new Token('END', 'END', $this->position);
    }

    public function number() : Token {
        $start = $this->position;
        while ($this->position < strlen($this->expression) && is_numeric($this->expression[$this->position])) {
            $this->position++;
        }
        $t = new Token('number', substr($this->expression, $start, $this->position - $start), $start);
        return $t;
    }

    public function operator() : Token {
        $char = $this->expression[$this->position];
        $this->position++;
        return new Token('operator', $char, $this->position);
    }
}