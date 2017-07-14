<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Documenter;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\Version;

use Eightfold\DocumenterPhp\Tests\BaseTest;

class DocumenterTest extends BaseTest
{
    public function testDocumenterInstantiatesProject()
    {
        $project = $this->project();
        $this->assertNotNull($project);
        $this->assertTrue(get_class($project) == Project::class, get_class($project));
    }

    public function testDocumenterProjectInstantiatesVersion()
    {
        $version = $this->version();
        $this->assertNotNull($version, 'version is null');
        $this->assertTrue(get_class($version) == Version::class);
    }

    public function testDocumenterUrl()
    {
        $documenter = $this->documenter();
        $expected = '/';
        $result = $documenter->url();
        $this->assertEquality($expected, $result);
    }

    public function testDocumenterProjectUrl()
    {
        $project = $this->project();
        $expected = '/documenter-php';
        $result = $project->url();
        $this->assertEquality($expected, $result);
    }

    public function testProjectVersionUrl()
    {
        $version = $this->version();
        $expected = '/documenter-php/v0-0-0';
        $result = $version->url();
        $this->assertEquality($expected, $result);
    }
}
