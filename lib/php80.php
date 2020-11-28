<?php

use Symfony\Polyfill\Php80\Php80;

if (\PHP_VERSION_ID < 80000) {
    function string_starts_with(string $haystack, string $needle): bool
    {
        return Php80::str_starts_with($haystack, $needle);
    }

    function str_ends_with(string $haystack, string $needle): bool
    {
        return Php80::str_ends_with($haystack, $needle);
    }
}
