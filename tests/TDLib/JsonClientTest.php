<?php

namespace Yaroslavche\TDLibBundle\Tests\TDLib;

use PHPUnit\Framework\TestCase;
use TDApi\LogConfiguration;
use Yaroslavche\TDLibBundle\TDLib\JsonClient;
use Yaroslavche\TDLibBundle\Tests\Kernel;

class JsonClientTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        LogConfiguration::setLogVerbosityLevel(LogConfiguration::LVL_FATAL_ERROR);
        $parameters = Kernel::getBundleConfig()['parameters'] ?? [];
        $client = Kernel::getBundleConfig()['client'] ?? [];
        $this->client = new JsonClient($parameters, $client);
    }

    public function testVersion()
    {
        $clientVersion = $this->client->getOption('version');
        $this->assertSame('1.3.0', $clientVersion);
    }
}
