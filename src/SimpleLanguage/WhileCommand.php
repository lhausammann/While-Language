<?php

namespace App\SimpleLanguage;

use App\Parser\Node;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WhileCommand extends ControlCommand
{
    public function execute(array &$context, OutputInterface $out, InputInterface $in): void
    {
        while ($this->expression->evaluate($context)) {
            $this->executeChildren($context, $out, $in);
        }
    }

    public function setExpression(Node $expression): void
    {
        $this->expression = $expression;
    }

    public function setStatements(array $statements): void
    {
        $this->statements = $statements;
    }
}
