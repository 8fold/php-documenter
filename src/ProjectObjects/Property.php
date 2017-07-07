<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;

use phpDocumentor\Reflection\ClassReflector\PropertyReflector;

use Eightfold\DocumenterPhp\Interfaces\HasDeclarations;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\ClassSubObject;
use Eightfold\DocumenterPhp\Traits\Sluggable;
use Eightfold\DocumenterPhp\Traits\HasInheritance;

/**
 * @category Symbols
 */
class Property extends PropertyReflector implements HasDeclarations
{
    use Gettable,
        DocBlocked,
        Sluggable,
        ClassSubObject,
        HasInheritance;

    static private $urlProjectObjectName = 'properties';

    /**
     * [__construct description]
     * @param \Eightfold\DocumenterPhp\ProjectObjects\Class_|\Eightfold\DocumenterPhp\ProjectObjects\Trait_|\Eightfold\DocumenterPhp\ProjectObjects\Interface_            $class     [description]
     * @param PropertyReflector $reflector [description]
     */
    public function __construct($class, PropertyReflector $reflector)
    {
        $this->class = $class;
        $this->project = $this->class->project;
        $this->reflector = $reflector;

        // Setting `node` on ClassReflector
        $this->node = $this->reflector->getNode();
    }

    public function isStatic()
    {
        return $this->reflector->isStatic();
    }

    public function largeDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $build[] = '$'. $this->name;

        $build = implode(' ', $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $build,
                'href' => $this->url()
            ]);
        }
        return $build;
    }

    public function mediumDeclaration($asHtml = true, $withLink = true)
    {
        $build = [];
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $build[] = '$'. $this->name;

        $build = implode(' ', $build);
        if ($withLink) {
            return Html5Gen::a([
                'class' => 'call-signature',
                'content' => $build,
                'href' => $this->url()
            ]);
        }
        return $build;
    }

    public function smallDeclaration($asHtml = true, $withLink = true)
    {
        return $this->mediumDeclaration($asHtml, $withLink);
    }

    public function miniDeclaration($asHtml = true, $withLink = true)
    {
        return $this->smallDeclaration($asHtml, $withLink);
    }


    public function microDeclaration($asHtml = true, $withLink = true, $showKeywords = true)
    {
        $base = $this->miniDeclaration($asHtml, $withLink);

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
            '><',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        if ($showKeywords) {
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
        }

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
