<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Class_;
use Eightfold\DocumenterPhp\ProjectObjects\Trait_;

class TraitTest extends BaseTest
{
    public function testClassTraitLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $object = $object->traits[0];
        $declaration = $object->largeDeclaration;
        $expected = '<span class="trait">trait</span> Gettable';
        $this->assertTrue($declaration == $expected, 'declaration: '. $declaration);
    }
}
