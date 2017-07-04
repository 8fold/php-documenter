<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\ClassReflector;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\ClassMethod;
use Eightfold\DocumenterPhp\ProjectObjects\Property;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

// use Eightfold\Documenter\Php\Property;
// use Eightfold\Documenter\Php\Method;
// use Eightfold\Documenter\Php\DocBlock;

// use Eightfold\Documenter\Interfaces\HasDeclarations;

// use Eightfold\Documenter\Traits\HasInheritance;
// use Eightfold\Documenter\Traits\Nameable;
// use Eightfold\Documenter\Traits\Symbolic;
// use Eightfold\Documenter\Traits\DocBlockable;
// use Eightfold\Documenter\Traits\HighlightableString;
// use Eightfold\Documenter\Traits\CanBeAbstract;
// use Eightfold\Documenter\Traits\CanBeFinal;
// use Eightfold\Documenter\Traits\CanHaveTraits;

/**
 * Represents a `class` in a project.
 *
 * @category Project object
 */
class Class_ extends ClassReflector implements HasDeclarations
{
    use Gettable,
        Namespaced,
        DocBlocked;

    static private $urlProjectObjectName = 'classes';

    private $project = null;

    private $reflector = null;

    private $interfaces = [];

    protected $traits = [];

    private $_properties = [];

    private $_methods = [];

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

    public function propertyWithName($name)
    {
        return $this->symbolWithName('properties', $name);
    }

    public function methods()
    {
        return $this->symbolsForProperty('_methods', ClassMethod::class, 'getMethods');
    }

    public function methodWithName($name)
    {
        return $this->symbolWithName('methods', $name);
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
}
