<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use phpDocumentor\Reflection\ClassReflector;

use Eightfold\DocumenterPhp\Project;

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

    private $project = null;

    private $reflector = null;

    private $interfaces = [];

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

    private function parentName()
    {
        $parts = explode('\\', $this->parentFullName());
        $name = array_pop($parts);
        if (is_null($this->project->objectWithFullName($this->parentFullName()))) {
            return '['. $name .']';
        }
        return $name;
    }

    private function parentFullName()
    {
        return $this->getParentClass();
    }

    private function interfaces()
    {
        if (count($this->interfaces) == 0) {
            $this->interfaces = $this->getInterfaces();
        }
        return $this->interfaces;
    }

    private function interfacesString()
    {
        return implode(', ', $this->interfaces());
    }

    public function largeDeclaration()
    {
        $build = [];
        $build[] = 'class '. $this->name;
        if (strlen($this->parentFullName()) > 0) {
            $build[] = 'extends '. $this->parentName();
        }
        $build[] = 'implements '. $this->interfacesString;
        return implode(' ', $build);
    }
}
