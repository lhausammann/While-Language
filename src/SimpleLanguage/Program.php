<?php

namespace App\SimpleLanguage;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Program extends ControlCommand
{
public function execute(array &$context, OutputInterface $out, InputInterface $in): void
{
    $this->executeChildren($context, $out, $in);
}}