<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\Service;

use TDLib\JsonClient;

Error_Reporting(E_ALL);
ini_set('display_errors', '1');

class TDLibService
{
    /**
     * @var JsonClient $client
     */
    private $client;

    /**
     * @var int $apiId
     */
    private $apiId;

    /**
     * @var string $apiHash
     */
    private $apiHash;

    public function __construct(int $apiId, string $apiHash)
    {
        $this->apiId = $apiId;
        $this->apiHash = $apiHash;
        $this->client = new JsonClient();
        dump($this->client);
    }

}
