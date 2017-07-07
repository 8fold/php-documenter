<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

use phpDocumentor\Reflection\ClassReflector;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ClassExternal;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasSymbols;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;
use Eightfold\DocumenterPhp\Traits\HasMethods;
use Eightfold\DocumenterPhp\Traits\HasProperties;
use Eightfold\DocumenterPhp\Traits\HasTraits;
use Eightfold\DocumenterPhp\Traits\HasObjects;
use Eightfold\DocumenterPhp\Traits\HasClassDefinitionsList;
use Eightfold\DocumenterPhp\Traits\HasInheritance;

/**
 * Represents a `class` in a project.
 *
 * @category Project object
 */
class Class_ extends ClassReflector implements HasDeclarations
{
    use Gettable,
        Namespaced,
        DocBlocked,
        Sluggable,
        HasSymbols,
        DefinesSymbols,
        HasMethods,
        HasProperties,
        HasTraits,
        HasObjects,
        HasClassDefinitionsList,
        HasInheritance;

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
}
