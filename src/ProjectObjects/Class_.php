<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

use phpDocumentor\Reflection\ClassReflector;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ClassExternal;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\ClassMethod;
use Eightfold\DocumenterPhp\ProjectObjects\Property;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\Sluggable;

/**
 * Represents a `class` in a project.
 *
 * @category Project object
 */
class Class_ extends ClassReflector
{
    use Gettable,
        Namespaced,
        DocBlocked,
        Sluggable;

    static private $urlProjectObjectName = 'classes';

    private $project = null;

    private $reflector = null;

    private $interfaces = [];

    protected $traits = [];

    private $_properties = [];

    protected $propertiesCategorized = [];

    private $_methods = [];

    protected $methodsCategorized = [];

    public function __construct(Project $project, ClassReflector $reflector)
    {
        $this->project = $project;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function project()
    {
        return $this->project;
    }

    public function isAbstract()
    {
        return $this->reflector->getNode()->isAbstract();
    }

    public function isInProjectSpace()
    {
        return true;
    }

    public function parent()
    {
        $extends = $this->node->extends;
        if (is_null($extends)) {
            return null;
        }

        $parentNamespace = implode('\\', $extends->parts);
        if ($parentClass = $this->project->objectWithFullName($parentNamespace)) {
            return $parentClass;
        }
        return new ClassExternal($extends->parts);
    }

    public function inheritance()
    {
        return $this->parentRecursive($this);
    }

    private function parentRecursive($object, $objects = [])
    {
        $objects[] = $object;
        $parent = $object->parent();
        if (!is_null($parent)) {
            return $this->parentRecursive($parent, $objects);
        }
        return array_reverse($objects);
    }

    private function interfaces()
    {
        return $this->objectsForPropertyName('interfaces', Interface_::class, $this->getInterfaces());
    }

    public function traits()
    {
        return $this->objectsForPropertyName('traits', Trait_::class, $this->reflector->getTraits());
    }

    public function properties()
    {
        return $this->symbolsForProperty('_properties', Property::class, 'getProperties');
    }

    private function propertiesCategorized()
    {
        return $this->getCategorized('propertiesCategorized', $this->properties(), 'properties');
    }

    public function propertyWithName($name)
    {
        return $this->symbolWithName('properties', $name);
    }

    public function propertyWithSlug($slugName)
    {
        return $this->objectWithSlug($slugName, $this->properties());
    }

    public function methods()
    {
        return $this->symbolsForProperty('_methods', ClassMethod::class, 'getMethods');
    }

    private function methodsCategorized()
    {
        return $this->getCategorized('methodsCategorized', $this->methods());
    }

    public function methodWithName($name)
    {
        return $this->symbolWithName('methods', $name);
    }

    public function methodWithSlug($slugName)
    {
        return $this->objectWithSlug($slugName, $this->methods());
    }

    private function objectWithSlug($slugName, $objects)
    {
        foreach ($objects as $object) {
            if ($object->slug == $slugName) {
                return $object;
            }
        }
        return null;
    }

    public function symbolsCategorized()
    {
        return array_merge_recursive($this->propertiesCategorized(), $this->methodsCategorized());
    }

    private function symbolsForProperty($instanceProperty, $classToInstantiate, $reflectorMethodName)
    {
        if (count($this->{$instanceProperty}) == 0 && count($this->reflector->$reflectorMethodName()) > 0) {
            $return = [];
            foreach ($this->reflector->$reflectorMethodName() as $reflector) {
                $return[] = new $classToInstantiate($this, $reflector);
            }
            $this->{$instanceProperty} = $return;
        }
        return $this->{$instanceProperty};
    }

    /**
     * [getCategorized description]
     *
     * @param  [type] $propertyName [description]
     * @param  [type] $symbols      [description]
     * @param  string $symbolType   [description]
     * @return [type]               [description]
     */
    private function getCategorized($propertyName, $symbols, $symbolType = 'methods')
    {
        if (count($this->{$propertyName}) == 0) {
            $staticPublic = 0;
            $staticProtected = 0;
            $staticPrivate = 0;
            $public = 0;
            $protected = 0;
            $private = 0;
            $build = [];
            foreach ($symbols as $symbol) {
                $category = (strlen($symbol->category()) > 0)
                    ? $symbol->category()
                    : 'NO_CATEGORY';
                $accessAndType = '';
                if ($this->symbolIsStatic($symbol) && $this->symbolIsPublic($symbol)) {
                    $accessAndType = 'static_public';
                    $staticPublic++;

                } elseif ($this->symbolIsStatic($symbol) && $this->symbolIsProtected($symbol)) {
                    $accessAndType = 'static_protected';
                    $staticProtected++;

                } elseif ($this->symbolIsStatic($symbol) && $this->symbolIsPrivate($symbol)) {
                    $accessAndType = 'static_private';
                    $staticPrivate++;

                } elseif ($this->symbolIsProtected($symbol)) {
                    $accessAndType = 'protected';
                    $protected++;

                } elseif ($this->symbolIsPrivate($symbol)) {
                    $accessAndType = 'private';
                    $private++;

                } else {
                    $accessAndType = 'public';
                    $public++;

                }
                $build[$category][$symbolType][$accessAndType][$symbol->name()] = $symbol;
            }

            if ($public == 0 && $protected == 0 && $private == 0 && $staticPublic == 0 && $staticProtected == 0 && $staticPrivate == 0) {
                $this->{$propertyName} = [];

            } else {
                foreach ($build as $category => $accessLevels) {
                    foreach ($accessLevels as $access => $symbolTypes) {
                        foreach ($symbolTypes as $symbolType => $symbols);
                        ksort($symbols);
                        $build[$category][$access][$symbolType] = $symbols;

                    }
                }
                $this->{$propertyName} = $build;

            }
        }
        return $this->{$propertyName};
    }

    private function symbolIsStatic($symbol)
    {
        return $this->symbolIs($symbol, 'isStatic');
    }

    private function symbolIsPublic($symbol)
    {
        return $this->symbolIs($symbol, 'isPublic');
    }

    private function symbolIsProtected($symbol)
    {
        return $this->symbolIs($symbol, 'isProtected');
    }

    private function symbolIsPrivate($symbol)
    {
        return $this->symbolIs($symbol, 'isPrivate');
    }

    private function symbolIs($symbol, $functionName)
    {
        // dump($functionName);
        // dump($symbol);
        return (method_exists($symbol, $functionName) && $symbol->{$functionName}());
    }

    public function symbolWithName($instanceMethod, $name)
    {
        foreach ($this->$instanceMethod() as $symbol) {
            if ($symbol->name == $name) {
                return $symbol;
            }
        }
        return null;
    }

    /**
     * A definition list of the symbols for the class.
     *
     * `$config` is an optional dictionary with the following optional keys.
     *
     * - **label:**          Placed before the definition list in the HTML string.
     *                       Defaults to the category name.
     * - **labelWrapper:**   HTML element to wrap the label in. Defaults to h2.
     * - **skipCategories:** By default all categories will be processed.
     *                       `skipCategories` is an array of categories to *not*
     *                       process.
     * - **onlyCategories:** By default all categories will be processed.
     *                       `onlyCategories` is an array of categories to process.
     *                       Note: If the same category is in both `onlyCategories`
     *                       and `skipCategories`, the category will be processed.
     * - **symboldOrder:**   Array defining what order to display the symbols. All the
     *                       following must be present (default order): properties, and
     *                       methods.
     * - **accessOrder:**    Array defining what order to display access orders. All
     *                       the following must present (default order): public,
     *                       protected, private, static_public, static_protected, and
     *                       static_private.
     *
     * @param  array $config   [description]
     * @return [type]               [description]
     */
    public function symbolDefinitions($config = [])
    {
        $default = [
            'symbolOrder' => [
                'properties',
                'methods'
            ],
            'accessOrder' => [
                'public',
                'protected',
                'private',
                'static_public',
                'static_protected',
                'static_private'
            ],
            'onlyCategories' => [],
            'skipCategories' => [],
            'labelWrapper' => 'h2'
        ];
        $config = array_merge($default, $config);

        $return = [];
        $symbols = $this->symbolsCategorized();
        foreach ($symbols as $category => $accessLevels) {
            $process = $this->shouldProcessSymbolDefinitionForCategory($category, $config);

            if ($process) {
                $symbolOrder = $config['symbolOrder'];
                $accessOrder = $config['accessOrder'];
                // Both are three levels deep
                // generic = [category][key1][key2]
                // Class_ = [category][concrete/abstract][classname]
                // Symbol = [category][symboltype][access]
                foreach ($symbolOrder as $symbolType) {
                    foreach ($accessOrder as $access) {
                        $inArray = isset($symbols[$category][$symbolType][$access]);
                        if ($inArray) {
                            $symbols = $symbols[$category][$symbolType][$access];
                            $return[] = $this->processSymbolsDefinitionForCategory($category, $symbols, $config);
                        }
                    }
                }
            }
        }
        return implode("\n\n", $return);
    }

    private function shouldProcessSymbolDefinitionForCategory($category, $config)
    {
        $onlyCategories = $config['onlyCategories'];
        $hasOnly = (count($onlyCategories) > 0);
        $inOnly = in_array($category, $onlyCategories);
        // print($category);
        // print($hasOnly);
        // print($inOnly);

        if ($hasOnly && $inOnly) {
            // print('should process');
            return true;
        }

        $skippedCategories = $config['skipCategories'];
        $hasSkipped = (count($skippedCategories) > 0);
        $inSkipped = in_array($category, $skippedCategories);
        // print($hasSkipped);
        // print($inSkipped);

        if (($hasOnly && !$inSkipped) || ($hasSkipped && $inSkipped)) {
            // print('skipping');
            return false;
        }
        // print('default process');
        return true;
    }

    private function processSymbolsDefinitionForCategory($category, $symbols, $config)
    {
        foreach ($symbols as $slug => $symbol) {
            $termContent = $symbol->largeDeclaration;
            if ($symbol->isDeprecated) {
                $termContent = [
                    'element' => 'del',
                    'config' => ['content' => $termContent]
                ];
            }

            $categoryContent[] = [
                'element' => 'dt',
                'config' => ['content' => $termContent]
            ];

            $description = $symbol->shortDescription;
            if ($symbol->isDeprecated) {
                $description = $symbol->deprecatedDescription;
            }
            $converter = new CommonMarkConverter();
            $description = $converter->convertToHtml($description);

            if (strlen($description) > 0) {
                $categoryContent[] = [
                    'element' => 'dd',
                    'config' => ['content' => $description]
                ];

            }
        }
                // }
            // }
        // }
        $list = Html5Gen::dl(['content' => $categoryContent]);

        $labelWrapper = $config['labelWrapper'];
        $labelText = (isset($config['label']))
            ? $config['label']
            : ($category == 'NO_CATEGORY')
                ? 'Miscellaneous'
                : $category;
        $label = Html5Gen::$labelWrapper([
                'content' => $labelText
            ]);

        return $label ."\n\n". $list;
    }

    /**
     * [parentName description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function parentName()
    {
        return $this->nameStringFromFullName($this->parentFullName());
    }

    /**
     * [parentFullName description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function parentFullName()
    {
        return $this->getParentClass();
    }

    /**
     * [interfaceNames description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function interfaceNames()
    {
        return $this->objectDisplayNames($this->interfaces());
    }

    /**
     * [traitNames description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function traitNames()
    {
        return $this->objectDisplayNames($this->traits());
    }

    /**
     * [objectDisplayNames description]
     * @param  [type] $objects [description]
     * @return [type]          [description]
     *
     * @category Strings
     */
    private function objectDisplayNames($objects)
    {
        $nameStrings = [];
        foreach ($objects as $object) {
            $nameStrings[] = $this->nameStringFromFullName($object->fullName)
;        }
        return implode(', ', $nameStrings);
    }

    /**
     * Displays the most complete representation of the Class definition.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *     implements [class-interfaces]
     *     has traits [class-traits]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->displayNameString($asHtml, $build);
        $this->inheritanceString($asHtml, $build);
        $this->interfacesString($asHtml, $build);
        $this->traitsString($asHtml, $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => implode(' ',$build),
                'href' => $this->url()
            ]);
        }
        return implode(' ', $build);
    }

    /**
     * Displays complete representation of the Class definition less traits.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *     implements [class-interfaces]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->displayNameString($asHtml, $build);
        $this->inheritanceString($asHtml, $build);
        $this->interfacesString($asHtml, $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => implode(' ',$build),
                'href' => $this->url()
            ]);
        }
        return implode(' ', $build);
    }

    /**
     * Displays complete representation of the Class definition less interfaces and
     * traits.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->displayNameString($asHtml, $build);
        $this->inheritanceString($asHtml, $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => implode(' ',$build),
                'href' => $this->url()
            ]);
        }
        return implode(' ', $build);
    }

    /**
     * Displays only class name.
     *
     * Ex. class [class-name]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->displayNameString($asHtml, $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => implode(' ',$build),
                'href' => $this->url()
            ]);
        }
        return implode(' ', $build);
    }

    /**
     * Displays only class name and optionally removed "class" keyword.
     *
     * Ex. class [class-name]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function microDeclaration($asHtml = true, $withLink = true, $showKeyword = true)
    {
        $string = $this->miniDeclaration($asHtml, $withLink);
        if ($showKeyword) {
            return $string;
        }
        return str_replace('class ', '', $string);
    }

    /**
     * [inheritanceString description]
     * @param  [type] $asHtml [description]
     * @param  [type] &$build [description]
     * @return [type]         [description]
     *
     * @category Strings
     */
    private function inheritanceString($asHtml, &$build)
    {
        if (strlen($this->parentName()) > 0) {
            $string = $this->relatedString($asHtml, $this->parentName());
            $build[] = StringHelpers::displayString($asHtml, $string, 'extends');
        }
    }

    /**
     * [interfacesString description]
     * @param  [type] $asHtml [description]
     * @param  [type] &$build [description]
     * @return [type]         [description]
     *
     * @category Strings
     */
    private function interfacesString($asHtml, &$build)
    {
        if (strlen($this->interfaceNames) > 0) {
            $string = $this->relatedString($asHtml, $this->interfaceNames());
            $build[] = StringHelpers::displayString($asHtml, $string, 'implements', 'implements-label');
        }
    }

    /**
     * [traitsString description]
     * @param  [type] $asHtml [description]
     * @param  [type] &$build [description]
     * @return [type]         [description]
     *
     * @category Strings
     */
    private function traitsString($asHtml, &$build)
    {
        if (strlen($this->traitNames()) > 0) {
            $keyword = (count($this->traits) == 1)
                ? 'has trait'
                : 'has traits';

            $traits = $this->relatedString($asHtml, $this->traitNames());

            $build[] = StringHelpers::displayString($asHtml, $traits, $keyword, 'traits-label');
        }
    }

    /**
     * [relatedString description]
     * @param  [type] $asHtml     [description]
     * @param  [type] $baseString [description]
     * @return [type]             [description]
     *
     * @category Strings
     */
    private function relatedString($asHtml, $baseString)
    {
        $string = $baseString;
        if ($asHtml) {
            $bases = explode(', ', $baseString);
            $htmlStrings = [];
            foreach ($bases as $base) {
                $htmlStrings[] = Html5Gen::span([
                    'content' => $base,
                    'class' => 'related'
                    ]);
            }
            $string = implode(', ', $htmlStrings);
        }
        return $string;
    }

    /**
     * [nameStringFromFullName description]
     * @param  [type] $fullName [description]
     * @return [type]           [description]
     *
     * @category Strings
     */
    private function nameStringFromFullName($fullName)
    {
        $parts = explode('\\', $fullName);
        $name = array_pop($parts);
        if (strlen($this->getParentClass()) > 0 && is_null($this->project->objectWithFullName($fullName))) {
            return '['. $name .']';

        } elseif (strlen($name) > 0) {
            return $name;

        }
        return '';
    }

    /**
     * [objectsForPropertyName description]
     * @param  [type] $instanceProperty        [description]
     * @param  [type] $classToInstantiate      [description]
     * @param  [type] $fileReflectorMethodName [description]
     * @return [type]                          [description]
     *
     * @category Utilities
     */
    private function objectsForPropertyName($instanceProperty, $class, $objectFullNames)
    {
        if (count($objectFullNames) == 0) {
            return [];
        }

        if (count($this->{$instanceProperty}) == 0) {
            $objects = [];
            foreach ($objectFullNames as $objectFullName) {
                $object = $this->project->objectWithFullName($objectFullName);
                if (!is_null($object) && is_a($object, $class)) {
                    $objects[] = $object;

                }
            }
            $this->{$instanceProperty} = $objects;
        }
        return $this->{$instanceProperty};
    }
}
