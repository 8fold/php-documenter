<?php

namespace Eightfold\DocumenterPhp;

use Eightfold\Html5Gen\Html5Gen;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;

class Documenter
{
    use Gettable;

    private $path = '';

    private $projectConfigurations = [];

    private $projects = [];

    private $domain = '';

    private $urlBase = '';

    private $maxVisibility = '';

    /**
     * [__construct description]
     * @param [type] $dirPath  The full path to folder containing all the projects for
     *                         the Documenter to be aware of.
     * @param [type] $projects A dictionary of the projects for this Documenter to pay
     *                         attentioned to. The key is the URL-friendly directory
     *                         name for the project. The value is the human-friendly
     *                         name of the project. (All other project folders will be
     *                         ignored.)
     */
    public function __construct($path, $projects, $maxVisibility = 'public', $urlBase = '/', $domain = '')
    {
        $this->path = $path;
        $this->projectConfigurations = $projects;
        $this->domain = $domain;
        $this->urlBase = $urlBase;
        $this->maxVisibility = $maxVisibility;
    }

    public function maxVisibility()
    {
        if ($this->maxVisibility == 'public') {
            return ['public', 'static_public'];

        } elseif ($this->maxVisibility == 'protected') {
            return ['public', 'protected', 'static_public', 'static_protected'];

        }
        return ['public', 'protected', 'private', 'static_public', 'static_protected', 'static_private'];
    }

    public function setMaxVisibility($maxVisibility)
    {
        if ($maxVisibility !== 'public' || $maxVisibility !== 'protected' || $maxVisibility !== 'private') {
            $this->maxVisibility = $maxVisibility;
        }
    }

    public function projects()
    {
        // TODO: Make $projects optional in construction and use file directory
        //       iterator (all projects, so to speak). This may be a horribel idea.
        if (count($this->projects) == 0) {
            foreach ($this->projectConfigurations as $slug => $config) {
                $title = '';
                if (isset($config['title'])) {
                    $title = $config['title'];
                }

                $category = 'NO_CATEGORY';
                if (isset($config['category'])) {
                    $category = $config['category'];
                }
                $this->projects[$category][$slug] =  new Project($this, $slug, $title, $category);
            }
        }
        return $this->projects;
    }

    public function projectWithSlug($slug)
    {
        $project = null;
        foreach ($this->projects() as $category => $slugged) {
            foreach ($slugged as $slugToCheck => $projectFound) {
                if ($slugToCheck == $slug) {
                    $project = $projectFound;
                    break;

                }
            }
        }
        return $project;
    }

    public function path()
    {
        return $this->path;
    }

    public function domain()
    {
        return $this->domain;
    }

    public function url()
    {
        return $this->domain . $this->urlBase;
    }

    public function projectsNavigator(Project $project = null, $includeButton = true)
    {
        // If total projects great than 1 - create project dropdown
        $button = '';
        if (count($this->projects()) > 1) {
            $button = $this->projectsSelector();

        } else {
            $button = 'Projects: ';

        }

        $projectLink = 'Select a project&hellip;';
        if (!is_null($project)) {
            $projectLink = Html5Gen::a([
                'href' => $project->url,
                'title' => $project->title,
                'content' => $project->title
            ]);
        }

        return Html5Gen::nav([
            'class' => 'projects',
            'content' => $button . $projectLink
        ]);
    }

    private function projectsSelector()
    {
        $italic = '<i class="fa fa-angle-down" aria-hidden="true"></i>';
        $button = [
            'element' => 'button',
            'config' => [
                'class' => 'collapsable',
                'content' => $italic .' Projects: '
            ]
        ];

        $listItems = [];
        foreach ($this->projects() as $category => $project) {
            foreach ($project as $slug => $object) {
                $listItems[] = [
                    'element' => 'li',
                    'config' => [
                        'content' => [
                            [
                                'element' => 'a',
                                'config' => [
                                    'href' => $object->url,
                                    'title' => $object->title,
                                    'content' => $object->title
                                ]
                            ]
                        ]
                    ]
                ];
            }
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
