<?php

namespace App\SimpleLanguage;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ControlCommand extends AbstractCommand
{
    protected array $statements = [];

    public function __construct(
        public string $name,
        public int $lineNumber,
    ) {
        parent::__construct($name, $lineNumber);
    }

    public function addStatement(AbstractCommand $statement): void
    {
        $this->statements[] = $statement;
    }

    public function executeChildren(array &$context, OutputInterface $out, InputInterface $in): void{
        foreach ($this->statements as $statement) {
            $statement->execute($context, $out, $in);
        }
    }
}