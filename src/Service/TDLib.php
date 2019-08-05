<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\Service;

use Yaroslavche\TDLibBundle\Exception\InvalidArgumentException;
use Yaroslavche\TDLibBundle\Exception\InvalidAuthenticationCodeException;
use Yaroslavche\TDLibBundle\Exception\InvalidDatabaseEncryptionKeyException;
use Yaroslavche\TDLibBundle\Exception\InvalidPhoneNumberException;
use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\Exception\InvalidTdlibParametersException;
use Yaroslavche\TDLibBundle\TDLib\JsonClient;

class TDLib
{
    /** @var JsonClient $jsonClient */
    private $jsonClient;

    /**
     * TDLib constructor.
     * @param string[]|bool[]|int[] $parameters
     * @param string[] $client
     * @throws InvalidArgumentException
     * @throws InvalidDatabaseEncryptionKeyException
     * @throws InvalidResponseException
     * @throws InvalidTdlibParametersException
     * @throws InvalidAuthenticationCodeException
     * @throws InvalidPhoneNumberException
     */
    public function __construct(array $parameters, array $client)
    {
        $this->jsonClient = new JsonClient($parameters, $client);
    }

    /**
     * @return JsonClient
     */
    public function getJsonClient(): JsonClient
    {
        return $this->jsonClient;
    }
}
