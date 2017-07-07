<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use Eightfold\DocumenterPhp\ProjectObjects\Trait_;

trait HasTraitDeclarations
{
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

        if (static::class == Trait_::class) {
            return str_replace('trait ', '', $string);
        }
        return str_replace('interface ', '', $string);
    }
}
