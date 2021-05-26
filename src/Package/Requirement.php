<?php

namespace Teralios\Vulcanus\Package;

class Requirement
{
    public function __construct(public string $name, public string $minversion)
    {
    }
}
