<?php

namespace App\SimpleLanguage;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SetCommand extends AbstractCommand
{

    public function execute(array &$context, OutputInterface $out, InputInterface $in): void
    {
        if (!$this->expression) {
            $questionHelper = new QuestionHelper();
            $question = new Question('Value for ' . $this->name . ': ');
            $context[$this->name] = $questionHelper->ask($in, $out, $question);
        } else {
            $context[$this->name] = $this->expression->evaluate($context);
        }
    }
}