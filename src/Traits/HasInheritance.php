<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\ClassExternal;

trait HasInheritance
{
    /**
     * [parent description]
     * @return [type] [description]
     *
     * @category Get parent class
     */
    public function parent()
    {
        $parentNamespace = $this->reflector->getParentClass();
        // $extends = $this->node->extends;
        if (strlen($parentNamespace) == 0) {
            return null;
        }

        // $parentNamespace = implode('\\', $extends->parts);
        if ($parentClass = $this->project->objectWithFullName($parentNamespace)) {
            return $parentClass;
        }
        $parts = explode('\\', $parentNamespace);
        return new ClassExternal($parts);
    }

    /**
     * [inheritance description]
     * @return [type] [description]
     *
     * @category Get parent class
     */
    public function inheritance()
    {
        return $this->parentRecursive($this);
    }

    /**
     * [parentRecursive description]
     * @param  [type] $object  [description]
     * @param  array  $objects [description]
     * @return [type]          [description]
     *
     * @category Get parent class
     */
    private function parentRecursive($object, $objects = [])
    {
        $objects[] = $object;
        $parent = $object->parent();
        if (!is_null($parent)) {
            return $this->parentRecursive($parent, $objects);
        }
        return array_reverse($objects);
    }
}
