<?php

namespace Eightfold\DocumenterPhp\ProjectObjects\SubObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;

use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\Parameter;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\ClassMethod;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

class TypeHint extends ParamTag
{
    use Gettable,
        DocBlocked;

    private $parameterOrMethod = null;

    private $docTag = null;

    private $_type = '';

    /**
     * [__construct description]
     * @param [type] $parameterOrMethod [description]
     * @param [type] $docTag            [description]
     * @param [type] $type              [description]
     */
    public function __construct($parameterOrMethod, $docTag, $type)
    {
        $this->parameterOrMethod = $parameterOrMethod;
        $this->docTag = $docTag;
        $this->_type = $type;
    }

    private function project()
    {
        return $this->parameterOrMethod->project;
    }

    public function displayString($asHtml = false, $withLink = false)
    {
        $content = $this->_type;
        // If first character is a backslash check if internal or external
        if (substr($content, 0, 1) == "\\" && !strpos($content, '[type]')) {
            $types = explode('|', $content);
            $displayStrings = [];
            foreach ($types as $typeString) {
                if ($class = $this->project->objectWithFullName($typeString)) {
                    $displayStrings[] = $class->microDeclaration(false, $withLink, false);

                } else {
                    // Not a class in the project
                    $parts = explode('\\', $typeString);
                    $name = array_pop($parts);
                    $displayStrings[] = '['. $name .']';

                }
            }
            $content = implode('|', $displayStrings);

        } elseif (strpos($content, '[type]')) {
            $content = str_replace('\\Eightfold\\DocumenterPhp\\', '', $content);

        }

        return ($asHtml)
            ? Html5Gen::span([
                'content' => $content,
                'class' => 'typehint'
                ])
            : $this->_type;
    }
}
