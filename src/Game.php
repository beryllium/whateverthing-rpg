<?php

namespace Beryllium\OnePageRpg;

use Beryllium\OnePageRpg\Commands\StartCommand;
use Symfony\Component\Console\Application;

class Game extends Application
{
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $startCommand = new StartCommand();
        $this->add($startCommand);
        $this->setDefaultCommand($startCommand->getName(), true);
    }
}