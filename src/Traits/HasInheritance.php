<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;
use Eightfold\DocumenterPhp\ClassExternal;

trait HasInheritance
{
    private $parent = null;

    /**
     * [parent description]
     * @return [type] [description]
     *
     * @category Get parent class
     */
    public function parent()
    {
        if (is_null($this->parent)) {
            $parentNamespace = '';
            if (static::class == Class_::class) {
                $parentNamespace = $this->reflector->getParentClass();

            } elseif (static::class == Interface_::class) {
                $parentNamespace = $this->reflector->getParentInterfaces();

            } elseif (isset($this->class)) {
                $parentNamespace = '\\'. $this->class->fullName;

            }

            if (strlen($parentNamespace) == 0) {
                return null;
            }

            if ($parent = $this->project->objectWithFullName($parentNamespace)) {
                $this->parent = $parent;

            } else {
                $parts = explode('\\', $parentNamespace);
                $this->parent = new ClassExternal($parts);

            }
        }
        return $this->parent;
    }

    public function parentDefinitionList()
    {
        $listItems = [];
        if ($this->parent()->isInProjectSpace) {
            $listItems[] = [
                'element' => 'dt',
                'config' => [
                    'content' => $this->parent->smallDeclaration
                ]
            ];

            $listItems[] = [
                'element' => 'dd',
                'config' => [
                    'content' => $this->parent->shortDescription
                ]
            ];

        } else {
            $listItems[] = [
                'element' => 'dt',
                'config' => [
                    'content' => $this->parent->space() .'\\'. $this->parent->name
                ]
            ];

            $listItems[] = [
                'element' => 'dd',
                'config' => [
                    'content' => 'Note: The parent is not within the scope of this codebase.'
                ]
            ];
        }

        return Html5Gen::dl([
                'content' => $listItems
            ]);
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

    /**
     * [parentName description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function parentName()
    {
        return $this->nameStringFromFullName($this->parentFullName());
    }

    /**
     * [parentFullName description]
     * @return [type] [description]
     *
     * @category Strings
     */
    private function parentFullName()
    {
        return $this->getParentClass();
    }
}
