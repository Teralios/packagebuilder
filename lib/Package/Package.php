<?php

namespace Teralios\Vulcanus\Package;

// imports
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class        Package
 * @package     pb
 * @subpackage  Teralios\Vulcanus\Package
 * @author      Karsten (Teralios) Achterrath
 * @copyright   ©2020 Teralios.de
 * @license     GNU General Public License <https://www.gnu.org/licenses/gpl-3.0.txt>
 */
class Package
{
    /**
     * @var Crawler Dom Crawler for package file
     */
    protected Crawler $crawler;

    /**
     * @var string Path to wsc package
     */
    protected string $pathToPackage;

    /**
     * @var string Default name of package file
     */
    protected string $packageFile = 'package.xml';

    /**
     * @var FileMap Name of files to ignore in archives.
     */
    protected FileMap $ignoreFiles;

    /**
     * Package constructor.
     * @param string $pathToPackage
     * @param Crawler|null $crawler
     */
    public function __construct(string $pathToPackage, ?Crawler $crawler = null)
    {
        $this->pathToPackage = $pathToPackage;
        $this->crawler = $crawler ?? new Crawler();

        $this->validatePath();
        $this->readPackageXML();
    }

    protected function validatePath()
    {
    }

    protected function readPackageXML()
    {
    }
}
