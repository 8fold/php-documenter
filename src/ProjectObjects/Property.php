<?php

namespace Eightfold\DocumenterPhp\ProjectObjects;

use Eightfold\Html5Gen\Html5Gen;

use phpDocumentor\Reflection\ClassReflector\PropertyReflector;

use Eightfold\DocumenterPhp\Traits\Gettable;
use Eightfold\DocumenterPhp\Traits\DocBlocked;
use Eightfold\DocumenterPhp\Traits\ClassSubObject;
use Eightfold\DocumenterPhp\Traits\Sluggable;

/**
 * @category Symbols
 */
class Property extends PropertyReflector
{
    use Gettable,
        DocBlocked,
        Sluggable,
        ClassSubObject;

    static private $urlProjectObjectName = 'properties';

    /**
     * [__construct description]
     * @param \Eightfold\DocumenterPhp\ProjectObjects\Class_|\Eightfold\DocumenterPhp\ProjectObjects\Trait_|\Eightfold\DocumenterPhp\ProjectObjects\Interface_            $class     [description]
     * @param PropertyReflector $reflector [description]
     */
    public function __construct($class, PropertyReflector $reflector)
    {
        $this->class = $class;
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


    public function microDeclaration($asHtml = true, $withLink = true, $showKeywords = true)
    {
        $build = [];
        $this->staticString($asHtml, $build);
        $this->accessString($asHtml, $build);
        $build[] = '$'. $this->name;

        $base = implode(' ', $build);

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
