<?php

namespace App\SimpleLanguage;

use App\Parser\MathematicalParser;
use App\Parser\Node;
use App\Parser\Tokenizer;

class Parser
{
    private MathematicalParser $expressionParser;
    private Program $program;

    private $statementCount = 0;

    public function __construct(
        public readonly Tokenizer $tokenizer,
    ) {
        $this->expressionParser = new MathematicalParser($this->tokenizer);
        $this->program = new Program('Program', 0);
    }

    public function parse(): AbstractCommand
    {
        while ($token = $this->tokenizer->lookahead()) {
            if ('END' === $token->type) {
                break;
            }

            if ('IDENTIFIER' === $token->type) {
                $command = $this->parseCommand();
                $this->program->addStatement($command);
            } elseif ('SEMICOLON' === $token->type) {
                continue;
            } else {
                throw new \RuntimeException("Unexpected token: $token->value");
            }
        }
        $this->expressionParser->match('END');

        return $this->program;
    }

    public function parseCommand(): AbstractCommand
    {
        $command = $this->expressionParser->match('IDENTIFIER');
        ++$this->statementCount;

        return match ($command->value) {
            'WHILE' => $this->parseWhile(),
            'SET' => $this->parseSet(),
            'PRINT' => $this->parsePrint(),
            default => throw new \RuntimeException('Unknown command: '.print_r($command, true)),
        };
    }

    public function parseWhile(): WhileCommand
    {
        $expr = $this->expressionParser->parse(false);
        $this->expressionParser->match('SEMICOLON', ';');
        $body = $this->parseBlock();
        $while = new WhileCommand('while', $this->statementCount);
        $while->setExpression($expr);
        $while->setStatements($body);

        return $while;
    }

    public function parseSet(): SetCommand
    {
        $expr = null; // Input mode
        $name = $this->expressionParser->match('IDENTIFIER')->value;
        $this->expressionParser->match('operator', '=');
        if ($lookahead = $this->tokenizer->lookahead()) {
            if ('operator' === $lookahead->type && '<' === $lookahead->value) {
                $this->expressionParser->match('operator', '<');
                $this->expressionParser->match('SEMICOLON', ';');
            } else {
                $expr = $this->expressionParser->parse(false);
                $this->expressionParser->match('SEMICOLON', ';');
            }
        }

        $cmd = new SetCommand($name, $this->statementCount);
        if ($expr) {
            $cmd->setExpression($expr);
        }

        return $cmd;
    }

    public function parsePrint(): PrintCommand
    {
        $next = $this->tokenizer->lookahead();
        if ('STRING' === $next->type) {
            $this->expressionParser->match('STRING');
            $expr = new Node($next);
        } else {
            $expr = $this->expressionParser->parse(false);
        }
        $this->expressionParser->match('SEMICOLON', ';');
        $command = new PrintCommand('Print', $this->statementCount);
        $command->setExpression($expr);

        return $command;
    }

    public function parseBlock(): array
    {
        $block = [];
        while ($token = $this->expressionParser->lookahead()) {
            if ('IDENTIFIER' === $token->type && 'END' === $token->value) {
                $this->expressionParser->match('IDENTIFIER', 'END');
                $this->expressionParser->match('SEMICOLON', ';');
                break;
            }
            $statement = $this->parseCommand();
            $block[] = $statement;
        }

        return $block;
    }
}
