<?php

namespace App\SimpleLanguage;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintCommand extends AbstractCommand
{

    public function execute(array &$context, OutputInterface $out, InputInterface $in): void
    {
        $result = $this->expression->evaluate($context);
        $out->writeln($result);
    }
}