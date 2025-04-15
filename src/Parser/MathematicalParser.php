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
    // operatorExpression [operator operatorExpression]*  (left-associative)
    // parenthisedExpression operator expression'
    // parenthisedExpression:='(' expression ')'
    // number operator expression
    private function expression(): Node
    {
        $lookahead = $this->lookahead();
        // starts with opening bracket - match whole expression
        // e.g. (7+2)
        if ($lookahead && $lookahead->value === '(') {
            return $this->matchParenthisedExpression();
        }
        // starts with a leaf node (number) - match following operator (list) with precedence:
        // e.g. 7 + 3 - 2
        if ($lookahead && $lookahead->type === 'number') {
            return $this->matchOperatorExpression();
        }

        // if we reach here, we have an invalid expression
        throw new \RuntimeException("Invalid expression at position {$lookahead->position}");
    }

    private function matchParenthisedExpression(): Node
    {
        $this->match('BRACKET', '(');
        $expression = $this->expression();
        $this->match('BRACKET', ')');

        return $expression;
    }

    private function matchOperatorExpression(): Node
    {
        // 7 + 3 [ / 2 - 5 +....]
        //$left = $operator = new Node($this->match('number'));
        $left = $operator = $this->matchOperatorDivideMultipliy();
        while ($token = $this->tryMatch('operator', ['+', '-'])) {
            // an operator mus be followed by a number
            $right = $this->matchOperatorDivideMultipliy();
            $operator = new CompositeNode($left, $right, $token);
            $left = $operator;
        }

        return $operator;
    }

    private function matchOperatorDivideMultipliy(): Node
    {
        // 7 * 3
        $left = $operator = new Node($this->match('number'));
        while ($token = $this->tryMatch('operator', ['*', '/'])) {
            // an operator mus be followed by a number
            $right = new Node($this->match('number'));
            $operator = new CompositeNode($left, $right, $token);
            $left = $operator;
        }

        return $operator;
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

    public function tryMatch(string $type, int|string|float|bool|array $value = null): ?Token
    {
        $token = $this->tokenizer->lookahead();
        if ($token->type !== $type) {
            return null;
        }

        // check using in_array
        if (is_array($value)) {
            if (!in_array($token->value, $value)) {
                return null;
            }
        } elseif ($value !== null && $token->value !== $value) {
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