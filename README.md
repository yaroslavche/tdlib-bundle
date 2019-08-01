## Installation and configuration
Create file `config/packages/yaroslavche_tdlib.yaml` with following content
```yaml
# config/packages/yaroslavche_tdlib.yaml

yaroslavche_tdlib:
  parameters:
    use_test_dc: true
    database_directory: "/var/tmp/tdlib"
    files_directory: "/var/tmp/tdlib"
    use_file_database: true
    use_chat_info_database: true
    use_message_database: true
    use_secret_chats: true
    api_id: 11111
    api_hash: 'abcdef1234567890abcdef1234567890'
    system_language_code: "en"
    device_model: "php"
    system_version: "7.2"
    application_version: "0.0.1"
    enable_storage_optimizer: true
    ignore_file_names: true
  client:
    encryption_key: ""
    default_timeout: 0.5
    auto_init: true
```
and install bundle with `composer`
```bash
composer require yaroslavche/tdlib-bundle
```

## Usage

### JsonClient
```php
<?php

use TDApi\LogConfiguration;
use Yaroslavche\TDLibBundle\TDLib\JsonClient;
use Yaroslavche\TDLibBundle\TDLib\Response\UpdateAuthorizationState;

LogConfiguration::setLogVerbosityLevel(LogConfiguration::LVL_FATAL_ERROR);
$tdlibParameters = [/** from config */];
$clientConfig = [/** from config */];
$client = new JsonClient($tdlibParameters, $clientConfig);

var_dump($client->getOption('version'));

$authorizationStateResponse = $client->getAuthorizationState();
if ($authorizationStateResponse->getType() === UpdateAuthorizationState::AUTHORIZATION_STATE_WAIT_PHONE_NUMBER)
{
    $client->setAuthenticationPhoneNumber('+380991234567');
}
else if ($authorizationStateResponse->getType() === UpdateAuthorizationState::AUTHORIZATION_STATE_READY)
{
    var_dump($client->getMe());
}
```

### TDLib Service
Service provide `getJsonClient` method, which will return `Yaroslavche\TDLibBundle\TDLib\JsonClient`. Inject service and use as you need. For example:
```php
use Yaroslavche\TDLibBundle\Service\TDLib;

final class SearchPublicChatController
{
    /**
     * @Route("/getMe", name="getMe")
     */
    public function __invoke(TDLib $tdlib): Response
    {
        $tdlib->getJsonClient()->getMe();
    }
}
```
