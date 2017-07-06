<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\TraitReflector;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\Namespaced;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasSymbols;
use Eightfold\DocumenterPhp\Traits\DefinesSymbols;
use Eightfold\DocumenterPhp\Traits\HasMethods;

/**
 * @category Project object
 */
class Trait_ extends TraitReflector
{
    use Gettable,
        DocBlocked,
        Namespaced,
        Sluggable;

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
     * @todo  Update declarations.
     *
     * See microDeclaration().
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        return $this->miniDeclaration($asHtml, $withLink);
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
        return $this->miniDeclaration($asHtml, $withLink);
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
        return $this->miniDeclaration($asHtml, $withLink);
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
        $build = [];
        $this->displayNameString($asHtml, $build, 'trait');
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => implode(' ',$build),
                'href' => $this->url()
            ]);
        }
        return implode(' ', $build);
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
        $string = $this->miniDeclaration($asHtml, $withLink);
        if ($showKeyword) {
            return $string;
        }
        return str_replace('trait ', '', $string);
    }
}
