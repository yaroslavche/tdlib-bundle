<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YaroslavcheTDLibBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new DependencyInjection\YaroslavcheTDLibExtension();
        }

        return $this->extension;
    }
}
