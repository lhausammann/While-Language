<?php

use App\Parser\CompositeNode;
use App\Parser\Node;
use App\Parser\Token;

class NodeTest extends PHPUnit\Framework\TestCase
{
    public function testNodeToString()
    {
        $token = new Token('number', '1', 0);
        $node = new Node($token);
        $this->assertEquals('1', (string) $node);
    }

    public function testOperatorToString()
    {
        $left = new Node(new Token('number', '1', 0));
        $right = new Node(new Token('number', '2', 0));
        $token = new Token('operator', '+', 0);
        $node = new CompositeNode($left, $right, $token);
        $this->assertEquals('1 + 2', (string) $node);
    }
}
