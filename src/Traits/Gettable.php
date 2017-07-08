<?php

namespace Eightfold\DocumenterPhp\Traits;

trait Gettable
{
   /**
     * [__get description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     *
     * @category Magic methods
     */
    public function __get($name)
    {
        // $class = get_class($this);
        // print("\n\n". $class ."\n");
        // print($name ."\n");
        // print((method_exists($this, $name)) ? 'true' : 'false');

        // Exists with the same name as the one being evaluated. If it does, call
        // the method, set the instance variable, and then return the results.
        if (method_exists($this, $name)) {
            return $this->$name();

        }

        // Last ditch effort. Give it over to the class itself.
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . __CLASS__ .
            ' on line ' . __LINE__,
            E_USER_NOTICE);
        return null;
    }
}
