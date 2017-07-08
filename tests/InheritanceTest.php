<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\Class_;


class InheritanceTest extends BaseTest
{
    protected $object = null;

    public function setUp()
    {
        $this->object = $this->object(Class_::class);
    }

    public function object($classNamespace)
    {
        $project = new Project($this->versionPath());
        return $project->objectWithFullName($classNamespace);
    }

    public function testInheritanceReturn()
    {
        $inheritance = $this->object->inheritance;
        $this->assertTrue(count($inheritance) == 2);
    }

    public function testParentReturn()
    {
        $expected = 'ClassReflector';
        $this->assertNotNull($this->object, 'object is null');

        $parent = $this->object->parent;
        $this->assertNotNull($parent, 'parent is null');

        $result = $this->object->parent->name;
        $this->assertTrue($expected == $result);
    }

    public function testParentDeclaration()
    {
        $expected = '<i>[ClassReflector]</i>';
        $result = $this->object->parent->largeDeclaration;
        $this->assertTrue(strlen($result) > 0, $result);
        $this->assertTrue($expected == $result, $result);
    }

    public function testParentBreadcrumbs()
    {
        $expected = '<span class="object-navigator"><span class="separated"><i>[ClassReflector]</i></span><span class="separated"><a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp-projectobjects/classes/class">Class_</a></span></span>';
        $result = $this->object->parentBreadcrumbs;
        $this->assertTrue($expected == $result, $result);
    }
}
