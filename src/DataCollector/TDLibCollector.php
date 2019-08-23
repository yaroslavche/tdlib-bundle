<?php

namespace Yaroslavche\TDLibBundle\DataCollector;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Yaroslavche\TDLibBundle\Service\TDLib;
use Yaroslavche\TDLibBundle\TDLib\ResponseInterface;

class TDLibCollector extends DataCollector
{
    const DATA_COLLECTOR_NAME = 'yaroslavche_tdlib.data_collector.tdlib';

    /** @var TDLib $config */
    private $tdlib;

    /**
     * ConfigCollector constructor.
     * @param TDLib $tdlib
     */
    public function __construct(TDLib $tdlib)
    {
        $this->tdlib = $tdlib;
    }


    /**
     * Collects data for the given Request and Response.
     * @param Request $request
     * @param Response $response
     * @param Exception|null $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        try {
            $this->data['version'] = $this->tdlib->getJsonClient()->getOption('version');
            $this->data['authorizationState'] = $this->tdlib->getJsonClient()->getAuthorizationState();
        } catch (Exception $exception) {
        }
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return static::DATA_COLLECTOR_NAME;
    }

    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->data['version'];
    }

    /**
     * @return ResponseInterface
     */
    public function getAuthorizationState(): ResponseInterface
    {
        return $this->data['authorizationState'];
    }
}
