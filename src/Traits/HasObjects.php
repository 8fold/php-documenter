<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ProjectObjects\Interface_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;

trait HasObjects
{
    private $interfaces = [];

    /**
     * [interfaces description]
     * @return [type] [description]
     *
     * @category Get interfaces for class
     */
    private function interfaces()
    {
        return $this->objectsForPropertyName('interfaces', Interface_::class, $this->getInterfaces());
    }

    /**
     * [propertyWithSlug description]
     * @param  [type] $slugName [description]
     * @return [type]           [description]
     *
     * @category Get properties for class
     */
    // public function propertyWithSlug($slugName)
    // {
    //     return $this->objectWithSlug($slugName, $this->properties());
    // }

    private function objectWithSlug($slugName, $objects)
    {
        foreach ($objects as $object) {
            if ($object->slug == $slugName) {
                return $object;
            }
        }
        return null;
    }

    /**
     * [objectsForPropertyName description]
     * @param  [type] $instanceProperty        [description]
     * @param  [type] $classToInstantiate      [description]
     * @param  [type] $fileReflectorMethodName [description]
     * @return [type]                          [description]
     *
     * @category Get symbols for class
     */
    private function objectsForPropertyName($instanceProperty, $class, $objectFullNames)
    {
        if (count($objectFullNames) == 0) {
            return [];
        }

        if (count($this->{$instanceProperty}) == 0) {
            $objects = [];
            foreach ($objectFullNames as $objectFullName) {
                $object = $this->version->objectWithFullName($objectFullName);
                if (!is_null($object) && is_a($object, $class)) {
                    $objects[] = $object;

                }
            }
            $this->{$instanceProperty} = $objects;
        }
        return $this->{$instanceProperty};
    }
}
