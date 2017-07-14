<?php
namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Documenter;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\Version;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    protected function assertEquality($expected, $result)
    {
       $this->assertTrue($result == $expected, $expected ."\n\n". $result);
    }

    protected function basePath()
    {
        $dir = __DIR__;
        $parts = explode('/', $dir);
        array_pop($parts);
        $base = implode('/', $parts);
        return $base .'/app_docs';
    }

    protected function versionPath()
    {
        return $this->basePath() .'/documenter-php/v0-0-0';
    }

    protected function documenter()
    {
        $documenter = new Documenter($this->basePath(), [
                'documenter-php' => [
                    'title' => 'Documenter for PHP'
                ]
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
}
