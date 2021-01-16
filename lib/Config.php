<?php

namespace Teralios\Vulcanus;

class Config
{
    protected string $pathToProjects = '';
    protected string $tmpDir = '';
    protected bool $useFileOperations = true;
    protected bool $withRequirements = true;
    protected array $otherConfig = [];

    public function __construct(string $mainFile, ?string $configFile = null)
    {
        $this->setPaths($mainFile);

        if ($configFile !== null && file_exists($configFile)) {
            $this->readConfig($configFile);
        }
    }

    protected function setPaths(string $mainFile): void
    {
        $mainFile = Helper::unifySeparators($mainFile);
        $mainDir = dirname($mainFile);

        $this->tmpDir = Helper::addTrailingslash($mainDir) . 'tmp/';
        $this->pathToProjects = Helper::addTrailingSlash(dirname($mainDir));
    }

    public function getPathToProjects(): string
    {
        return $this->pathToProjects;
    }

    public function getTmpDir(): string
    {
        return $this->tmpDir;
    }

    public function withRequirements(): bool
    {
        return $this->withRequirements;
    }

    public function useFileOperations(): bool
    {
        return $this->useFileOperations;
    }

    public function has(string $name): bool
    {
        return isset($this->otherConfig[$name]);
    }

    public function get(string $name): mixed
    {
        return $this->otherConfig[$name] ?? null;
    }

    protected function readConfig(string $configFile)
    {
        $config = [];
        include($configFile);

        $this->otherConfig = $config;
    }
}