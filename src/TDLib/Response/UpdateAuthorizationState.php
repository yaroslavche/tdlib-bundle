<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\TDLib\AbstractResponse;

class UpdateAuthorizationState extends AbstractResponse
{
    public const AUTHORIZATION_STATE_READY = 'authorizationStateReady';
    public const AUTHORIZATION_STATE_WAIT_PHONE_NUMBER = 'authorizationStateWaitPhoneNumber';
    public const AUTHORIZATION_STATE_WAIT_CODE = 'authorizationStateWaitCode';
}
