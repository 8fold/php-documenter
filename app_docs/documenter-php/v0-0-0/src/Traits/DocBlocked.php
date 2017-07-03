<?php

namespace Eightfold\DocumenterPhp\Traits;

trait DocBlocked
{
    private $docBlock = null;

    /**
     * [category description]
     * @return [type] [description]
     *
     * @category Utilities
     */
    public function category()
    {
        if (!is_null($this->docBlock()) && $this->docBlock()->hasTag('category')) {
            $category = $this->docBlock()->getTagsByName('category');

            // always use the first one.
            $category = $category[0];

            // always use the short description for categories.
            return $category->getDescription();
        }
        return null;
    }

    /**
     * [docBlock description]
     * @return [type] [description]
     *
     * @category Utilities
     */
    public function docBlock()
    {
        if (is_null($this->docBlock)) {
            if (is_a($this, Parameter::class)) {
                $this->docBlock = $this->method->docBlock();

            } else {
                $this->docBlock = $this->reflector->getDocBlock();

            }
        }
        return $this->docBlock;
    }
}
