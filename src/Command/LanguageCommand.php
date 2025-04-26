<?php

declare(strict_types=1);

namespace App\Command;

use App\Parser\Tokenizer;
use App\SimpleLanguage\Parser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:language', description: 'Hello PhpStorm')]
class LanguageCommand extends Command
{
    private string $script = <<<EOT

SET running=true;
PRINT "Enter your name or 'exit' to quit";
SET tpl = "Dein Name ist: #{name}";
WHILE (running);
    SET EXIT = "exit";
    SET innerLoop = true;
    PRINT "Dein Name:";
    SET name = <;
    WHILE (name ~ EXIT AND innerLoop=true);
        SET innerLoop = false;
        PRINT tpl;
    END;
    WHILE (name = EXIT AND innerLoop=true);
        SET innerLoop = false;
        SET running = false;
        PRINT "Bye!";
    END;
END;
EOT;

    protected function configure(): void
    {
        $this->setDescription('While Language - input file name to laod');
        $this->addArgument('file', null, 'File to load');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (null !== $file) {
            $this->script = file_get_contents(__DIR__.'/../../scripts/'.$file);
            if (false === $this->script) {
                $output->writeln("<error>Could not read file: $file</error>");

                return Command::FAILURE;
            } else {
                $this->script = str_replace("\r\n", "\n", $this->script);
            }
        }

        try {
            $this->tokenizer = new Tokenizer($this->script);
            $this->parser = new Parser($this->tokenizer);
            $program = $this->parser->parse();
        } catch (\Exception $e) {
            $output->writeln("<error>Parser error: {$e->getMessage()}</error>");
            $output->writeln('Context: '.$this->tokenizer->getContext());

            return Command::FAILURE;
        }
        $context = ['true' => true, 'false' => false];
        $program->execute($context, $output, $input);

        return Command::SUCCESS;
    }
}
