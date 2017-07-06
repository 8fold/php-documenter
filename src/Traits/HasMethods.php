<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\ClassMethod;

trait HasMethods
{
    private $_methods = [];

    protected $methodsCategorized = [];

    /**
     * [methods description]
     * @return [type] [description]
     *
     * @category Get methods for class
     */
    public function methods()
    {
        return $this->symbolsForProperty('_methods', ClassMethod::class, 'getMethods');
    }

    /**
     * [methodsCategorized description]
     * @return [type] [description]
     *
     * @category Get methods for class
     */
    private function methodsCategorized()
    {
        return $this->getCategorized('methodsCategorized', $this->methods());
    }

    /**
     * [methodWithName description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     *
     * @category Get methods for class
     */
    public function methodWithName($name)
    {
        return $this->symbolWithName('methods', $name);
    }

    /**
     * [methodWithSlug description]
     * @param  [type] $slugName [description]
     * @return [type]           [description]
     *
     * @category Get methods for class
     */
    public function methodWithSlug($slugName)
    {
        return $this->objectWithSlug($slugName, $this->methods());
    }
}
