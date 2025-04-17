<?php

namespace App\Parser;

class CompositeNode extends Node
{
    public function __construct(
        public Node $left,
        public Node $right,
        public Token $token,
    ) {
        if ('operator' === !$this->token->type) {
            throw new \InvalidArgumentException('Token must be an operator');
        }
    }

    public function __toString(): string
    {
        // calls toString() recurcively
        return $this->left.' '.$this->token->value.' '.$this->right;
    }

    public function evaluate(array $context): float|int|string
    {
        return match ($this->token->value) {
            '+' => $this->left->evaluate($context) + $this->right->evaluate($context),
            '-' => $this->left->evaluate($context) - $this->right->evaluate($context),
            '*' => $this->left->evaluate($context) * $this->right->evaluate($context),
            '/' => $this->left->evaluate($context) / $this->right->evaluate($context),
            'AND' => $this->left->evaluate($context) && $this->right->evaluate($context),
            'OR' => $this->left->evaluate($context) || $this->right->evaluate($context),
            '=' => $this->left->evaluate($context) == $this->right->evaluate($context),
            '~' => $this->left->evaluate($context) != $this->right->evaluate($context),
            '<' => $this->left->evaluate($context) < $this->right->evaluate($context),
            '>' => $this->left->evaluate($context) > $this->right->evaluate($context),

            default => throw new \InvalidArgumentException('Invalid operator'),
        };
    }
}
