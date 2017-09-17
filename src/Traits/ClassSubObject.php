<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

trait ClassSubObject
{
    private $class = null;

    private $project = null;

    private $reflector = null;

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
