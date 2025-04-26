<?php

namespace App\SimpleLanguage;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrintCommand extends AbstractCommand
{
    public function execute(array &$context, OutputInterface $out, InputInterface $in): void
    {
        $result = $this->expression->evaluate($context);
        // interpolate values with the given context:
        if (preg_match_all('/#\{(\w+)\}/', $result, $matches)) {
            foreach ($matches[1] as $var) {
                if (!isset($context[$var])) {
                    throw new \RuntimeException("Undefined variable: {$var}");
                }

                $result = str_replace('#{' . $var . '}', $context[$var], $result);
            }
        }


        $out->writeln($result);
    }
}
