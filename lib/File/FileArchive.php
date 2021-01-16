<?php

namespace Teralios\Vulcanus\File;

class FileArchive
{
    protected string $path;
    protected FileMap $pathsToPack;
    protected FileMap $blackList;
    protected FileMap $whiteList;

    protected FileHandlers $fileHandlers;
}