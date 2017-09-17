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
        $project = $this->version();
        $object = $project->objectWithFullName(Class_::class);
        $object = $object->traits[0];
        $declaration = $object->largeDeclaration;
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp-traits/traits/gettable"><span class="trait">trait</span> Gettable</a>';
        $this->assertTrue($declaration == $expected, 'declaration: '. $declaration);
    }
}
