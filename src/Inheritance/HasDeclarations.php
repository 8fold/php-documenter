<?php

namespace Eightfold\DocumenterPhp\Traits;

interface HasDeclarations
{
    /**
     * Displays the most complete representation of the Class definition.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *     implements [class-interfaces]
     *     has traits [class-traits]
     *
     * @return [type] [description]
     *
     * @category Declarations
     */
    public function largeDeclaration($asHtml = true, $withLink = true);

    /**
     * Displays complete representation of the Class definition less traits.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *     implements [class-interfaces]
     *
     * @return [type] [description]
     *
     * @category Declarations
     */
    public function mediumDeclaration($asHtml = true, $withLink = true);

    /**
     * Displays complete representation of the Class definition less interfaces and
     * traits.
     *
     * Ex. class [class-name]
     *     extends [class-parent]
     *
     * @return [type] [description]
     *
     * @category Declarations
     */
    public function smallDeclaration($asHtml = true, $withLink = true);

    /**
     * Displays only class name.
     *
     * Ex. class [class-name]
     *
     * @return [type] [description]
     *
     * @category Declarations
     */
    public function miniDeclaration($asHtml = true, $withLink = true);

    /**
     * Displays only class name and optionally removed "class" keyword.
     *
     * Ex. class [class-name]
     *
     * @return [type] [description]
     *
     * @category Declarations
     */
    public function microDeclaration($asHtml = true, $withLink = true, $showKeyword = true);
}
