<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use phpDocumentor\Reflection\ClassReflector\MethodReflector;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\Parameter;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\TypeHint;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
// use Eightfold\Documenter\Php\File;
// use Eightfold\Documenter\Php\DocBlock;

// use Eightfold\Documenter\Php\Trait_;
// use Eightfold\Documenter\Php\Interface_;
// use Eightfold\Documenter\Php\Parameter;

// use Eightfold\Documenter\Interfaces\HasDeclarations;

// use Eightfold\Documenter\Traits\TraitGroupDocNameParam;
// use Eightfold\Documenter\Traits\TraitGroupDeclaredStaticAccess;

// use Eightfold\Documenter\Traits\CanBeAbstract;
// use Eightfold\Documenter\Traits\CanBeFinal;
// use Eightfold\Documenter\Traits\HighlightableString;

/**
 * @category Symbols
 */
class ClassMethod extends MethodReflector
{
    use Gettable,
        DocBlocked;

    private $class = null;

    private $project = null;

    private $reflector = null;

    private $url = '';

    private $parameters = [];

    private $returnType = null;

    public function __construct(Class_ $class, MethodReflector $reflector)
    {
        $this->class = $class;
        $this->project = $this->class->project;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function url()
    {
        if (strlen($this->url) == 0) {
            $slug = StringHelpers::slug($this->reflector->getShortName());
            $this->url = $this->class->url .'/methods/'. $slug;
        }
        return $this->url;
    }

    public function parameters()
    {
        $parameters = $this->reflector->getArguments();
        if (count($this->parameters) == 0) {
            foreach($parameters as $parameter) {
                $this->parameters[] = new Parameter($this, $parameter);
            }
        }
        return $this->parameters;
    }

    private function returnType()
    {
        if (is_null($this->returnType) && !is_null($this->docBlock()) && $this->docBlock()->hasTag('return')) {
            $tag = $this->docBlock()->getTagsByName('return')[0];
            $type = $tag->getType();
            $this->returnType = new TypeHint($this, $tag, $type);
        }
        return $this->returnType;
    }

    public function name()
    {
        return $this->reflector->getShortName();
    }

    private function finalString($asHtml, &$build)
    {
        if ($this->reflector->isFinal()) {
            if ($asHtml) {
                $build[] = Html5Gen::span([
                        'content' => 'final',
                        'class' => 'final'
                    ]);

            } else {
                $build[] = 'final';

            }
        }
    }

    private function staticString($asHtml, &$build)
    {
        if ($this->reflector->isStatic()) {
            if ($asHtml) {
                $build[] = Html5Gen::span([
                        'content' => 'static',
                        'class' => 'static'
                    ]);

            } else {
                $build[] = 'static';

            }
        }
    }

    private function accessString($asHtml, &$build)
    {
        $access = $this->reflector->getVisibility();
        if ($asHtml) {
            $build[] = Html5Gen::span([
                    'content' => $access,
                    'class' => 'access'
                ]);

        } else {
            $build[] = $access;

        }
    }

    private function functionString($asHtml, &$build)
    {
        if ($asHtml) {
            $build[] = Html5Gen::span([
                    'content' => 'function',
                    'class' => 'function'
                ]);

        } else {
            $build[] = 'function';

        }
    }

    private function parameterString($asHtml, &$build)
    {
        $pStrings = [];
        if (count($this->parameters()) > 0) {
            foreach ($this->parameters() as $parameter) {
                $pStrings[] = $parameter->largeDeclaration;
            }
        }
        $build[] = '('. implode(', ', $pStrings) .')';
    }

    private function returnTypeString($asHtml, &$build)
    {
        if ($returnTag = $this->returnType()) {
            $build[] = ': '. $returnTag->displayString($asHtml);
        }
    }

    /**
     * Displays the most complete representation of the Function definition.
     *
     * Ex. static [access-level]
     *     function [function-name]([type] [parameter] = '[default]'): [return-type]
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->finalString($asHtml, $build);
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $this->functionString($asHtml, $build);
        $build[] = $this->name;
        $this->parameterString($asHtml, $build);
        $this->returnTypeString($asHtml, $build);

        $built = str_replace([' (', ' :'], ['(', ':'], implode(' ', $build));
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $built,
                'href' => $this->url()
            ]);
        }
        return $built;
    }
}
