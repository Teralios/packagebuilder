<?php

namespace Teralios\Vulcanus\File;

class FileMap implements \Iterator, \Countable
{
    protected array $files = [];
    protected int $index = 0;

    public function __construct(?array $files = null)
    {
        if ($files !== null) {
            $this->addFiles($files);
        }
    }

    public function addFiles(array $files): self
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
        if (!in_array($file, $this->files)) {
            $this->files[] = $file;
        }

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

    public function valid(): bool
    {
        return isset($this->files[$this->index]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return count($this->files);
    }
}
