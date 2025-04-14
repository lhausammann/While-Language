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
        $token = $this->tokenizer->next();
        if ($token->type !== 'END') {
            throw new \RuntimeException("Unexpected token: {$token->value} at position {$token->position}");
        }
        return $result;
    }

    // expression:
    // multiplyDivideExpr '+|-' multiplyDivideExpr
    // multiplyDivideExpr: 'expression' ('*'|'/' expression)*


    private function expression(): Node
    {
        // 7 + 3 [-2 +....]
        $left = $operator = new Node($this->match('number'));
        while ($token = $this->tryMatch('operator')) {
            // an operator mus be followed by a number
            $right = new Node($this->match('number'));
            $operator = new CompositeNode($left, $right, $token);
            $left = $operator;
        }
        $this->matchEnd();
        return $operator;
    }

    public function match(string $type): Token
    {
        $token = $this->tokenizer->next();
        if ($token->type !== $type) {
            throw new \RuntimeException("Expected token of type $type, got {$token->type} at position {$token->position}");
        }
        return $token;
    }

    public function matchEnd(): Token // of type END
    {
        $token = $this->tokenizer->next();
        if ($token->type !== 'END') {
            throw new \RuntimeException("Expected end of expression, got {$token->type} at position {$token->position}");
        }

        return $token;
    }

    public function tryMatch(string $type): ?Token
    {
        $token = $this->tokenizer->next();
        if ($token->type !== $type) {
            return null;
        }
        return $token;
    }
}