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
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;

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
        Sluggable,
        DefinesSymbols;

    static private $urlProjectObjectName = 'classes';

    private $project = null;

    private $reflector = null;

    private $interfaces = [];

    protected $traits = [];

    private $_properties = [];

    protected $propertiesCategorized = [];

    private $_methods = [];

    protected $methodsCategorized = [];

    public $startedProcessingCategory = [];

    public $startedProcessingAccessLevel = [];

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
            $count = 0;
            $build = [];
            foreach ($symbols as $symbol) {
                $category = (strlen($symbol->category()) > 0)
                    ? $symbol->category()
                    : 'NO_CATEGORY';

                $accessAndType = '';
                if ($symbol->reflector->isStatic() && $access = $symbol->reflector->getVisibility()) {
                    $accessAndType = 'static_'. $access;
                    // $accessAndType = 'static_private';
                    $count++;

                } else {
                    $accessAndType = $symbol->reflector->getVisibility();;
                    $count++;

                }

                $build[$category][$symbolType][$accessAndType][$symbol->name()] = $symbol;
            }

            if ($count == 0) {
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
        return (method_exists($symbol, $functionName) && $symbol->reflector->{$functionName}());
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

    static protected function definesSymbolsDefaultConfig()
    {
        return [
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
            ]
        ];
    }

    protected function processSymbolTypeForCategory($category, $symbols, $symbolType, $config, &$return)
    {
        if (count($config['accessOrder']) > 0) {
            foreach ($config['accessOrder'] as $access) {
                if (isset($symbols[$access])) {
                    $symbolsToProcess = $symbols[$access];
                    $return[] = $this->processSymbolsDefinitionForCategory($category, $symbolsToProcess, $config);
                }
            }
        }
    }
}
