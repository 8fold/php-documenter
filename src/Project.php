<?php

namespace Eightfold\DocumenterPhp;

use \DirectoryIterator;

use Eightfold\Html5Gen\Html5Gen;

use Eightfold\DocumenterPhp\Version;

use Eightfold\DocumenterPhp\Traits\Gettable;

class Project
{
    use Gettable;

    private $documenter = null;

    private $slug = '';

    private $category = '';

    private $versions = [];

    /**
     * [__construct description]
     *
     * @param string $path   The complete path to the project version.
     * @param string $root   The root directory of the directory to process within the
     *                       project. No starting slash.
     * @param array $ignore  Array of directory names to ignore.
     */
    public function __construct($documenter, $slug, $title = '', $category = '')
    // public function __construct($path, $root = 'src', $ignore = [])
    {
        $this->documenter = $documenter;
        $this->slug = $slug;
        $this->title = $title;
        $this->category = $category;
    }

    public function documenter()
    {
        return $this->documenter;
    }

    public function path()
    {
        return $this->documenter->path .'/'. $this->slug;
    }

    public function slug()
    {
        return $this->slug;
    }

    public function title()
    {
        return $this->title;
    }

    public function category()
    {
        return $this->category;
    }

    public function url()
    {
        if ($this->documenter->url == '/') {
            return '/'. $this->slug;
        }
        return $this->documenter->url .'/'. $this->slug;
    }

    public function versions()
    {
        if (count($this->versions) == 0) {
            if ($directory = new DirectoryIterator($this->path)) {
                foreach ($directory as $projectFileInfo) {
                    if ($projectFileInfo->isDir() && !$projectFileInfo->isDot()) {
                        $versionSlug = $projectFileInfo->getFilename();
                        $this->versions[$versionSlug] = null;

                    }
                }
            }
        }
        return $this->versions;
    }

    public function highestVersionSlug()
    {
        $versions = $this->versions();
        $versionNumbers = [];
        foreach ($versions as $slug => $version) {
            $versionNumbers[] = str_replace(['v', '-'], ['', '.'], $slug);

        }
        $reversed = array_reverse($versionNumbers);
        $highest = $reversed[0];
        return 'v'. str_replace('.', '-', $highest);
    }

    public function versionWithSlug($slug, $root = 'src', $ignore = [])
    {
        if (array_key_exists($slug, $this->versions())) {
            // Cache Project instance
            if (is_null($this->versions[$slug])) {
                $title = $this->versions[$slug];
                $this->versions[$slug] =  new Version($this, $slug, $root, $ignore);

            }
            return $this->versions[$slug];
        }
        return null;
    }

    public function versionsNavigator(Version $version = null, $includeButton = true)
    {
        $button = '';
        if (count($this->versions()) > 1) {
            $button = $this->versionsSelector();

        } else {
            $button = 'Version: ';

        }

        $link = 'Select a version&hellip;';
        if (!is_null($version)) {
            $link = Html5Gen::a([
                'href' => $version->url,
                'title' => $version->version,
                'content' => $version->version
            ]);
        }

        return Html5Gen::nav([
            'class' => 'version-navigator',
            'content' => $button . $link
        ]);
    }

    private function versionsSelector()
    {
        $italic = '<i class="fa fa-angle-down" aria-hidden="true"></i>';
        $button = [
            'element' => 'button',
            'config' => [
                'class' => 'collapsable',
                'content' => $italic .' Versions: '
            ]
        ];

        $listItems = [];
        foreach ($this->versions() as $slug => $version) {
            if (is_null($version)) {
                $version = $this->versionWithSlug($slug);

            }
            $listItems[] = [
                'element' => 'li',
                'config' => [
                    'content' => [
                        [
                            'element' => 'a',
                            'config' => [
                                'href' => $version->url,
                                'title' => $version->version,
                                'content' => $version->version
                            ]
                        ]
                    ]
                ]
            ];
        }

        $list = [
            'element' => 'ul',
            'config' => [
                'class' => 'collapsed',
                'content' => $listItems
            ]
        ];

        return Html5Gen::elements([$button, $list]);
    }
}
