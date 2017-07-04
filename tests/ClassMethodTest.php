<?php

namespace Eightfold\DocumenterPhp\tests;

use Eightfold\DocumenterPhp\Tests\BaseTest;

use Eightfold\DocumenterPhp\Project;
use Eightfold\DocumenterPhp\ProjectObjects\ClassMethod;

class ClassMethodTest extends BaseTest
{
    public function testClassMethodLargeDeclaration()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $method = $object->methodWithName('projectPaths');
        $declaration = $method->largeDeclaration();
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp/classes/project/methods/projectpaths"><span class="static">static</span> <span class="access">public</span> <span class="function">function</span> projectPaths(<span class="typehint">string</span> <span class="parameter">$path</span>,  <span class="parameter">&$projArray</span>)</a>';
        $this->assertTrue($expected == $declaration, 'declaration: '. $declaration);
    }

    public function testClassMethodLargeDeclarationReturnType()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $method = $object->methodWithName('url');
        $declaration = $method->largeDeclaration();
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp/classes/project/methods/url"><span class="access">public</span> <span class="function">function</span> url(): <span class="typehint">[type]</span></a>';
        $this->assertTrue($expected == $declaration, 'declaration: '. $declaration);
    }

    public function testClassMethodLargeDeclarationReturnProjectClass()
    {
        $project = new Project($this->versionPath());
        $object = $project->objectWithFullName(Project::class);
        $method = $object->methodWithName('objectWithFullName');
        $declaration = $method->largeDeclaration();
        $expected = '<a class="call-signature" href="/documenter-php/v0-0-0/eightfold-documenterphp/classes/project/methods/objectwithfullname"><span class="access">public</span> <span class="function">function</span> objectWithFullName(<span class="typehint">[type]</span> <span class="parameter">$fullName</span>): <span class="typehint">Class_|Trait_|Interface_|[Something]</span></a>';
        $this->assertTrue($expected == $declaration, 'declaration: '. $declaration);
    }
}
