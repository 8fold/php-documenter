<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use phpDocumentor\Reflection\InterfaceReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

/**
 * Represents an `interface` in a project.
 *
 * @category Project object
 */
class Interface_ extends InterfaceReflector
{
    use Gettable,
        Namespaced,
        DocBlocked;

    private $reflector = null;

    private $project = null;

    public function __construct(Project $project, InterfaceReflector $reflector)
    {
        $this->project = $project;
        $this->reflector = $reflector;

        // Setting `node` on InterfaceReflector
        $this->node = $this->reflector->getNode();
    }

    // public function methods()
    // {
    //     return array_values($this->reflector->methods);
    // }

    // public function namespaceName()
    // {
    //     $parts = explode('\\', $this->longName());
    //     array_pop($parts);
    //     return implode('\\', $parts);
    // }
}
