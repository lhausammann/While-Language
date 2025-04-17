<?php

namespace App\Parser;


readonly class MathematicalParser
{
    public function __construct(private Tokenizer $tokenizer)
    {
    }

    public function parse($checkEnd = true): Node
    {
        $result = $this->expression();
        if ($checkEnd) {
            $this->matchEnd();
        }
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
       $left = $this->matchLogicalExpression();
       // do not parse recursively (left-associative)
       while($lookahead = $this->tryMatch('operator', ['AND', 'OR'])) {
            // an operator must be followed by an expression
            $right = $this->matchLogicalExpression();
            $left = new CompositeNode($left, $right, $lookahead);
        }
        return $left;
    }

    private function matchLogicalExpression(): Node
    {
        $left = $this->matchComparisonExpression();
        // do not parse recursively (left-associative)
        while($lookahead = $this->tryMatch('operator', ['AND', 'OR'])) {
            // an operator must be followed by an expression
            $right = $this->matchComparisonExpression();
            $left = new CompositeNode($left, $right, $lookahead);
        }
        return $left;
    }

    private function matchComparisonExpression(): Node
    {
        $left = $this->matchPlusMinusExpression();
        // do not parse recursively (left-associative)
        while($lookahead = $this->tryMatch('operator', ['=', '~', '<', '>', '>'])) {
            // an operator must be followed by an expression
            $right = $this->matchPlusMinusExpression();
            $left = new CompositeNode($left, $right, $lookahead);
        }
        return $left;
    }

    private function matchPlusMinusExpression(): Node {
        $left = $this->matchOperatorDivideMultipliy();
        // do not parse recursively (left-associative)
        while($lookahead = $this->tryMatch('operator', ['+', '-'])) {
            // an operator must be followed by an expression
            $right = $this->matchOperatorDivideMultipliy();
            $left = new CompositeNode($left, $right, $lookahead);
        }

        return $left;
    }

    private function matchParenthisedExpression(): Node
    {
        $this->match('BRACKET', '(');
        $expression = $this->expression();
        $this->match('BRACKET', ')');

        return $expression;
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
        // at this position (matchPrimary) only unary "-x" is allowed
        // refactor to "0-x"
        if ($unaryMinus = $this->tryUnaryMinus()) {
            return $unaryMinus;
        }

        if ($token = $this->tryMatch('IDENTIFIER')) {
            return new Node($token);
        }

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

    // Add unary minus as a special case
    private function tryUnaryMinus() {
        $token = $this->tokenizer->lookahead();
        if ($token->type === 'operator' && $token->value === '-') {
            $this->tokenizer->next();
            return new CompositeNode(
                new Node(new Token('number', '0', $token->position)),
                $this->matchPrimaryExpression(),
                $token
            );
        }
        return null;
    }
}