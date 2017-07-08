<?php

namespace Eightfold\DocumenterPhp;

// Project paths
use \DirectoryIterator;

// Files for project
use \RecursiveDirectoryIterator;
use \RecursiveCallbackFilterIterator;
use \FilesystemIterator;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

use Eightfold\DocumenterPhp\File;
use Eightfold\DocumenterPhp\Version;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;

use Eightfold\Documenter\Php\Method;
use Eightfold\Documenter\Php\Property;

class Project
{
    use Gettable;

    private $documenter = null;

    private $path = '';

    private $slug = '';

    private $versions = [];

    // /**
    //  * Add all the project paths to an array passed by reference.
    //  *
    //  * All projects being considered for documentation generation exist in a `$path`.
    //  * Each directory within the `$path` is considered a project being considered.
    //  *
    //  * Use this method to get all the paths being considered for documentation.
    //  *
    //  * Calls `projectPathsForSlug`.
    //  *
    //  * @param  string $path       Base path where all projects are stored.
    //  * @param  array  &$projArray Array to update with the paths.
    //  */
    // static public function projectPaths($path, &$projArray)
    // {
    //     // Verify file or directory exists.
    //     if (file_exists($path)) {
    //         $directory = new DirectoryIterator($path);

    //         // Iterate over the containing directory.
    //         foreach ($directory as $projectFileInfo) {
    //             // Each project should be its own directory within container.
    //             // Further, do not want to process hidden files or directories.
    //             if ($projectFileInfo->isDir() && !$projectFileInfo->isDot()) {
    //                 $projectSlug = $projectFileInfo->getFilename();
    //                 Project::projectPathsForSlug($path, $projectSlug, $projArray);
    //             }
    //         }
    //     }
    // }

    // *
    //  * Add all version paths to an array passed by reference.
    //  *
    //  * Calls `projectPathForVersion`
    //  *
    //  * @param  string $path       Base path where all projets are stored.
    //  * @param  string $slug       The directory name for the specific project.
    //  * @param  array  &$projArray Array to update with the paths.

    // static public function projectPathsForSlug($path, $slug, &$projArray)
    // {
    //     $projectPath = $path .'/'. $slug;

    //     if (file_exists($projectPath)) {
    //         $directory = new DirectoryIterator($projectPath);
    //         foreach ($directory as $version) {
    //             if ($version->isDir() && !$version->isDot()) {
    //                 $versionSlug = $version->getFilename();
    //                 Project::projectPathForVersion($path, $slug, $versionSlug, $projArray);
    //             }
    //         }
    //     }
    // }

    // /**
    //  * Add specific version for specific project to array passed by reference.
    //  *
    //  * @param  string $path       Base path where all projets are stored.
    //  * @param  string $slug       The directory name for the specific project.
    //  * @param  string $version    The directory name for the specific version.
    //  * @param  array  &$projArray Array to update with the paths.
    //  */
    // static public function projectPathForVersion($path, $slug, $version, &$projArray)
    // {
    //     $projArray[$slug][$version] = $path .'/'. $slug .'/'. $version;
    // }

    /**
     * [__construct description]
     *
     * @param string $path   The complete path to the project version.
     * @param string $root   The root directory of the directory to process within the
     *                       project. No starting slash.
     * @param array $ignore  Array of directory names to ignore.
     */
    public function __construct($documenter, $slug, $title = '')
    // public function __construct($path, $root = 'src', $ignore = [])
    {
        $this->documenter = $documenter;
        $this->path = $documenter->dirPath .'/'. $slug;
        $this->slug = $slug;

        // Path should be [...] /projectSlug/versionSlug
        // $this->path = $path;

        // $parts = explode('/', $this->path);
        // $this->basePath = implode('/', $parts);
        // $this->versionSlug = array_pop($parts);
        // $this->projectSlug = array_pop($parts);

        // $this->root = $root;
        // $this->ignore = $ignore;
    }

    private function path()
    {
        return $this->path;
    }

    public function slug()
    {
        return $this->slug;
    }

    public function url()
    {
        if ($this->documenter->url == '/') {
            return '/'. $this->slug;
        }
        return $this->documenter->url .'/'. $this->slug;
    }

    public function versions()
    {
        if (count($this->versions) == 0) {
            if ($directory = new DirectoryIterator($this->path)) {
                foreach ($directory as $projectFileInfo) {
                    if ($projectFileInfo->isDir() && !$projectFileInfo->isDot()) {
                        $versionSlug = $projectFileInfo->getFilename();
                        $this->versions[$versionSlug] = null;

                    }
                }
            }
        }
        return $this->versions;
    }

    public function versionWithSlug($slug, $root = 'src', $ignore = [])
    {
        if (array_key_exists($slug, $this->versions)) {
            // Cache Project instance
            if (is_null($this->versions[$slug])) {
                $title = $this->versions[$slug];
                $this->versions[$slug] =  new Version($this, $slug, $root, $ignore);

            }
            return $this->versions[$slug];
        }
        return null;
    }
}
