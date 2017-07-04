<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\Parameter;

trait DocBlocked
{
    private $docBlock = null;

    static private function paramTagsForDocBlock($docBlock)
    {
        return $docBlock->getTagsByName('param');
    }

    static private function paramTagForVariableName($name, $docBlock)
    {
        $paramTag = null;
        foreach ($docBlock->getTagsByName('param') as $param) {
            if ($param->getVariableName() == $name) {
                $paramTag = $param;
                break;
            }
        }
        return $paramTag;
    }

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
