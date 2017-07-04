<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use phpDocumentor\Reflection\InterfaceReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

/**
 * Represents an `interface` in a project.
 *
 * @category Project object
 */
class Interface_ extends InterfaceReflector
{
    use Gettable,
        Namespaced,
        DocBlocked;

    static private $urlProjectObjectName = 'interfaces';

    private $reflector = null;

    private $project = null;

    public function __construct(Project $project, InterfaceReflector $reflector)
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
        $keyword = 'interface';
        $this->displayNameString($asHtml, $build, $keyword);
        $string = implode(' ', $build);
        if ($showKeyword) {
            return $string;
        }
        return str_replace($keyword .' ', $string);
    }
}
