<?php

namespace Eightfold\DocumenterPhp;

use phpDocumentor\Reflection\FileReflector;

class File extends FileReflector
{
    public function __construct($file, $validate = false, $encoding = 'utf-8')
    {
        parent::__construct($file, $validate, $encoding);
        parent::process();
    }
}
