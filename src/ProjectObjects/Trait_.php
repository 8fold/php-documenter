<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\TraitReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;

/**
 * @category Project object
 */
class Trait_ extends TraitReflector
{
    use Gettable,
        Namespaced;

    static private $urlProjectObjectName = 'traits';

    private $project = null;

    private $reflector = null;

    public function __construct(Project $project, TraitReflector $reflector)
    {
        $this->project = $project;
        $this->reflector = $reflector;

        // Setting `node` on InterfaceReflector
        $this->node = $this->reflector->getNode();
    }

    /**
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        return $this->microDeclaration($asHtml, $withLink);
    }

    /**
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        return $this->microDeclaration($asHtml, $withLink);
    }

    /**
     *
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        return $this->microDeclaration($asHtml, $withLink);
    }

    /**
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        return $this->microDeclaration($asHtml, $withLink);
    }

    /**
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function microDeclaration($asHtml = true, $withLink = true, $showKeyword = true)
    {
        $build = [];
        $keyword = 'trait';
        $this->displayNameString($asHtml, $build, $keyword);
        $string = implode(' ', $build);
        if ($showKeyword) {
            return $string;
        }
        return str_replace($keyword .' ', $string);
    }
}
