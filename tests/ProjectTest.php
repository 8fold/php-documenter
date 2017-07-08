<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;

class ProjectTest extends BaseTest
{
    public function testProjectsCollection()
    {
        $projects = [];
        Project::projectPaths($this->basePath(), $projects);
        $this->assertTrue(count($projects) == 1);
    }

    public function testProjectPathsForSlug()
    {
        $projects = [];
        Project::projectPathsForSlug($this->basePath(), 'documenter-php', $projects);
        $this->assertTrue(count($projects) == 1);
    }

    public function testProjectPathForVersion()
    {
        $projects = [];
        Project::projectPathForVersion($this->basePath(), 'documenter-php', 'v0-0-0', $projects);
        $this->assertTrue(count($projects) == 1);
    }

    public function testProjectVersionSlug()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue($project->versionSlug() == 'v0-0-0');
    }

    public function testProjectVersion()
    {
        $project = new Project($this->versionPath());
        $result = $project->version();
        $this->assertTrue($result == '0.0.0', 'found version: '. $result);
    }

    public function testProjectSlug()
    {
        $project = new Project($this->versionPath());
        $result = $project->projectSlug();
        $this->assertTrue($result == 'documenter-php');
    }

    public function testProjectFiles()
    {
        $project = new Project($this->versionPath());
        $result = $project->totalFiles();
        $this->assertTrue($result == 14, 'found files: '. $result);
    }

    public function testProjectClasses()
    {
        $project = new Project($this->versionPath());
        $result = $project->classes();
        $this->assertTrue(count($result) == 9, 'found classes: '. count($result));
    }

    public function testProjectTraits()
    {
        $project = new Project($this->versionPath());
        $result = $project->traits();
        $this->assertTrue(count($result) == 4, 'found traits: '. count($result));
    }

    public function testProjectInterfaces()
    {
        $project = new Project($this->versionPath());
        $result = $project->interfaces();
        $this->assertTrue(count($result) == 1, 'found interfaces: '. count($result));
    }

    public function testProjectObjectWithFullName()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $result = $object->fullName();
        $this->assertTrue($result == Project::class);
    }

    public function testProjectClassesCategorized()
    {
        $project = new Project($this->versionPath());
        $objects = $project->classesCategorized();
        $this->assertTrue(count($objects) == 3, 'found categories: '. count($objects));
    }
}
