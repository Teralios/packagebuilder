<?php

namespace Teralios\Vulcanus\Package;

use Symfony\Component\DomCrawler\Crawler;

class Package
{
    protected Crawler $crawler;
    protected string $pathToPackage;
    protected string $packageFile = 'package.xml';
    protected array $ignoreFiles = [];

    public function __construct(string $pathToPackage, ?Crawler $crawler = null)
    {

    }
}
