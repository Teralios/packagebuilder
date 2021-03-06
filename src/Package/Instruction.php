<?php

namespace Teralios\Vulcanus\Package;

class Instruction
{
    protected const FILE_TYPES = [
        'file',
        'acpTemplate',
        'template',
        'language'
    ];

    protected const TYPE_PATHS = [
        'file' => 'files/',
        'acpTemplate' => 'acptemplates/',
        'template' => 'templates/',
        'language' => 'language/'
    ];

    protected const IN_MAIN = [
        'language'
    ];

    public function __construct(public string $type, protected string $value)
    {
    }

    public function hasFiles(): bool
    {
        return in_array($this->type, self::FILE_TYPES);
    }

    public function getPath(): ?string
    {
        if ($this->hasFiles()) {
            return (empty($this->value)) ? self::TYPE_PATHS[$this->type] : substr($this->value, 0, -4) . '/';
        }

        return null;
    }

    public function toMainArchive(): bool
    {
        return in_array($this->type, self::IN_MAIN);
    }

    public function getFileName(): ?string
    {
        if ($this->hasFiles()) {
            return null;
        }

        return match(true) {
            $this->type === 'sql' && empty($this->value) => 'install.sql',
            empty($this->value) => $this->type . '.xml',
        default => $this->value
        };
    }
}
