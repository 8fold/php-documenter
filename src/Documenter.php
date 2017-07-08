<?php

namespace Eightfold\DocumenterPhp;

use \DirectoryIterator;

use Eightfold\DocumenterPhp\Project;

use Eightfold\DocumenterPhp\Traits\Gettable;

class Documenter
{
    use Gettable;

    private $dirPath = '';

    private $projects = [];

    private $domain = '';

    private $urlBase = '';

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
    public function __construct($dirPath, $projects, $urlBase = '/', $domain = '')
    {
        $this->dirPath = $dirPath;
        $this->projects = $projects;
        $this->domain = $domain;
        $this->urlBase = $urlBase;
    }

    public function projects()
    {
        // TODO: Make $projects optional in construction and use file directory
        //       iterator (all projects, so to speak).
        return $this->projects;
    }

    public function projectWithSlug($slug)
    {
        if (isset($this->projects[$slug]) && $getProject = $this->projects[$slug]) {
            // Cache Project instance
            if (is_string($getProject)) {
                $title = $this->projects[$slug];
                $this->projects[$slug] =  new Project($this, $slug, $title);

            }
            return $this->projects[$slug];
        }
        return null;
    }

    public function domain()
    {
        return $this->domain;
    }

    public function url()
    {
        return $this->domain . $this->urlBase;
    }
}
