<?php

namespace Teralios\Vulcanus\Package;

// imports
use SimpleXMLElement;
use Teralios\Vulcanus\Helper;

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
    // default locations
    protected const DEFAULT_FILE_FOLDER     = '*files/';
    protected const DEFAULT_LANGUAGE_FOLDER = 'language/';
    protected const DEFAULT_TEMPLATE_FOLDER = '*templates/';

    // system variables
    protected SimpleXMLElement $packageXML;
    protected bool $withRequirements = false;

    // path to package
    protected string $pathToPackage;

    // default information of a package
    protected string $packageFile = 'package.xml';
    protected ?string $id;
    protected ?string $name;
    protected ?string $version;

    // files and folders.
    protected FileMap $ignoreFiles;
    protected FileMap $archiveFolders;
    protected FileMap $packageFolders;

    /**
     * Package constructor.
     * @param string $pathToPackage
     * @throws PackageException
     */
    public function __construct(string $pathToPackage)
    {
        $this->pathToPackage = $pathToPackage;

        $this->loadDefaults();
        $this->validatePath();
        $this->readPackageXML();
    }

    /**
     * Set and return to pack requirements.
     * @param bool|null $withRequirements
     * @return bool
     */
    public function withRequirements(?bool $withRequirements = null): bool
    {
        $this->withRequirements = $withRequirements ?? $this->withRequirements;

        return $this->withRequirements;
    }

    /**
     * Return package id (tld.vendor_name.package_name)
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Return readable package name.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return package version.
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Returns map with files to ignore while packing.
     * @return FileMap
     */
    public function getFilesToIgnore(): FileMap
    {
        return $this->ignoreFiles;
    }

    /**
     * Returns a map with folders for own packages.
     * @return FileMap
     */
    public function getFolderToArchive(): FileMap
    {
        return $this->archiveFolders;
    }

    /**
     * Returns a map with folders for package archive.
     * @return FileMap
     */
    public function getFolderToPackage(): FileMap
    {
        return $this->packageFolders;
    }

    /**
     * Load default information.
     */
    protected function loadDefaults(): void
    {
        // default files to ignore
        $this->ignoreFiles = new FileMap();
        $this->ignoreFiles->addFiles([
            '.*', // ignore .github, .gitignore and other . dot files.
            'require.build.js', // not needed in package
            '*.md', // Markdown files are for documentation.
        ]);

        // package folders
        $this->packageFolders = new FileMap();

        // folders with own archive
        $this->archiveFolders = new FileMap();
    }

    /**
     * Validate path and check package xml.
     * @throws PackageException
     */
    protected function validatePath(): void
    {
        $file = Helper::addTrailingSlash(Helper::unifySeparators($this->pathToPackage)) . $this->packageFile;

        if (!file_exists($file)) {
            throw new PackageException("'" . $this->pathToPackage . "' did not contain a valid WSC package.");
        }

        $this->packageXML = simplexml_load_file($file);
    }

    /**
     * Reads package xml.
     */
    protected function readPackageXML(): void
    {
        $this->id = $this->packageXML->attributes()->name ?? null;
        if ($this->id === null) {
            throw new PackageException('No package name found');
        }

        $this->name = $this->packageXML->children()->packageinformation->children()->packagename ?? $this->id;
        $this->version = $this->packageXML->children()->packageinformation->children()->version ?? 'unknown';
    }
}
