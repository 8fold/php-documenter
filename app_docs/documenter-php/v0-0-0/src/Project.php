<?php

namespace Eightfold\DocumenterPhp;

// Project paths
use \DirectoryIterator;

// Files for project
use \RecursiveDirectoryIterator;
use \RecursiveCallbackFilterIterator;
use \FilesystemIterator;

use Eightfold\DocumenterPhp\File;

use Eightfold\DocumenterPhp\Traits\Gettable;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;

use Eightfold\Documenter\Php\Interface_;
use Eightfold\Documenter\Php\Method;
use Eightfold\Documenter\Php\Property;

class Project
{
    use Gettable;

    private $path = '';

    private $basePath = '';

    private $versionSlug = '';

    private $projectSlug = '';

    private $root = '';

    private $ignore = [];

    private $files = [];

    private $classes = [];

    private $classesCategorized = [];

    private $traits = [];

    private $interfaces = [];

    /**
     * Add all the project paths to an array passed by reference.
     *
     * All projects being considered for documentation generation exist in a `$path`.
     * Each directory within the `$path` is considered a project being considered.
     *
     * Use this method to get all the paths being considered for documentation.
     *
     * Calls `projectPathsForSlug`.
     *
     * @param  string $path       Base path where all projects are stored.
     * @param  array  &$projArray Array to update with the paths.
     */
    static public function projectPaths($path, &$projArray)
    {
        // Verify file or directory exists.
        if (file_exists($path)) {
            $directory = new DirectoryIterator($path);

            // Iterate over the containing directory.
            foreach ($directory as $projectFileInfo) {
                // Each project should be its own directory within container.
                // Further, do not want to process hidden files or directories.
                if ($projectFileInfo->isDir() && !$projectFileInfo->isDot()) {
                    $projectSlug = $projectFileInfo->getFilename();
                    Project::projectPathsForSlug($path, $projectSlug, $projArray);
                }
            }
        }
    }

    /**
     * Add all version paths to an array passed by reference.
     *
     * Calls `projectPathForVersion`
     *
     * @param  string $path       Base path where all projets are stored.
     * @param  string $slug       The directory name for the specific project.
     * @param  array  &$projArray Array to update with the paths.
     */
    static public function projectPathsForSlug($path, $slug, &$projArray)
    {
        $projectPath = $path .'/'. $slug;

        if (file_exists($projectPath)) {
            $directory = new DirectoryIterator($projectPath);
            foreach ($directory as $version) {
                if ($version->isDir() && !$version->isDot()) {
                    $versionSlug = $version->getFilename();
                    Project::projectPathForVersion($path, $slug, $versionSlug, $projArray);
                }
            }
        }
    }

    /**
     * Add specific version for specific project to array passed by reference.
     *
     * @param  string $path       Base path where all projets are stored.
     * @param  string $slug       The directory name for the specific project.
     * @param  string $version    The directory name for the specific version.
     * @param  array  &$projArray Array to update with the paths.
     */
    static public function projectPathForVersion($path, $slug, $version, &$projArray)
    {
        $projArray[$slug][$version] = $path .'/'. $slug .'/'. $version;
    }

    /**
     * [__construct description]
     *
     * @param string $path   The complete path to the project version.
     * @param string $root   The root directory of the directory to process within the
     *                       project.
     * @param array $ignore  Array of directory names to ignore.
     */
    public function __construct($path, $root = '/src', $ignore = [])
    {
        // Path should be [...] /projectSlug/versionSlug
        $this->path = $path;

        $parts = explode('/', $this->path);
        $this->basePath = implode('/', $parts);
        $this->versionSlug = array_pop($parts);
        $this->projectSlug = array_pop($parts);

        $this->root = $root;
        $this->ignore = $ignore;
    }

    private function basePath()
    {
        return $this->basePath;
    }

    public function projectSlug()
    {
        return $this->projectSlug;
    }

    public function versionSlug()
    {
        return $this->versionSlug;
    }

    public function version()
    {
        return str_replace(['v', '-'], ['', '.'], $this->versionSlug());
    }

    /**
     * @todo Consider deprecating; developers using this library may not use.
     *
     * @return [type] [description]
     */
    public function url()
    {
        return '/'. $this->projectSlug .'/'. $this->versionSlug;
    }

    /**
     * [files description]
     * @return [type] [description]
     *
     * @category Files
     */
    private function files()
    {
        if (count($this->files) == 0) {
            $iterator = $this->fileIterator();
            $files = [];
            foreach ($iterator as $fileInfo) {
                $file = new File($fileInfo->getPathname());
                $file->process();
                $namespaceSlug = $this->namespaceToSlug($file->getNamespace());
                $files[$namespaceSlug][] = $file;

            }
            // var_dump(array_keys($files));
            $this->files = $files;
        }
        return $this->files;
    }

    /**
     * The total number of files being processed for documentation.
     *
     * @return [type] [description]
     *
     * @category Files
     */
    public function totalFiles()
    {
        return array_sum(array_map("count", $this->files()));
    }

    /**
     * @category Get objects
     *
     * @return [type] [description]
     */
    public function classes()
    {
        return $this->objectsForPropertyName('classes', Class_::class, 'getClasses');
    }

    /**
     * [classesCategorized description]
     * @return [type] [description]
     *
     * @category Get objects
     */
    public function classesCategorized()
    {
        return $this->objectsOrdered($this->classes(), 'classesCategorized');
    }

