<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use phpDocumentor\Reflection\ClassReflector\PropertyReflector;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

/**
 * @category Symbols
 */
class Property extends PropertyReflector
{
    use Gettable,
        DocBlocked;

    private $class = null;

    private $reflector = null;

    public function __construct(Class_ $class, PropertyReflector $reflector)
    {
        $this->class = $class;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }
}
