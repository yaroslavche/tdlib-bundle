<?php

namespace Yaroslavche\TDLibBundle\Tests;

use PHPUnit\Framework\TestCase;
use Yaroslavche\TDLibBundle\Service\TDLib;

class FunctionalTest extends TestCase
{
    /**
     * @var Kernel $kernel 
     */
    private $kernel;
    /**
     * @var TDLib $tdlibService
     */
    private $tdlibService;

    protected function setUp(): void
    {
        $this->kernel = new Kernel();
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $this->tdlibService = $container->get('yaroslavche_tdlib.service.tdlib');
    }

    public function testServiceWiring()
    {
        $this->assertInstanceOf(TDLib::class, $this->tdlibService);
    }
}
