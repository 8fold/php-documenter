<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;

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

    public function traitDefinitionList()
    {
        $listItems = [];
        foreach ($this->traits as $trait) {
            $listItems[] = [
                'element' => 'dt',
                'config' => ['content' => $trait->mediumDeclaration]
            ];

            $listItems[] = [
                'element' => 'dd',
                'config' => ['content' => $trait->shortDescription]
            ];
        }

        return Html5Gen::dl([
                'content' => $listItems
            ]);
    }
}
