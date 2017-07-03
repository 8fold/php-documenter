<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use phpDocumentor\Reflection\TraitReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;

/**
 * @category Project object
 */
class Trait_ extends TraitReflector
{
    use Gettable,
        Namespaced;

    private $reflector = null;

    public function __construct(Project $project, TraitReflector $reflector)
    {
        $this->reflector = $reflector;
        // $this->project = $project;
        // $this->node = $this->reflector->getNode();
    }
}
