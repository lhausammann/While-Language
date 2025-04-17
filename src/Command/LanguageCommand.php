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
PRINT "Wie ist Dein Name?";
SET name = <;
PRINT "Hallo";
PRINT name;

SET x = 15;
WHILE(x > 10);
    PRINT "x is greater than 10";
    SET x = x - 1;
    PRINT x;
END;
SET y = 10;
PRINT "Resultat:";
PRINT x + y;
EOT;


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->tokenizer = new Tokenizer($this->script);
        $this->parser = new Parser($this->tokenizer);
        $program = $this->parser->parse();
        $context = [];
        $program->execute($context, $output, $input);
        return Command::SUCCESS;
    }
}
