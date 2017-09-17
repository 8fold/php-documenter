<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;

class ProjectTest extends BaseTest
{
    public function testProjectVersionSlug()
    {
        $project = $this->version();
        $this->assertTrue($project->slug() == 'v0-0-0');
    }

    public function testProjectVersion()
    {
        $project = $this->version();
        $result = $project->version();
        $this->assertTrue($result == '0.0.0', 'found version: '. $result);
    }

    public function testProjectSlug()
    {
        $project = $this->project();
        $result = $project->slug();
        $this->assertTrue($result == 'documenter-php');
    }

    public function testProjectFiles()
    {
        $project = $this->version();
        $result = $project->totalFiles();
        $this->assertTrue($result == 14, 'found files: '. $result);
    }

    public function testProjectClasses()
    {
        $project = $this->version();
        $result = $project->classes();
        $this->assertTrue(count($result) == 9, 'found classes: '. count($result));
    }

    public function testProjectTraits()
    {
        $project = $this->version();
        $result = $project->traits();
        $this->assertTrue(count($result) == 4, 'found traits: '. count($result));
    }

    public function testProjectInterfaces()
    {
        $project = $this->version();
        $result = $project->interfaces();
        $this->assertTrue(count($result) == 1, 'found interfaces: '. count($result));
    }

    public function testProjectObjectWithFullName()
    {
        $project = $this->version();
        $object = $project->objectWithFullName(Project::class);
        $result = $object->fullName();
        $this->assertTrue($result == Project::class);
    }

    public function testProjectClassesCategorized()
    {
        $project = $this->version();
        $objects = $project->classesCategorized();
        $this->assertTrue(count($objects) == 3, 'found categories: '. count($objects));
    }
}
