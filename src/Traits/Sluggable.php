<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;

use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;

trait Sluggable
{
    /**
     * [slug description]
     * @return [type] [description]
     *
     * @category Strings
     */
    public function slug()
    {
        return StringHelpers::slug($this->name);
    }

    /**
     * Get the url for the Project Object with this Trait.
     *
     * Note: You should create a static private property called
     * `$urlProjectObjectName`. Ex. `static private $urlProjectObjectName = 'classes';`
     *
     * @return [type] [description]
     *
     * @category Strings
     */
    public function url()
    {
        $build = [];
        if (static::class == Class_::class || static::class == Trait_::class) {
            $build[] = $this->project->url;
            $build[] = StringHelpers::namespaceToSlug($this->space);

        } else {
            $build[] = $this->class->url;

        }
        $build[] = static::$urlProjectObjectName;
        $build[] = $this->slug;
        return implode('/', $build);
    }

    public function objectTypeTitle($wrapper = 'h1')
    {
        $deprecatedString = '';
        if ($this->isDeprecated) {
            $deprecatedString = Html5Gen::span([
                    'content' => 'Deprecated',
                    'class' => 'deprecated'
                ]);
        }

        $type = '';
        if ($this->name == '__construct') {
            $type = 'Initializer';

        } else {
            $name = static::class;
            $parts = explode('\\', $name);
            $type = str_replace('_', '', array_pop($parts));
        }

        $typeString = Html5Gen::span([
                'content' => $type,
                'class' => 'method-type'
            ]);

        $content = [
            $deprecatedString,
            $typeString,
            $this->microDeclaration(false, false, false)
        ];

        $content = implode(Html5Gen::br(), $content);

        return Html5Gen::$wrapper([
                'content' => [
                    $content
                ]
            ]);
    }
}
