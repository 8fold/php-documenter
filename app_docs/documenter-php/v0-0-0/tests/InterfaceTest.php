<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Interface_;

class InterfaceTest extends BaseTest
{
    public function testClassTraitLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $object = $object->interfaces[0];
        $declaration = $object->largeDeclaration;
        $expected = '<span class="interface">interface</span> HasDeclarations';
        $this->assertTrue($declaration == $expected, 'declaration: '. $declaration);
    }
}
