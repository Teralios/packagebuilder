<?php

namespace Teralios\Vulcanus;

// imports
use Symfony\Component\Console\Application;

class Core extends Application
{
    protected ?Config $config = null;

    /**
     * Core constructor.
     * @param Config|null $config
     */
    public function __construct(?Config $config = null)
    {
        parent::__construct("Teralios' WoltLab® Suite Core Package Builder", '2.0.0 Alpha 1');

        if ($config !== null) {
            $this->setConfig($config);
        }
        $this->addDefaultCommands();
    }

    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    protected function addDefaultCommands()
    {
    }
}
