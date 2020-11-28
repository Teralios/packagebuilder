<?php

namespace Teralios\Vulcanus;

// imports
use Symfony\Component\Console\Application;

class Core extends Application
{
    public function __construct()
    {
        parent::__construct("Teralios' Vulcanus", "1.1.0 Alpha 1");

        $this->addDefaultCommands();
    }

    protected function addDefaultCommands()
    {

    }
}
