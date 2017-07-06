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
        $declaration = $object->largeDeclaration(false, false);
        $this->assertTrue($declaration == 'class Project has trait Gettable', 'declaration: '. $declaration);
    }

    public function testClassInterfaces()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $this->assertTrue(count($object->interfaces) == 1, 'interfaces found: '. count($object->interfaces));
    }

    public function testClassTraits()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $this->assertTrue(count($object->traits) == 3, 'traits found: '. count($object->traits));
    }

    public function testProjectTraits()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $this->assertTrue(count($object->traits) == 1, 'traits found: '. count($object->traits));
    }

    public function testProjectMethods()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $this->assertTrue(count($object->methods) == 21, 'methods found: '. count($object->methods));
    }

    public function testProjectProperties()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $this->assertTrue(count($object->properties) == 11, 'properties found: '. count($object->properties));
    }

    public function testClassPropertyLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $prop = $object->propertyWithName('path');
        $declaration = $prop->largeDeclaration();
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp/classes/project/properties/path"><span class="access">private</span> $path</a>';
        $this->assertTrue($expected == $declaration, 'declaration: '. $declaration);
    }

    public function testClassClassLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Class_::class);
        $declaration = $object->largeDeclaration;
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp-projectobjects/classes/class"><span class="class">class</span> Class_ <span class="extends">extends</span> <span class="related">[ClassReflector]</span> <span class="implements-label">implements</span> <span class="related">HasDeclarations</span> <span class="traits-label">has traits</span> <span class="related">Gettable</span>, <span class="related">Namespaced</span>, <span class="related">DocBlocked</span></a>';
        $this->assertTrue($declaration == $expected, 'declaration: '. $declaration);
    }
}
