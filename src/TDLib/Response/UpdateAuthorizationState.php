<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\TDLib\Response;

class UpdateAuthorizationState extends Response
{
    public const AUTHORIZATION_STATE_READY = 'authorizationStateReady';
    public const AUTHORIZATION_STATE_WAIT_PHONE_NUMBER = 'authorizationStateWaitPhoneNumber';
    public const AUTHORIZATION_STATE_WAIT_CODE = 'authorizationStateWaitCode';
}
