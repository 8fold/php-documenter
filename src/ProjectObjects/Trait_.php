<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\TraitReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasSymbols;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;
use Eightfold\DocumenterPhp\Traits\HasMethods;
use Eightfold\DocumenterPhp\Traits\HasProperties;
use Eightfold\DocumenterPhp\Traits\HasObjects;
use Eightfold\DocumenterPhp\Traits\HasClassDefinitionsList;
use Eightfold\DocumenterPhp\Traits\HasInheritance;
use Eightfold\DocumenterPhp\Traits\HasTraitDeclarations;

/**
 * @category Project object
 */
class Trait_ extends TraitReflector
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

    static private $urlProjectObjectName = 'traits';

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
        return array_merge_recursive($this->propertiesCategorized(), $this->methodsCategorized());
    }
}
