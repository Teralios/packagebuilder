<?php

namespace Teralios\Vulcanus\Package;

// imports
use SimpleXMLElement;
use Teralios\Vulcanus\File\FileMap;
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
    protected bool $withRequirements = true;

    // path to package
    protected string $pathToPackage;

    // default information of a package
    protected string $packageFile = 'package.xml';
    protected string $id;
    protected string $name;
    protected string $version;
    protected string $abbr;
    protected bool $isApplication;

    // files and folders.
    protected FileMap $blackList;
    protected FileMap $whiteList;
    protected FileMap $archiveFolders;
    protected FileMap $packageFolders;
    protected FileMap $requirements;

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
    public function getBlackListFiles(): FileMap
    {
        return $this->blackList;
    }

    /**
     * Return map with white listed files.
     * @return FileMap
     */
    public function getWhiteListFiles(): FileMap
    {
        return $this->whiteList;
    }

    /**
     * Returns a map with folders for own archive.
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
     * Return requirements to pack.
     * @return FileMap
     */
    public function getRequirements(): FileMap
    {
        return $this->requirements;
    }

    /**
     * Load default information.
     */
    protected function loadDefaults(): void
    {
        // default files to ignore
        $this->blackList = new FileMap();
        $this->blackList->addFiles([
            '.*', // ignore .github, .gitignore and other . dot files.
            'require.build.js', // not needed in package
            '*.md', // Markdown files are for documentation.
        ]);

        // whitelist for black list files.
        $this->whiteList = new FileMap();
        $this->whiteList->addFile('.htaccess');

        // package folders
        $this->packageFolders = new FileMap();

        // folders with own archive
        $this->archiveFolders = new FileMap();

        // requirements
        $this->requirements = new FileMap();
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
     * @throws PackageException
     */
    protected function readPackageXML(): void
    {
        // basic package information
        $this->id = $this->packageXML->attributes()->name ?? '';
        if (empty($this->id)) {
            throw new PackageException('No package name found');
        }

        // extended package information
        $this->name = $this->packageXML->packageinformation->packagename ?? $this->id;
        $this->version = $this->packageXML->packageinformation->version ?? 'unknown';
        $isApplication = $this->packageXML->packageinformation->isapplication ?? false;
        $this->isApplication = (bool) $isApplication;

        // abbr
        $parts = explode('.', $this->id);
        $this->abbr = end($parts);

        // load information for pack package.
        if ($this->withRequirements === true) {
            $this->readRequirements();
        }

        $this->readInstructionsBlock();
    }

    protected function readRequirements(): void
    {
        $packages = $this->packageXML->requiredpackages->requiredpackage;
        if (count($packages)) {
            foreach ($packages as $package) {
                $file = $package->attributes()->file ?? null;

                if ($file !== null) {
                    $parts = explode('/', $file);
                    $packageToPack = end($parts);
                    $packageToPack = str_replace('.tar', '', $packageToPack);
                    $this->requirements->addFile($packageToPack);
                }
            }
        }
    }

    protected function readInstructionsBlock()
    {
        $instructionsBlock = $this->packageXML->children()->instructions ?? null;

        if ($instructionsBlock !== null) {
            foreach ($instructionsBlock as $instructions) {
                if (count($instructions->instruction)) {
                    $this->readInstructions($instructions->instruction);
                }
            }
        }
    }

    protected function readInstructions(SimpleXMLElement $instructions)
    {
        foreach ($instructions as $instruction) {
            $type = $instruction->attributes()->type ?? null;
            $application = $instruction->attributes()->application ?? 'wcf';

            if (str_ends_with(strtolower($type), 'file')) {
                $prefix = $this->getPrefix($application);

                $archive = str_replace('*', $prefix, static::DEFAULT_FILE_FOLDER);
                $this->archiveFolders->addFile($archive);
            }

            if (str_ends_with(strtolower($type), 'template')) {
                $prefix = ((str_starts_with($type, 'acp')) ? 'acp' : '') . $this->getPrefix($application);
                $archive = str_replace('*', $prefix, static::DEFAULT_TEMPLATE_FOLDER);
                $this->archiveFolders->addFile($archive);
            }

            if ($type == 'language') {
                $this->packageFolders->addFile('language/');
            }
        }
    }

    protected function getPrefix(string $application): string
    {
        if ($this->isApplication && $application == $this->abbr) {
            return '';
        } elseif (!$this->isApplication && $application == 'wcf') {
            return '';
        }

        return $application;
    }
}
