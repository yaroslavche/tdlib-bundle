<?php

namespace Yaroslavche\TDLibBundle\Tests;

use PHPUnit\Framework\TestCase;
use TDApi\LogConfiguration;
use Yaroslavche\TDLibBundle\Service\TDLib;
use Yaroslavche\TDLibBundle\TDLib\JsonClient;

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

    /** @todo need to create a mock */
    protected function setUp(): void
    {
        $this->kernel = new Kernel();
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        LogConfiguration::setLogVerbosityLevel(LogConfiguration::LVL_ERROR);
        $this->tdlibService = $container->get('yaroslavche_tdlib.service.tdlib');
    }

    public function testServiceWiring()
    {
        $this->assertInstanceOf(TDLib::class, $this->tdlibService);
    }

    public function testJsonClient()
    {
        $jsonClient = $this->tdlibService->getJsonClient();
        $this->assertInstanceOf(JsonClient::class, $jsonClient);
        $this->assertSame('1.4.0', $jsonClient->getOption('version'));
    }
}