    /**
     * [objectWithFullName description]
     * @param  [type] $fullName [description]
     * @return [type]           [description]
     *
     * @category Get objects
     */
    public function objectWithFullName($fullName)
    {
        $fullNameSlug = $this->namespaceToSlug($fullName);

        // Check classes for `fullNameSlug`.
        $classes = $this->classes();
        if (isset($classes[$fullNameSlug])) {
            return $classes[$fullNameSlug];
        }
        return null;
    }

    /**
     *
     * @category Get objects
     *
     * @param  [type] $propertyName       [description]
     * @param  [type] $fileMethodName     [description]
     * @param  [type] $classToInstantiate [description]
     * @return [type]                     [description]
     */
    private function objectsForPropertyName($instanceProperty, $classToInstantiate, $fileReflectorMethodName)
    {
        // We have more than 0 Files and 0 objects in instance property.
        if (count($this->files()) > 0 && count($this->{$instanceProperty}) == 0) {

            $objects = [];
            // Iterate over our files array. [namespace][i] => File
            foreach ($this->files() as $namespace => $namespaceFiles) {

                // Iterate over File instances.
                foreach ($namespaceFiles as $file) {

                    // If the File or FileReflector has the desired method.
                    if (method_exists($file, $fileReflectorMethodName)) {

                        // Get the reflectors by calling the method
                        $reflectorsAfterMethodCall = $file->$fileReflectorMethodName();

                        // Iterate over the reflectors.
                        foreach ($reflectorsAfterMethodCall as $reflector) {

                            // Instantiate an instance of our object.
                            $object = new $classToInstantiate($this, $reflector);

                            // Convert namespace (plus class, trait, interface name).
                            $key = $this->namespaceToSlug($object->fullName);

                            // Add instance to objects array.
                            $objects[$key] = $object;

                        }
                    }
                }
            }
            // Set instance property value for caching purposes.
            $this->{$instanceProperty} = $objects;
        }
        return $this->{$instanceProperty};
    }

    /**
     * [objectsOrdered description]
     * @param  [type] $symbols      [description]
     * @param  [type] $propertyName [description]
     * @return [type]               [description]
     *
     * @category Get objects
     */
    private function objectsOrdered($symbols, $propertyName)
    {
        if (count($this->{$propertyName}) == 0) {
            $abstract = 0;
            $build = [];
            foreach ($symbols as $key => $symbol) {
                $category = (strlen($symbol->category()) > 0)
                    ? $symbol->category()
                    : 'NO_CATEGORY';
                $type = 'NO_TYPE';
                if (get_class($symbol) == Class_::class || get_class($symbol)  == Method_::class) {
                    if ($symbol->isAbstract()) {
                        $type = 'abstract';
                        $abstract++;

                    } else {
                        $type = 'concrete';

                    }
                }
                $build[$category][$type][$symbol->name()] = $symbol;

            }
            $this->{$propertyName} = $build;
        }
        return $this->{$propertyName};
    }

    /**
     * [traits description]
     * @return [type] [description]
     *
     * @category Files
     */
    public function traits()
    {
        return $this->objectsForPropertyName('traits', Trait_::class, 'getTraits');
    }

    /**
     * Converts backslashes to hyphens and converts string to all lower case.
     *
     * @param  [type] $namespace [description]
     * @return [type]            [description]
     *
     * @category Utilities
     */
    private function namespaceToSlug($namespace)
    {
        $parts = explode('\\', $namespace);
        if (strlen($parts[0]) == 0) {
            array_shift($parts);
            $namespace = implode('\\', $parts);
        }
        return strtolower(str_replace('\\', '-', $namespace));
    }

    /**
     * [fileIterator description]
     * @return [type] [description]
     *
     * @category Utilities
     */
    private function fileIterator()
    {
        $follow = FilesystemIterator::FOLLOW_SYMLINKS;
        $directory = new RecursiveDirectoryIterator($this->path .'/'. $this->root, $follow);
        $ignore = $this->ignore;
        $basePath = $this->basePath;

        $filter = new RecursiveCallbackFilterIterator(
            $directory,
            function ($current, $key, $iterator) use ($ignore, $basePath) {
                $filename = $current->getFilename();

                $ignored = in_array($filename, $ignore);
                $hidden = $filename[0] === '.';

                $isDir = $current->isDir();
                $isPhp = !strcasecmp($current->getExtension(), 'php');

                if ($ignored || $hidden) {
                    return false;

                } elseif ($isDir) {
                    // var_dump($filename);
                    $filePath = strtolower($filename);
                    $filePathExploded = explode('/', $filePath);
                    $intersect = array_intersect($filePathExploded, $ignore);
                    $remainingCount = count($intersect);
                    return (0 == $remainingCount);

                } elseif ($isPhp) {
                    // var_dump($filename);
                    return str_replace($basePath, '', $current->getFilename());

                }
        });
        $iterator = new \RecursiveIteratorIterator($filter);
        return $iterator;
    }



















    /**
     * Get versions for a specified project.
     *
     * @param  [type] $projectSlug [description]
     * @return [type]              [description]
     *
     * @category Utilities
     */
    private function versions($projectSlug)
    {
        $projects = $this->projects();
        if (count($this->versions) == 0 && isset($projects[$projectSlug])) {
            return $projects[$projectSlug];

        }
        return [];
    }
}
