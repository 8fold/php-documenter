<?php

namespace Eightfold\DocumenterPhp;

use Eightfold\Html5Gen\Html5Gen;

use Eightfold\DocumenterPhp\Traits\Gettable;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

/**
 * Represents a `class` outside the current project.
 *
 * Sometimes projects will extend classes found in other projects. In those cases,
 * Documenter instantiates this class instead of `Class_`. Therefore, `ClassExternal`
 * has the minimum functionality necessary to display information related to the
 * external class without essentially making it part of the project being explored.
 *
 * @category Project object
 */
class ClassExternal implements HasDeclarations
{
    use Gettable;

    /**
     * Copy of `$namespaceParts` for caching.
     *
     * @var array
     */
    private $parts = [];

    /**
     * @category Initializer
     *
     * @param array $namespaceParts Array containing the full class name of the
     *                              external class in order.
     */
    public function __construct($namespaceParts)
    {
        if (isset($namespaceParts[0]) && strlen($namespaceParts[0]) == 0) {
            array_shift($namespaceParts);
        }
        $this->parts = $namespaceParts;
    }

    /**
     * Imploded `$parts` using a backslash for the separater after removing last
     * element from the array.
     *
     * @return string
     */
    public function space()
    {
        $copy = $this->parts;
        array_pop($copy);
        return implode('\\', $copy);
    }

    public function fullName()
    {
        return implode('\\', $this->parts);
    }

    /**
     * Last element in `$parts`.
     *
     * @return string
     */
    public function name()
    {
        // dd(array_pop($this->parts));
        return array_pop($this->parts);
    }

    /**
     * Always returns false as this class is specifically for external classes.
     *
     * @return boolean Always false.
     */
    public function isInProjectSpace()
    {
        return false;
    }

    /**
     * Always null, we do not want to try and document all related or dependent
     * projects.
     *
     * @return [type] [description]
     */
    public function parent()
    {
        return null;
    }

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
    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        return Html5Gen::i([
                'content' => '['. $this->name .']'
            ]);
    }

    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        return $this->largeDeclaration;
    }

    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        return $this->largeDeclaration;
    }

    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        return $this->largeDeclaration;
    }

    public function microDeclaration($asHtml = true, $withLink = true, $showKeyword = true)
    {
        return $this->largeDeclaration;
    }
}
