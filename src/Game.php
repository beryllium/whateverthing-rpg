<?php

namespace Beryllium\OnePageRpg;

use Beryllium\OnePageRpg\Commands\StartCommand;
use Symfony\Component\Console\Application;

class Game extends Application
{
    public function __construct(string $name = 'Whateverthing: The One-Page RPG Engine', string $version = '1.0.0')
    {
        parent::__construct($name, $version);

        $startCommand = new StartCommand();
        $this->add($startCommand);
        $this->setDefaultCommand($startCommand->getName(), true);
    }
}