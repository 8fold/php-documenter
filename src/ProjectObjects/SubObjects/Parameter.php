<?php

namespace Eightfold\DocumenterPhp\ProjectObjects\SubObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Method;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\TypeHint;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;

/**
 * @category Symbols
 */
class Parameter extends ArgumentReflector implements HasDeclarations
{
    use Gettable,
        DocBlocked;

    // private $project = null;

    private $method = null;

    private $reflector = null;

    private $types = [];

    public function __construct(Method $method, ArgumentReflector $reflector)
    {
        $this->method = $method;
        // $this->project = $this->method->class->project;
        $this->reflector = $reflector;

        // Setting `node` on ArgumentReflector
        $this->node = $this->reflector->getNode();
    }

    public function project()
    {
        return $this->method->class->project;
    }

    public function name()
    {
        return $this->getName();
    }

    private function types()
    {
        if (count($this->types) == 0) {
            $return = [];
            if ($paramTag = static::paramTagForVariableName($this->name, $this->docBlock())) {
                $types = $paramTag->getTypes();
                foreach ($types as $type) {
                    $return[] = new TypeHint($this, $paramTag, $type);
                }
                $this->types = $return;
            }
        }
        return $this->types;
    }

    private function typeString($asHtml, &$build = [])
    {
        $types = $this->types();
        $typeStrings = [];
        foreach ($this->types() as $type) {
            $typeStrings[] = $type->displayString($asHtml);

        }
        $complete = implode('|', $typeStrings);
        $build[] = $complete;
        return $complete;
    }

    private function nameString($asHtml, &$build)
    {
        $content = $this->name();
        if ($this->isByRef()) {
            $content = '&'. $content;
        }

        $build[] = ($asHtml)
            ? Html5Gen::span([
                'content' => $content,
                'class' => 'parameter'
                ])
            : $content;
    }

    private function defaultString($asHtml, &$build)
    {
        if ($default = $this->getDefault()) {
            $build[] = '= '. $default;
        }
    }

    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->typeString($asHtml, $build);
        $this->nameString($asHtml, $build);
        $this->defaultString($asHtml, $build);
        return implode(' ', $build);
    }

    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->typeString($asHtml, $build);
        $this->nameString($asHtml, $build);
        return implode(' ', $build);
    }

    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        return $this->mediumDeclaration($asHtml, $withLink);
    }

    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        return $this->mediumDeclaration($asHtml, $withLink);
    }

    public function microDeclaration($asHtml = true, $withLink = true, $showKeyword = true)
    {
        return $this->mediumDeclaration($asHtml, $withLink);
    }
}
