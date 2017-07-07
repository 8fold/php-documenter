<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\Trait_;

trait HasTraits
{
    /**
     * [traits description]
     * @return [type] [description]
     *
     * @category Get traits for class
     */
    public function traits()
    {
        return $this->objectsForPropertyName('traits', Trait_::class, $this->reflector->getTraits());
    }
}
