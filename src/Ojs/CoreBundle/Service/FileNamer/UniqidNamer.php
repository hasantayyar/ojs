<?php

namespace Ojs\CoreBundle\Service\FileNamer;

use Ojs\CoreBundle\Helper\FileHelper;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class UniqidNamer implements NamerInterface
{
    public function name(FileInterface $file)
    {
        $fileHelper = new FileHelper();

        $fileName = sprintf('%s.%s', uniqid(), $file->getExtension());

        return $fileHelper->generatePath($fileName, false).$fileName;
    }
}
