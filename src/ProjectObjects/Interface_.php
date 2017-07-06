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
        DocBlocked,
        Namespaced,
        Sluggable,
        HasSymbols,
        DefinesSymbols,
        HasMethods,
        HasProperties,
        HasObjects,
        HasClassDefinitionsList,
        HasInheritance,
        HasTraitDeclarations;

    static private $urlProjectObjectName = 'interfaces';

    private $project = null;

    private $reflector = null;

    public function __construct(Project $project, TraitReflector $reflector)
    {
        $this->project = $project;
        $this->reflector = $reflector;

        // Setting `node` on InterfaceReflector
        $this->node = $this->reflector->getNode();
    }

    public function isInProjectSpace()
    {
        return true;
    }

    /**
     * [symbolsCategorized description]
     * @return [type] [description]
     *
     * @category Get symbols for class
     */
    public function symbolsCategorized()
    {
        return $this->methodsCategorized();
    }
}
