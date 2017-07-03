<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Class_;

class ClassTest extends BaseTest
{
    public function testProjectClassLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $declaration = $object->largeDeclaration;
        $this->assertTrue($declaration == 'class Project', 'declaration: '. $declaration);
    }

    public function testClassClassLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $declaration = $object->largeDeclaration;
        $this->assertTrue($declaration == 'class Class_ extends [ClassReflector] implements HasDeclarations', 'declaration: '. $declaration);
    }
}
