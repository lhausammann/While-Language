<?php

use App\Parser\CompositeNode;
use App\Parser\MathematicalParser;
use App\Parser\Tokenizer;

class MathematicalParserTest extends PHPUnit\Framework\TestCase
{
    public function testParseSimpleExpression()
    {
        $expr = '1 + 2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        $this->assertInstanceOf(CompositeNode::class, $result);
        $this->assertEquals('1 + 2', (string) $result);
    }

    public function testEvaluateSimpleExpression()
    {
        $expr = '1 + 2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        $this->assertEquals(3, $result->evaluate([]));
    }

    public function testPlusMinusExpression()
    {
        // $this->markTestSkipped();
        $expr = '5 + 3 - 10 -20 + 3';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        $this->assertEquals(-19, $result->evaluate([]));
    }

    public function checkHandleParentheses()
    {
        $expr = '(1 + 2) * 3';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        $this->assertEquals(9, $result->evaluate([]));
    }

    public function testOperatorPrecedence()
    {
        $expr = '2 + 2 * 3';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        // Assuming the CompositeNode handles operator precedence correctly
        $this->assertEquals(8, $result->evaluate([]));

        $expr = '2 * 3 * 4';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        // Assuming the CompositeNode handles operator precedence correctly
        $this->assertEquals(24, $result->evaluate([]));
    }

    public function testUnaryMinus()
    {
        $expr = '-5 - 3';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        $this->assertEquals(-8, $result->evaluate([]));

        $expr = '5--3';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        // Assuming the CompositeNode handles unary minus correctly
        $this->assertEquals(8, $result->evaluate([]));

        $expr = '--5 - -3'; // 5+3
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();
        // Assuming the CompositeNode handles unary minus correctly
        $this->assertEquals(8, $result->evaluate([]));
    }

    public function testContext()
    {
        $expr = 'x + 2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        // Assuming the CompositeNode handles context correctly
        $context = ['x' => 3];
        $this->assertEquals(5, $result->evaluate($context));
    }

    public function testAnd()
    {
        $expr = 'true AND false';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        // Assuming the CompositeNode handles logical AND correctly
        $this->assertEquals(false, $result->evaluate(['true' => true, 'false' => false]));

        $expr = '2*2 AND 2*2';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        // Assuming the CompositeNode handles logical AND correctly
        $this->assertEquals(true, $result->evaluate(['true' => true, 'false' => false]));
    }

    public function testOr()
    {
        $expr = 'true OR false';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        // Assuming the CompositeNode handles logical AND correctly
        $this->assertEquals(true, $result->evaluate(['true' => true, 'false' => false]));

        $expr = 'false AND false OR true AND true';
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $result = $parser->parse();

        // Assuming the CompositeNode handles logical AND correctly
        $this->assertEquals(true, $result->evaluate(['true' => true, 'false' => false]));
    }
}
