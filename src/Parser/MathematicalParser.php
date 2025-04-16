<?php

namespace App\Parser;


readonly class MathematicalParser
{
    public function __construct(private Tokenizer $tokenizer)
    {
    }

    public function parse(): Node
    {
        $result = $this->expression();
        return $result;
    }

    // expression:=
    // operatorDivideMultiplyExpression +/- operatorMultiplyExpression (left-associative)

    // operatorDivideMultiplyExpression:=
    // primaryExpression (*/ operatorMultiplyExpression)*
    // primaryExpression:=
    // number | parenthisedExpression

    private function expression(): Node
    {
       $left = $this->matchOperatorDivideMultipliy();
       // do not parse recursively (left-associative)
       while($lookahead = $this->tryMatch('operator', ['+', '-'])) {
            // an operator must be followed by an expression
            $right = $this->matchOperatorDivideMultipliy();
            $left = new CompositeNode($left, $right, $lookahead);
        }
        return $left;


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


    // match first multiply/divide expression (returning composite or single node),
    // grouping *,/ together before parsing +/-
    private function matchOperatorExpression(): Node
    {
        // 7 + 3 [ / 2 - 5 * 10....]
        $left = $operator = $this->matchOperatorDivideMultipliy();
        while ($token = $this->tryMatch('operator', ['+', '-'])) {
            // an operator mus be followed by an expression
            //$right = $this->expression();
            $right = $this->matchPrimaryExpression(); // do NOT recurse
            $operator = new CompositeNode($left, $right, $token);
            $left = $operator;
        }

        return $operator;
    }

    private function matchOperatorDivideMultipliy(): Node
    {
        // 7 * 3, "7", 7*5
        $left = $operator = $this->matchPrimaryExpression();
        while ($token = $this->tryMatch('operator', ['*', '/'])) {
            // an operator mus be followed by a number
            $right = $this->matchPrimaryExpression(); // do NOT recurse

            $operator = new CompositeNode($left, $right, $token);
            $left = $operator; // if an operator follows, the current operator is the "left" input.
        }

        return $operator;
    }

    // match a "primary" expression, starting with '(' or a number.
    // if the token is a number, return it as a node
    // if the token is a '(', match the whole expression and return it
    // if the token is neither, throw an exception
    public function matchPrimaryExpression(): Node
    {
        $token = $this->tokenizer->lookahead();
        if ($token->type === 'number') {
            return new Node($this->match('number'));
        } elseif ($token->type === 'BRACKET' && $token->value === '(') {
            return $this->matchParenthisedExpression();
        }

        throw new \RuntimeException("Expected number or '(', got {$token->type} at position {$token->position}");
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
            throw new \RuntimeException("Expected end of expression, got {$token->type} at position {$token->position} with value: {$token->value}");
        }

        return $token;
    }

    public function lookahead(): ?Token
    {
        return $token = $this->tokenizer->lookahead();

    }
}