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
    phone_number: "+380991234567"
    encryption_key: ""
    default_timeout: 0.5
    auto_init: false
```
and install bundle with `composer`
```bash
composer require yaroslavche/tdlib-bundle
```

## WebSocket server with initialized JsonClient (experimental)
```bash
$ bin/console tdlib:start --port=12345 --api_id=11111 --api_hash=abcdef1234567890abcdef1234567890 --phone_number=+380991234567
```
```js
const ws = new WebSocket('ws://127.0.0.1:12345');
ws.onopen = function () {
    console.log('Socket connection opened properly.');
    ws.send('{"@type": "getAuthorizationState"}');
    ws.send('{"@type": "getMe"}');
};

ws.onmessage = function (evt) {
    console.log(evt.data);
};
```
will produce
```log
Socket connection opened properly.
{"@type":"authorizationStateReady","@extra":1681692777}
{"@type":"user","id":11111,"first_name":"yaroslav","last_name":"","username":"","phone_number":"380991234567","status":{"@type":"userStatusOffline","was_online":1565015419},"outgoing_link":{"@type":"linkStateKnowsPhoneNumber"},"incoming_link":{"@type":"linkStateKnowsPhoneNumber"},"is_verified":false,"is_support":false,"restriction_reason":"","have_access":true,"type":{"@type":"userTypeRegular"},"language_code":"","@extra":1714636915}
```
For now can be checked on profiler data collector page (will be removed later).

## Data Collector
With installed `symfony/profiler-pack`.

*If reached timeout exception, then seems bundle config is passed, but can't connect to client. Should configure for real one `api_id`, `api_hash` and `phone_number` (can be `test_dc`). 

## Usage

### JsonClient
```php
use TDApi\LogConfiguration;
use Yaroslavche\TDLibBundle\TDLib\JsonClient;
use Yaroslavche\TDLibBundle\TDLib\Response\UpdateAuthorizationState;

LogConfiguration::setLogVerbosityLevel(LogConfiguration::LVL_FATAL_ERROR);
$tdlibParameters = [/** from config */];
$clientConfig = [/** from config */];
$client = new JsonClient($tdlibParameters, $clientConfig);

if ($clientConfig['auto_init'] === false)
{
    $client->initJsonClient();
}

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
Service provides `getJsonClient` method, which will return `Yaroslavche\TDLibBundle\TDLib\JsonClient`. Inject service and use as you need. For example:
```php
use Yaroslavche\TDLibBundle\Service\TDLib;

final class GetMeController
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
