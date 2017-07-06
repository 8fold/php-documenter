<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

use phpDocumentor\Reflection\ClassReflector;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ClassExternal;
// use Eightfold\DocumenterPhp\ProjectObjects\Interface_;
// use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
// use Eightfold\DocumenterPhp\ProjectObjects\ClassMethod;
// use Eightfold\DocumenterPhp\ProjectObjects\Property;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasSymbols;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;
use Eightfold\DocumenterPhp\Traits\HasMethods;
use Eightfold\DocumenterPhp\Traits\HasProperties;
use Eightfold\DocumenterPhp\Traits\HasObjects;

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
        HasSymbols,
        DefinesSymbols,
        HasMethods,
        HasProperties;

    static private $urlProjectObjectName = 'classes';

    private $project = null;

    private $reflector = null;

    public function __construct(Project $project, ClassReflector $reflector)
    {
        $this->project = $project;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function isAbstract()
    {
        return $this->reflector->getNode()->isAbstract();
    }

    public function isInProjectSpace()
    {
        return true;
    }

    /**
     * [project description]
     * @return [type] [description]
     *
     * @category Get project
     */
    public function project()
    {
        return $this->project;
    }

    /**
     * [parent description]
     * @return [type] [description]
     *
     * @category Get parent class
     */
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

    /**
     * [inheritance description]
     * @return [type] [description]
     *
     * @category Get parent class
     */
    public function inheritance()
    {
        return $this->parentRecursive($this);
    }

    /**
     * [parentRecursive description]
     * @param  [type] $object  [description]
     * @param  array  $objects [description]
     * @return [type]          [description]
     *
     * @category Get parent class
     */
    private function parentRecursive($object, $objects = [])
    {
        $objects[] = $object;
        $parent = $object->parent();
        if (!is_null($parent)) {
            return $this->parentRecursive($parent, $objects);
        }
        return array_reverse($objects);
    }

    /**
     * [symbolsCategorized description]
     * @return [type] [description]
     *
     * @category Get symbols for class
     */
    public function symbolsCategorized()
    {
        return array_merge_recursive($this->propertiesCategorized(), $this->methodsCategorized());
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
     * @category Declarations
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
     * @category Declarations
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
     * @category Declarations
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
     * @category Declarations
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
     * @category Declarations
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
     * [definesSymbolsDefaultConfig description]
     * @return [type] [description]
     *
     */
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

    /**
     * [processSymbolTypeForCategory description]
     * @param  [type] $category   [description]
     * @param  [type] $symbols    [description]
     * @param  [type] $symbolType [description]
     * @param  [type] $config     [description]
     * @param  [type] &$return    [description]
     * @return [type]             [description]
     */
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
