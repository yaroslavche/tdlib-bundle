<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\TDLib\AbstractResponse;
use Yaroslavche\TDLibBundle\TDLib\ResponseInterface;

class UpdateConnectionState extends AbstractResponse implements ResponseInterface
{
    public const CONNECTION_STATE_READY = 'connectionStateConnecting';

    /** @var string|null */
    private $state;

    public function __construct(string $rawResponse)
    {
        parent::__construct($rawResponse);
        $stateProperty = $this->getProperty('state');
        $state = $stateProperty->{'@type'};
        if (!in_array($state, [
            static::CONNECTION_STATE_READY,
        ])) {
            throw new InvalidResponseException($state, InvalidResponseException::INVALID_VALUE);
        }
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }
}
