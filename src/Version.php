<?php

namespace Eightfold\DocumenterPhp;

// // Project paths
// use \DirectoryIterator;

// Files for project
use \RecursiveDirectoryIterator;
use \RecursiveCallbackFilterIterator;
use \FilesystemIterator;

use Eightfold\DocumenterPhp\Helpers\StringHelpers;

// use Eightfold\DocumenterPhp\File;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;

use Eightfold\Documenter\Php\Method;
// use Eightfold\Documenter\Php\Property;

class Version
{
    use Gettable,
        DefinesSymbols;

    private $project = null;

    private $slug = '';

    private $root = '';

    private $ignore = [];

    private $files = [];

    private $classes = [];

    private $classesCategorized = [];

    private $traits = [];

    private $traitsCategorized = [];

    private $interfaces = [];

    private $interfacesCategorized = [];

    public function __construct($project, $slug, $root = 'src', $ignore = [])
    {
        $this->project = $project;
        $this->slug = $slug;
        $this->root = $root;
        $this->ignore = $ignore;
    }

    public function slug()
    {
        return $this->slug;
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
        return $this->project->url .'/'. $this->slug;
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
                $namespaceSlug = StringHelpers::namespaceToSlug($file->getNamespace());
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
     * [traits description]
     * @return [type] [description]
     *
     * @category Get objects
     */
    public function traits()
    {
        return $this->objectsForPropertyName('traits', Trait_::class, 'getTraits');
    }

    public function traitsCategorized()
    {
        return $this->objectsOrdered($this->traits(), 'traitsCategorized');
    }

    /**
     * @category Get objects
     *
     * @return [type] [description]
     */
    public function interfaces()
    {
        return $this->objectsForPropertyName('interfaces', Interface_::class, 'getInterfaces');
    }

    public function interfacesCategorized()
    {
        return $this->objectsOrdered($this->interfaces(), 'interfacesCategorized');
    }

    /**
     * Factory method that returns instantiated project object with given full name.
     *
     * For example, if we have a class named `Hello` in namespace `Vendor\World`,
     * passing `\Vendor\World\Hello` would result in an instance of Class_; thereby,
     * giving you access to all the details for that class. Further, if we has a trait
     * with the same name, the result would be an instance of Trait_.
     *
     * @param  [type] $fullName [description]
     *
     * @return \Eightfold\DocumenterPhp\ProjectObjects\Class_|\Eightfold\DocumenterPhp\ProjectObjects\Trait_|\Eightfold\DocumenterPhp\ProjectObjects\Interface_  Instance of project object
     *
     * @category Get objects
     */
    public function objectWithFullName($fullName)
    {
        if ($class = $this->objectWithFullNameFromObjects($fullName, $this->classes())) {
            return $class;
        }

        if ($interface = $this->objectWithFullNameFromObjects($fullName, $this->interfaces())) {
            return $interface;
        }

        if ($trait = $this->objectWithFullNameFromObjects($fullName, $this->traits())) {
            return $trait;
        }

        return null;
    }

    public function objectWithPath($path)
    {
        $replacements = [
            '/classes/'    => '-',
            '/traits/'     => '-',
            '/interfaces/' => '-',
            '/properties/' => '-',
            '/methods/'    => '-',
            '/'            => '-'
        ];
        $replace = array_keys($replacements);
        $with = array_values($replacements);
        $slug = str_replace($replace, $with, $path);
        return $this->objectWithFullName($slug);
    }

    private function objectWithFullNameFromObjects($fullName, $objects)
    {
        $fullNameSlug = StringHelpers::namespaceToSlug($fullName);
        if (isset($objects[$fullNameSlug])) {
            return $objects[$fullNameSlug];
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
                            $key = StringHelpers::namespaceToSlug($object->fullName);

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
                if (get_class($symbol) == Class_::class || get_class($symbol)  == Method::class) {
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

    protected function definesSymbolsDefaultConfig()
    {
        return [
            'symbolOrder' => [
                'abstract',
                'concrete',
                'NO_TYPE'
            ]
        ];
    }

    protected function processSymbolTypeForCategory($category, $symbols, $symbolType, $config, &$return)
    {
        $return[] = $this->processSymbolsDefinitionForCategory($category, $symbols, $config);
    }
}
