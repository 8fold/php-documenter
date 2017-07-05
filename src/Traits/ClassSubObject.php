<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

trait ClassSubObject
{
    private $class = null;

    private $project = null;

    private $reflector = null;

    private $url = '';

    public function url()
    {
        if (strlen($this->url) == 0) {
            $slug = StringHelpers::slug($this->reflector->getShortName());
            $this->url = $this->class->url .'/'. static::$urlProjectObjectName .'/'. $slug;
        }
        return $this->url;
    }


    public function name()
    {
        return $this->reflector->getShortName();
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
}
