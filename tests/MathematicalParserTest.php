<?php

use App\Parser\CompositeNode;
use App\Parser\Tokenizer;
use App\Parser\MathematicalParser;

class MathematicalParserTest extends \PHPUnit\Framework\TestCase
{
    public function testParseSimpleExpression()
    {
        $expr = '1 + 2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        $this->assertInstanceOf(CompositeNode::class, $result);
        $this->assertEquals('1 + 2', (string)$result);
    }

    public function testEvaluateSimpleExpression()
    {
        $expr = '1 + 2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        $this->assertEquals(3, $result->evaluate());
    }
}