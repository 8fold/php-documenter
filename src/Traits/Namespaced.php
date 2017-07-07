<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\Helpers\StringHelpers;

trait Namespaced
{
    private $namespaceParts = [];

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

    /**
     * [classString description]
     * @param  [type] $asHtml [description]
     * @param  [type] &$build [description]
     * @return [type]         [description]
     *
     * @category Strings
     */
    private function displayNameString($asHtml, &$build, $keyword = 'class')
    {
        $build[] = StringHelpers::displayString($asHtml, $this->name, $keyword);
    }
}
