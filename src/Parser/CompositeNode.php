<?php

namespace App\Parser;

class CompositeNode extends Node
{
    public function __construct(
        public Node $left,
        public Node $right,
        public Token $token
    ) {

        if (!$this->token->type === 'operator') {
            throw new \InvalidArgumentException('Token must be an operator');
        }
    }

    public function __toString(): string
    {
        // calls toString() recurcively
        return $this->left . ' ' . $this->token->value . ' ' . $this->right;
    }

    public function evaluate(): float {
        return match($this->token->value) {
            '+' => $this->left->evaluate() + $this->right->evaluate(),
            '-' => $this->left->evaluate() - $this->right->evaluate(),
            '*' => $this->left->evaluate() * $this->right->evaluate(),
            '/' => $this->left->evaluate() / $this->right->evaluate(),
            default => throw new \InvalidArgumentException('Invalid operator'),
        };
    }
}