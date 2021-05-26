<?php

namespace Teralios\Vulcanus;

final class Helper
{
    public static function unifySeparators(string $path): string
    {
        return \str_replace(array('\\\\', '\\'), '/', $path);
    }

    public static function addTrailingSlash(string $path): string
    {
        return (\str_ends_with($path, '/')) ? $path : $path . '/';
    }

    public static function getAttribute(string $name, \DOMNode $node): ?string
    {
        if (!$node->hasAttributes()) {
            return null;
        }

        /** @scrutinizer ignore-call */
        $attribute = $node->attributes->getNamedItem($name);

        return $attribute->nodeValue ?? null;
    }
}
