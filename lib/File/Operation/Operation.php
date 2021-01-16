<?php

namespace Teralios\Vulcanus\File\Operation;

abstract class Operation
{
    protected string $extension = '*';
    protected ?string $content = null;
    protected ?string $error = null;

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function execute(): bool
    {
        if ($this->content !== null) {
            try {
                $this->action();
            } catch (OperationException $e) {
                $this->error = $e->getMessage();
                return false;
            }
        }

        return true;
    }

    public function getErrorInformation(): ?string
    {
        return $this->error;
    }

    abstract protected function action(): void;
}