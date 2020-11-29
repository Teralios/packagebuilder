<?php

namespace Teralios\Vulcanus;

final class Helper
{
    public static function unifySeparators(string $path): string
    {
        $path = str_replace('\\\\', '/', $path);
        $path = str_replace('\\', '/', $path);

        return $path;
    }

    public static function addTrailingSlash(string $path): string
    {
        return (str_ends_with($path, '/')) ? $path : $path . '/';
    }
}
