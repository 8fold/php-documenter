<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;

use Eightfold\DocumenterPhp\ProjectObjects\Method;
use Eightfold\DocumenterPhp\ProjectObjects\Property;

trait HasSymbols
{
    abstract public function symbolsCategorized();

    /**
     * [symbolWithName description]
     * @param  [type] $instanceMethod [description]
     * @param  [type] $name           [description]
     * @return [type]                 [description]
     *
     * @category Get symbols for class
     */
    public function symbolWithName($instanceMethod, $name)
    {
        foreach ($this->$instanceMethod() as $symbol) {
            if ($symbol->name == $name) {
                return $symbol;
            }
        }
        return null;
    }

    /**
     * [symbolsForProperty description]
     * @param  [type] $instanceProperty    [description]
     * @param  [type] $classToInstantiate  [description]
     * @param  [type] $reflectorMethodName [description]
     * @return [type]                      [description]
     *
     * @category Get symbols for class
     */
    private function symbolsForProperty($instanceProperty, $classToInstantiate, $reflectorMethodName)
    {
        if (count($this->{$instanceProperty}) == 0 && count($this->reflector->$reflectorMethodName()) > 0) {
            $return = [];
            foreach ($this->reflector->$reflectorMethodName() as $reflector) {
                $return[] = new $classToInstantiate($this, $reflector);
            }
            $this->{$instanceProperty} = $return;
        }
        return $this->{$instanceProperty};
    }

    /**
     * [getCategorized description]
     *
     * @param  [type] $propertyName [description]
     * @param  [type] $symbols      [description]
     * @param  string $symbolType   [description]
     * @return [type]               [description]
     *
     * @category Get symbols for class
     */
    private function getCategorized($propertyName, $symbols, $symbolType = 'methods')
    {
        if (count($this->{$propertyName}) == 0) {
            $build = [];
            foreach ($symbols as $symbol) {
                $category = '';
                if ($symbol->name() == '__construct') {
                    $category = 'Initializer';

                } elseif (strlen($symbol->category()) > 0) {
                    $category = $symbol->category();

                } else {
                    $category = 'NO_CATEGORY';

                }

                $accessAndType = '';
                if ($symbol->reflector->isStatic() && $access = $symbol->reflector->getVisibility()) {
                    $accessAndType = 'static_'. $access;

                } else {
                    // Default is public
                    $accessAndType = $symbol->reflector->getVisibility();;

                }

                $build[$category][$symbolType][$accessAndType][$symbol->name()] = $symbol;
            }

            // Sort symbols alphabetically by name.
            foreach ($build as $category => $accessLevels) {
                foreach ($accessLevels as $access => $symbolTypes) {
                    foreach ($symbolTypes as $symbolType => $symbols) {
                        ksort($symbols);
                        $build[$category][$access][$symbolType] = $symbols;
                    }
                }
            }
            $this->{$propertyName} = $build;
        }
        return $this->{$propertyName};
    }
}
