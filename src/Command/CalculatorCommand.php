<?php

declare(strict_types=1);

namespace App\Command;

use App\Parser\MathematicalParser;
use App\Parser\Tokenizer;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


#[AsCommand(name: 'app:calculator', description: 'Hello PhpStorm')]
class CalculatorCommand extends Command
{
    protected function configure()
    {
        $this->setHelp('This command allows you to calculate...');
        $this->addArgument('expression', InputArgument::REQUIRED, 'Expression');
        $this->addOption('context', 'c', InputArgument::OPTIONAL, 'Context', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $defaultContext = ['true' => true, 'false' => false, 'null' => null];
        $context = parse_url('?'.$input->getOption('context'), PHP_URL_QUERY);
        if ($context) {
            $context = $context + $defaultContext;
        } else {
            $context = $defaultContext;
        }
        
        $output->writeln('Context: ' . print_r($context, true));

        $expr = $input->getArgument('expression');
        $tokenizer = new Tokenizer($expr);
        $parser = new MathematicalParser($tokenizer);
        $ast = $parser->parse();
        $output->writeln((string) $ast->evaluate($context));
        return Command::SUCCESS;
    }
}
