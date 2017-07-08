<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Documenter;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\Version;

class DocumenterTest extends BaseTest
{
    protected function documenter()
    {
        $documenter = new Documenter($this->basePath(), [
                'documenter-php' => 'Documenter for PHP'
            ]);
        $this->assertNotNull($documenter);
        return $documenter;
    }
    protected function project()
    {
        $documenter = $this->documenter();
        $projects = $documenter->projects;
        $this->assertTrue(count($projects) == 1);

        return $documenter->projectWithSlug('documenter-php');
    }

    protected function version()
    {
        $project = $this->project();
        $this->assertTrue(count($project->versions) == 1, 'did not find versions');

        return $project->versionWithSlug('v0-0-0');
    }

    public function testDocumenterInstantiatesProject()
    {
        $project = $this->project();
        $this->assertNotNull($project);
        $this->assertTrue(get_class($project) == Project::class);
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
