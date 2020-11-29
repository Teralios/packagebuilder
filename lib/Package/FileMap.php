<?php

namespace Teralios\Vulcanus\Package;

class FileMap implements \Iterator
{
    protected array $files = [];
    protected int $index = 0;

    public function __construct(?array $files = null)
    {
        if ($files !== null) {
            $this->setFiles($files);
        }
    }

    public function addFiles(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                $this->addFile($file);
            }
        }

        return $this;
    }

    public function addFile(string $file): self
    {
        $this->files[] = $file;

        return $this;
    }

    public function current(): ?string
    {
        return $this->files[$this->index] ?? null;
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid()
    {
        return isset($this->files[$this->index]);
    }

    public function rewind()
    {
        $this->index = 0;
    }
}
