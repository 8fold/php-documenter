<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;

use phpDocumentor\Reflection\ClassReflector\PropertyReflector;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\ClassSubObject;

/**
 * @category Symbols
 */
class Property extends PropertyReflector
{
    use Gettable,
        DocBlocked,
        ClassSubObject;

    static private $urlProjectObjectName = 'properties';

    public function __construct(Class_ $class, PropertyReflector $reflector)
    {
        $this->class = $class;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $build[] = '$'. $this->name;

        $build = implode(' ', $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $build,
                'href' => $this->url()
            ]);
        }
        return $build;
    }

    public function isStatic()
    {
        return $this->reflector->isStatic();
    }
}
