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
            if ($char === '(' || $char === ')') {
                $this->position++;
                return new Token('BRACKET', $char, $this->position);
            }

            if (ctype_space($char)) {
                $this->position++;
                continue;
            }
            if (is_numeric($char)) {
                return $this->number();
            }
            if ($char === '+' || $char === '-' || $char === '*' || $char === '/' || $char === '<' || $char === '>' || $char === '=' || $char === '~') {
                return $this->operator();
            }

            // parse identifier or named operator
            if (ctype_alpha($char)) {
                $start = $this->position;
                while ($this->position < strlen($this->expression) && ctype_alnum($this->expression[$this->position])) {
                    $this->position++;
                }
                $name = substr($this->expression, $start, $this->position - $start);
                $type = $name === 'AND' || $name === 'OR' ? 'operator' : 'IDENTIFIER';
                return new Token($type, $name, $start);
            }

            throw new \RuntimeException("Unexpected character: $char at position $this->position");

        }

        return new Token('END', 'END', $this->position);
    }

    public function lookahead(): Token {
        $pos = $this->position;
        $token = $this->next();
        $this->position = $pos;
        return $token;
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