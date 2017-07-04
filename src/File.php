<?php

namespace Eightfold\DocumenterPhp;

use phpDocumentor\Reflection\FileReflector;

class File extends FileReflector
{
    public function __construct($file, $validate = false, $encoding = 'utf-8')
    {
        parent::__construct($file, $validate, $encoding);

        // TODO: This will probably be the biggest performance hit during
        // initialization; therefore, I wwonder if we can deprecate the need for this
        // upon instantiating the File.
        parent::process();
    }
}
