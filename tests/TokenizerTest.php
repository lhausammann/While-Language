<?php

use App\Parser\Tokenizer;

/**
 * Tokenizer handles the tokenization of mathematical expressions.
 */
class TokenizerTest extends PHPUnit\Framework\TestCase
{
    public function testNext()
    {
        $expr = '   1   +  1';
        $tokenizer = new Tokenizer($expr);
        $tokens = [];
        while ($token = $tokenizer->next()) {
            if ('END' === $token->type) {
                break;
            }
            $tokens[] = $token;
        }

        $this->assertCount(3, $tokens);
        $this->assertEquals('number', $tokens[0]->type);
        $this->assertEquals('1', $tokens[0]->value);
        $this->assertEquals(3, $tokens[0]->position);
        $this->assertEquals('operator', $tokens[1]->type);
        $this->assertEquals('+', $tokens[1]->value);
        $this->assertEquals(8, $tokens[1]->position);
        $this->assertEquals('number', $tokens[2]->type);
        $this->assertEquals('1', $tokens[2]->value);
        $this->assertEquals(10, $tokens[2]->position);
        $this->assertEquals('END', $token->type);
    }

    public function testBracket()
    {
        $expr = '(1 + 2) * 3';
        $tokenizer = new Tokenizer($expr);
        $tokens = [];
        while ($token = $tokenizer->next()) {
            if ('END' === $token->type) {
                break;
            }
            $tokens[] = $token;
        }

        $this->assertCount(7, $tokens);
        $this->assertEquals('BRACKET', $tokens[0]->type);
        $this->assertEquals('(', $tokens[0]->value);
        $this->assertEquals(1, $tokens[0]->position);
        $this->assertEquals('number', $tokens[1]->type);
        $this->assertEquals('1', $tokens[1]->value);
        $this->assertEquals(1, $tokens[1]->position);
        $this->assertEquals('operator', $tokens[2]->type);
        $this->assertEquals('+', $tokens[2]->value);
        $this->assertEquals(4, $tokens[2]->position);
        // usw.
    }
}
