<?php

namespace App\SimpleLanguage;

use App\Parser\Node;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand
{
    protected ?Node $expression = null;

    public function __construct(
        public string $name,
        public int $lineNumber,
    ) {
    }

    public function setExpression(Node $expression): void
    {
        $this->expression = $expression;
    }

    abstract public function execute(array &$context, OutputInterface $out, InputInterface $in): void;
}
