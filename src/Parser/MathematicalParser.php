<?php

namespace App\Parser;


class MathematicalParser
{
    public function __construct(private readonly Tokenizer $tokenizer)
    {
    }

    public function parse(): Node
    {
        $result = $this->expression();
        $this->matchEnd();
        
        return $result;
    }

    // expression:=
    // '(' expression ') operator expression'
    // number operator expression
    private function expression(): Node
    {
        $lookahead = $this->lookahead();
        // starts with opening bracket - match whole expression
        // (7+2)
        if ($lookahead && $lookahead->value === '(') {
            $this->match('BRACKET', '(');
            $expr = $this->expression();
            $this->match('BRACKET', ')');
            // if the next token is a number, we can return the expression
            // otherwise we have to check if it is an operator
            $operator = new CompositeNode($expr, $this->expression(),$this->match('operator'));
            return $operator;
        }

        // 7 + 3 [-2 +....]
        // starts with a number (bracket is handled above). Could be follwoed by operator and another expression
        //if ($lookahead->type === 'number') {
        $left = new Node($this->match('number'));
        $lookahead = $this->lookahead();
        if ($lookahead && $lookahead->type === 'operator') {
            while ($token = $this->tryMatch('operator')) {
                // an operator mus be followed by a number
                $right = $this->expression();
                $operator = new CompositeNode($left, $right, $token);
                $left = $operator;
            }
        }

        return $left;
    }

    public function match(string $type, int|string|float $value = null): Token
    {
        $token = $this->tokenizer->next();
        if ($token->type !== $type) {
            throw new \RuntimeException("Expected token of type $type, got {$token->type} at position {$token->position}");
        }
        if ($value !== null && $token->value !== $value) {
            throw new \RuntimeException("Expected token with value $value, got {$token->value} at position {$token->position}");
        }

        return $token;
    }

    public function tryMatch(string $type): ?Token
    {
        $token = $this->tokenizer->lookahead();
        if ($token->type !== $type) {
            return null;
        }

        return $this->tokenizer->next();
    }

    public function matchEnd(): Token // of type END
    {
        $token = $this->tokenizer->next();
        if ($token->type !== 'END') {
            throw new \RuntimeException("Expected end of expression, got {$token->type} at position {$token->position}");
        }

        return $token;
    }

    public function lookahead(): ?Token
    {
        return $token = $this->tokenizer->lookahead();

    }
}