# Documenter for Php by 8fold

Documenter is a library (more of an extension or wrapper) based on [phpDocumentor](https://www.phpdoc.org), which, as of this writing, is the more popular base library for interacting with inline documentation for PHP projects.

There are a multitude of generators available for creating documentation sites for PHP projects. Many of those generated static sites (HTML or other files) for presenting on the web. This makes sense for two primary reasons:

1. content is static (the project's PHP files don't typically change once captured) and 
2. performance (large projects can contain hundreds or even throusands of files).

However, when examining projects at 8fold (and a few others), we found these two considerations no longer carrying the weight they once would have. As PHP projects become more modular, the number of files to capture documentation for is becoming less and less. Further, PHP and its related Zend Framework are actually pretty fast. Finally, various modern development techniques can also reduce the load put on the server to generate the documentation. (See <a href="#performance">Performance section</a> for more details.)

At 8fold, we also found limitations with the static site generators, which inspired us to try something different.

Add Documenter to a PHP project and deliver the documentation via web-based APIs. Add Documenter to a dynamic site and integrate it seemlessly (no need to switch from dynamic content delivery to static content delivery). Change your return strings or template files without having to do anything else to update all project versions to the new look and feel.

## DocBlocks

DocBlocks are the main way to document your code and make it ready for Documenter. For PHP a DocBlock appears between a forward slash followed by two asterisks (/**) and single asterisk followed by a forward slash (*/). Further, they appear just *above* the element being documented.

```php
/**
 * This is the short description of the DocBlock.
 *
 * The long description begins one empty line (paragraph) below the short description.
 */
class HelloWorld
{
    /**
     * Short description
     */
    private function print()
    {
       // Comments such as these do not count.
    }
}
```

Documenter is not all that opinionated when it comes to the *way* you write your DocBlocks; therefore, you can read the [phpDocumentor documentation](https://www.phpdoc.org/docs/latest/guides/index.html) to get a handle on writing documentation in your code.

Having said that, the following are some things to consider and be aware of when working with Documenter.

**Categories:** phpDocumentor has [deprecated use of the `@category` tag](https://www.phpdoc.org/docs/latest/references/phpdoc/tags/category.html). We understand and agree with the desire to deprecate this tag given what it was initially designed to do. However, semantically, it is the most appropriate tag name for grouping various elements to generate a site; therefore, Documenter does take advantage of this tag for the purposes of bucketing methods, properties, classes, traits, and so on. 

Note: Think of categories like sections on a page, but asking you to update all your `@category` tags to `@section` seems like a lot to ask all things considered.

**Return types:** If you have a method that returns an instance of an object within your project, you can create a more robust user experience by documenting this type as the full class namespace preceded with a backslash.

```php
// We have a class with a full name of: Hello\World\Class

/**
 * @return \Hello\World\Class
 */
public function isntance() {}
```

When Documenter returns the tring for the type hint, the string will be "Class" indicating it is a class *within* the current project. Further, by setting the `withLink` parameter to `true` when getting the display string, an anchor to the URL of the object will also be present.

```html
<a href="[project-slug]/[version-slug]/hello-world/class">Class</a>
```

For classes that are *not* in the scope of the project, Documenter still only returns the class name (without the name space), but wraps the name in square brackets ([]).

```php
Something\Outside\The\Project\Space\SomeClass
```

Becomes:

```html
[SomeClass]
```

The important thing is the backslash at the beginning of the namespace. Further, this functionality applies to the parents of classes.

```php
use Hello\World\Class

use Something\Outside\The\Project\Space\SomeClass 

class MyLocalClass extends Class
{

}

class MyExternalClass extends SomeClass
{

}
```

The result for getting the display string of the parents would be the same as the previous outputs.

<h2 id="performance">Performance</h2>

Documenter is designed to only instantiate or calculate when absolutely necessary (lazy loading). Documenter is designed to hold onto objects and values once they have been instantiated or calculated (caching).

Documenter has not been performance tested; if performance in live applications ever becomes a noticeable issue, we will do so. Having said that, it is estimated that the longest operation in Documenter is going to be in setting up the initial array of files for a Project.

To start Documenter, you instantiate Project by passing in the path to the project:

```php
// Path to the directory containing the files to inspect.
$path = '/path/to/project/version';

// Root 
// (to help increase performance, only process files within 
// a cerain root folder).
$root = '/src';

// Ignore
// (to help increase performance, tell Documenter to skip 
// certain files. Note: Only .php files are processed)
$ignore = ['something.php'];

$project = new Project($path, $root, $ignore);
```

At this point in the lifecycle, no heavy processing or instantiation has occurred.

```php
// Get the total number of files being considered in the 
// project.
$project->totalFiles;
```

This fires the loop that iterates through the project folder looking for `.php` files (that takes time). Each time a file is found worther caring about a File object is instantiated (takes time). Each time a File object is instantiated, phpDocumentor's `process()` FileReflector method is fired (takes time). We are talking microseconds here, not whole seconds. Having said that, if we could put off calling `process()` and only firing it once per File instance, we might see a performance gain...but maybe not one worth writing home about.

The reason it may not be worth writing home about is because each File instance is stored in array on the Project object. Further, you typically don't interact with the Files array. Instead, you interact with arrays of objects *within* those Files. And, these are only instantiated when you try to get one of those arrays.

```php
// Get array of all the classes in the project.
$classes = $projects->classes();
```

We loop over all the Files and use phpDocumentor methods to get the classes within the file and instantiate a Class_ object. All the Class_ instances are added to an array for future reference.

Between the performance of phpDocumentor and using these techniques, performance has been a non-issue as of today. If that changes, steps will be taken to improve.

## Generated URL patterns

Note: It was debated on whether to include namespaces in the URLs as they can become quite verbose; however, because PHP allows projects to use multiple namespaces, it was decided to allow for that by including `object-namespace` in the URLs generated by Documenter. Further, you do *not* need to use the URLs generated by Documenter in sites that use Documenter; however, you may not be able to take advantage of some of the automation afforded you by Documenter ($object->url(), for example). Finally and, having said all that, it was determined that sites which use namespace as hierarchical url structures (/[root-namespace]/[sub-namespace]/:id) are inviting difficult navigation trees and information architectures; therefore, the whole namespace is used as a level of the URL structure.

**`/[project-slug]`** This is a prefix URL generted to allow creating a list of versions for a project.

**`/[project-slug]/[project-version-slug]`** This is the base URL generated by a given project version.

**`/[project-slug]/[project-version-slug]/[object-namespace]`** This is a prefix URL to allow creating a list of Project Objects within a given Namespace.

**`/[project-slug]/[project-version-slug]/[object-namespace]/[project-object-name]`** This is a prefix URL to allow creating a list of Class_, Trait_, Interface_, Method, and Property objcts found in the files within the project. `project-object-name` will be one of the following strings, listed in respective order to the type of object instantiated:

- classes
- traits
- interfaces
- functions
- properties

**`/[project-slug]/[project-version-slug]/[object-namespace]/[functions or properties]/[project-object-slug]`** This is the url for viewing a specific Method or Property Project Object that is not "owned" by another Project Object. `project-object-slug` is the `name` of the object converte to a slug.

Note: Most of the projects we work on do not have functions or methods that exist outside a Class_, Trait_, or Interface_. Therefore, namespace may not be a necessary consideration. *Put this analysis in the TODO column.*

**`/[project-slug]/[project-version-slug]/[object-namespace]/[classes, traits, or interfaces]/[project-object-slug]`** This is a prefix URL to allow the creation of a page listing all the Method, Properties, Trait_, and Interface_ Project Objects used by a single Class_, Trait_, or Interface_.

**`/[project-slug]/[project-version-slug]/[object-namespace]/[classes, traits, or interfaces]/[project-object-slug]/[methods or properties]`** This is a prefix URL to all the creation of apage lsiting all the Method or Property Project Objects used by a single Class_, Trait_, or Interface_. 

**`/[project-slug]/[project-version-slug]/[object-namespace]/[classes, traits, or interfaces]/[project-object-slug]/[methods or properties]/[project-object-slug]`** This URL displays a single method or property that is owned by a Class_, Trait_, or Interface_ Project Object.
