<?php
namespace Eightfold\DocumenterPhp\tests;

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
}
