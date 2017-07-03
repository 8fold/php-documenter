<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\DocumenterPhp\Helpers\StringHelpers;

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

    /**
     * [slug description]
     * @return [type] [description]
     *
     * @category Strings
     */
    public function slug()
    {
        return StringHelpers::slug($this->name);
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

    /**
     * Get the url for the Project Object with this Trait.
     *
     * Note: You should create a static private property called
     * `$urlProjectObjectName`. Ex. `static private $urlProjectObjectName = 'classes';`
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function url()
    {
        if (strlen($this->url) == 0) {
            $spaceSlug = StringHelpers::namespaceToSlug($this->space);
            $this->url = $this->project->url() .'/'. $spaceSlug .'/'. static::$urlProjectObjectName .'/'. $this->slug;
        }
        return $this->url;
    }
}
