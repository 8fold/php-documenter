<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\Property;

trait HasProperties
{
    private $_properties = [];

    protected $propertiesCategorized = [];

    /**
     * [properties description]
     * @return [type] [description]
     *
     * @category Get properties for class
     */
    public function properties()
    {
        return $this->symbolsForProperty('_properties', Property::class, 'getProperties');
    }

    /**
     * [propertiesCategorized description]
     * @return [type] [description]
     *
     * @category Get properties for class
     */
    private function propertiesCategorized()
    {
        return $this->getCategorized('propertiesCategorized', $this->properties(), 'properties');
    }

    /**
     * [propertyWithName description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     *
     * @category Get properties for class
     */
    public function propertyWithName($name)
    {
        return $this->symbolWithName('properties', $name);
    }
}
