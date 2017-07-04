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
        $this->assertTrue($project->versionSlug == 'v0-0-0');
    }

    public function testProjectVersion()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue($project->version == '0.0.0', 'found version: '. $project->version);
    }

    public function testProjectSlug()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue($project->projectSlug == 'documenter-php');
    }

    public function testProjectFiles()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue($project->totalFiles == 14, 'found files: '. $project->totalFiles);
    }

    public function testProjectClasses()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue(count($project->classes) == 9, 'found classes: '. count($project->classes));
    }

    public function testProjectTraits()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue(count($project->traits) == 4, 'found traits: '. count($project->traits));
    }

    public function testProjectInterfaces()
    {
        $project = new Project($this->versionPath());
        $this->assertTrue(count($project->interfaces) == 1, 'found interfaces: '. count($project->interfaces));
    }

    public function testProjectObjectWithFullName()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $this->assertTrue($object->fullName == Project::class);
    }

    public function testProjectClassesCategorized()
    {
        $project = new Project($this->versionPath());
        $objects = $project->classesCategorized;
        $this->assertTrue(count($objects) == 3, 'found categories: '. count($objects));
    }
}
