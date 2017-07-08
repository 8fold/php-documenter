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
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp-interfaces/interfaces/hasdeclarations"><span class="trait">trait</span> HasDeclarations</a>';
        $this->assertTrue($declaration == $expected, 'declaration: '. $declaration);
    }
}
