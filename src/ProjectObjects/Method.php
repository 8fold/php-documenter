<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

use phpDocumentor\Reflection\ClassReflector\MethodReflector;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\Parameter;
use Eightfold\DocumenterPhp\ProjectObjects\SubObjects\TypeHint;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\ClassSubObject;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasInheritance;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

/**
 * @category Symbols
 */
class Method extends MethodReflector implements HasDeclarations
{
    use Gettable,
        DocBlocked,
        Sluggable,
        ClassSubObject,
        HasInheritance;

    private $parameters = [];

    private $returnType = null;

    static private $urlProjectObjectName = 'methods';

    /**
     * [__construct description]
     * @param \Eightfold\DocumenterPhp\ProjectObjects\Class_|\Eightfold\DocumenterPhp\ProjectObjects\Trait_|\Eightfold\DocumenterPhp\ProjectObjects\Interface_            $class     [description]
     * @param PropertyReflector $reflector [description]
     */
    public function __construct($class, MethodReflector $reflector)
    {
        $this->class = $class;
        $this->project = $this->class->project;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function parameters()
    {
        if (count($this->parameters) == 0) {
            $parameters = $this->reflector->getArguments();
            foreach($parameters as $parameter) {
                $this->parameters[] = new Parameter($this, $parameter);
            }
        }
        return $this->parameters;
    }

    public function definitionListForParameters()
    {
        $listItems = [];
        foreach ($this->parameters as $parameter) {
            $listItems[] = [
                'element' => 'dt',
                'config' => [
                    'content' => $parameter->mediumDeclaration(),
                    'class' => 'code'
                ]
            ];

            $converter = new CommonMarkConverter();
            $content = $converter->convertToHtml($parameter->shortDescription ."\n\n". $parameter->discussion);
            $listItems[] = [
                'element' => 'dd',
                'config' => ['content' => $content]
            ];
        }

        return Html5Gen::dl([
                'content' => $listItems
            ]);
    }

    public function returnType()
    {
        if (is_null($this->returnType) && !is_null($this->docBlock()) && $this->docBlock()->hasTag('return')) {
            $tag = $this->docBlock()->getTagsByName('return')[0];
            $type = $tag->getType();
            $this->returnType = new TypeHint($this, $tag, $type);
        }
        return $this->returnType;
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

        $built = str_replace([' (', '( ', ' :'], ['(', '([type]', ':'], implode(' ', $build));
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $built,
                'href' => $this->url()
            ]);
        }
        return $built;
    }

    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->finalString($asHtml, $build);
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $this->functionString($asHtml, $build);
        $build[] = $this->name;
        $this->parameterString($asHtml, $build);

        $built = str_replace([' (', '( ', ' :'], ['(', '([type]', ':'], implode(' ', $build));
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $built,
                'href' => $this->url()
            ]);
        }
        return $built;
    }

    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->finalString($asHtml, $build);
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $this->functionString($asHtml, $build);
        $build[] = $this->name .'()';

        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $built,
                'href' => $this->url()
            ]);
        }
        return $built;
    }

    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->finalString($asHtml, $build);
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $this->functionString($asHtml, $build);
        $build[] = $this->name;
        $base = implode(' ', $build);
        $base .= '()';

        $replace = [
            '>abstract<',
            'static',
            'final',
            'private',
            'protected',
            'public',
            'function',
            'class'
        ];
        $with = [
            '>abs<',
            'stat',
            'fin',
            'priv',
            'prot',
            'pub',
            'func',
            'class'
        ];

        $build = str_replace($replace, $with, $base);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $build,
                'href' => $this->url()
            ]);
        }
        return $build;
    }

    public function microDeclaration($asHtml = true, $withLink = true, $showKeywords = false)
    {
        $base = $this->miniDeclaration($asHtml, false);
        $replace = [
            '>abs<',
            'stat',
            'fin',
            'priv',
            'prot',
            'pub',
            'func',
            'class'
        ];
        $with = [
            '><',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];

        $build = str_replace($replace, $with, $base);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $build,
                'href' => $this->url()
            ]);
        }
        return $build;
    }
}
