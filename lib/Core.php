<?php

namespace Teralios\Vulcanus;

// imports
use Symfony\Component\Console\Application;

class Core extends Application
{
    /**
     * Core constructor.
     */
    public function __construct()
    {
        parent::__construct("Teralios' WoltLab® Suite Core Package Builder", '2.0.0 Alpha 1');
        $this->addDefaultCommands();
    }

    protected function addDefaultCommands()
    {
    }
}
