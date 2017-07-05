<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\Parameter;

trait DocBlocked
{
    private $docBlock = null;

    private $deprecatedDescription = '';

    static private function paramTagsForDocBlock($docBlock)
    {
        return $docBlock->getTagsByName('param');
    }

    static private function paramTagForVariableName($name, $docBlock)
    {
        if (is_null($docBlock)) {
            return null;
        }

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

    public function isDeprecated()
    {
        if (!is_null($this->docBlock()) && $this->docBlock()->hasTag('deprecated')) {
            return true;
        }
        return false;
    }

    public function deprecatedDescription()
    {
        if (strlen($this->deprecatedDescription) == 0 && $this->isDeprecated()) {
            $docTag = $this->docBlock()->getTagsByName('deprecated');
            $docTag = $docTag[0];
            $this->deprecatedDescription = $docTag->getDocBlock()->getShortDescription();
        }
        return $this->deprecatedDescription;
    }

    public function discussion()
    {
        if (!is_null($this->docBlock())) {
            return $this->docBlock()->getLongDescription();
        }
        return '';
    }

    public function shortDescription()
    {
        if (!is_null($this->docBlock())) {
            return $this->docBlock()->getShortDescription();
        }
        return '';
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
