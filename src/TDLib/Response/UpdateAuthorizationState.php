<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\TDLib\AbstractResponse;
use Yaroslavche\TDLibBundle\TDLib\ResponseInterface;

class UpdateAuthorizationState extends AbstractResponse implements ResponseInterface
{
    public const AUTHORIZATION_STATE_READY = 'authorizationStateReady';
    public const AUTHORIZATION_STATE_WAIT_PHONE_NUMBER = 'authorizationStateWaitPhoneNumber';
    public const AUTHORIZATION_STATE_WAIT_CODE = 'authorizationStateWaitCode';
    public const AUTHORIZATION_STATE_WAIT_TDLIB_PARAMETERS = 'authorizationStateWaitTdlibParameters';
    public const AUTHORIZATION_STATE_WAIT_ENCRYPTION_KEY = 'authorizationStateWaitEncryptionKey';
    public const AUTHORIZATION_STATE_LOGGING_OUT = 'authorizationStateLoggingOut';

    /** @var string|null */
    private $authorizationState;

    public function __construct(string $rawResponse)
    {
        parent::__construct($rawResponse);
        $authorizationStateProperty = $this->getProperty('authorization_state');
        $authorizationState = $authorizationStateProperty->{'@type'};
        if (!in_array($authorizationState, [
            static::AUTHORIZATION_STATE_READY,
            static::AUTHORIZATION_STATE_WAIT_PHONE_NUMBER,
            static::AUTHORIZATION_STATE_WAIT_CODE,
            static::AUTHORIZATION_STATE_WAIT_TDLIB_PARAMETERS,
            static::AUTHORIZATION_STATE_WAIT_ENCRYPTION_KEY,
            static::AUTHORIZATION_STATE_LOGGING_OUT,
        ])) {
            throw new InvalidResponseException($authorizationState, InvalidResponseException::INVALID_VALUE);
        }
        $this->authorizationState = $authorizationState;
    }

    /**
     * @return string|null
     */
    public function getAuthorizationState(): ?string
    {
        return $this->authorizationState;
    }
}
