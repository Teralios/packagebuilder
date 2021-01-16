<?php

namespace Teralios\Vulcanus\File;

use Teralios\Vulcanus\File\Operation\Operation;

class File
{
    protected string $filename;
    protected string $content;
    protected bool $processed = false;
    protected bool $status = false;
    protected array $operations = [];

    public function setFile(string $filename): self
    {
        $this->filename = $filename;
        $this->processed = false;

        return $this;
    }

    public function processFile(): bool|self
    {
        if (!$this->readFile()) {
            return false;
        }

        if (!$this->executeFileOperations()) {
            return false;
        }

        return $this;
    }

    protected function readFile(): bool
    {
        if (file_exists($this->filename)) {
            $this->content = file_get_contents($this->filename);
            return true;
        }

        return false;
    }

    protected function executeFileOperations(): bool
    {
        if (count($this->operations)) {
            /** @var Operation $operation */
            foreach ($this->operations as $operation) {
                $extension = mb_strtolower($operation->getExtension());

                if ($extension == '*' || str_ends_with(mb_strtolower($this->filename), $extension)) {
                    $operation->setContent($this->content);

                    if ($operation->execute()) {
                        $this->content = $operation->getContent();
                        $operation->setContent(null);
                    } else {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}