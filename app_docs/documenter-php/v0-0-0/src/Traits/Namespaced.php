<?php

namespace Eightfold\DocumenterPhp\Traits;

trait Namespaced
{
    private $namespaceParts = [];

    private $url = '';

    private function namespaceParts()
    {
        if (count($this->namespaceParts) == 0) {
            $this->namespaceParts = $this->reflector->getNode()->namespacedName->parts;
        }
        return $this->namespaceParts;
    }

    public function fullName()
    {
        return implode('\\', $this->namespaceParts());
    }

    public function space()
    {
        $parts = $this->namespaceParts();
        array_pop($parts);
        return implode('\\', $parts);
    }

    public function name()
    {
        return $this->reflector->getShortName();
    }

    public function url()
    {
        if (strlen($this->url) == 0) {
            $slugged = [];
            foreach ($this->namespaceParts() as $part) {
                $slugged[] = kebab_case($part);
            }
            array_shift($slugged);
            array_shift($slugged);
            $thisPath = implode('/', $slugged);
            $this->url = $this->project->url() .'/'. $thisPath;
        }
        return $this->url;
    }
}
