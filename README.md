## Installation
```bash
$ composer require yaroslavche/tdlib-bundle
```

## WebSocket server with initialized JsonClient (experimental)
Create console application (`console.php`):
```php
#!/usr/bin/env php
<?php
# console.php

require './vendor/autoload.php';

use Symfony\Component\Console\Application;
use Yaroslavche\TDLibBundle\Command\TDLibStartCommand;

$application = new Application();

$application->add(new TDLibStartCommand());

$application->run(); 

```
and run:
```bash
$ php console.php tdlib:start --port=12345 --api_id=11111 --api_hash=abcdef1234567890abcdef1234567890
```
Then create HTML file (`index.html`)
```html
<!-- index.html -->
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8"/>
    <title>TDLib WebSocket Example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <style>

    </style>
</head>
<body>
<div id="tdlib">
    <div class="ui container">
        <div class="ui comments fluid">
            <form class="ui reply form">
                <div class="field">
                    <textarea ref="query">{"@type": "getAuthorizationState"}</textarea>
                </div>
                <div class="ui primary submit labeled icon button" @click="query">
                    <i class="icon paper plane"></i> Query
                </div>
            </form>
            <div class="comment" v-for="(entry, key) in log" :key="key">
                <div class="content">
                    <div class="metadata">
                        <div class="date">#{{ key }}</div>
                    </div>
                    <div class="text">
                        <p>{{ entry }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script>
    const tdlib = new Vue({
        el: '#tdlib',
        data: {
            ws: null,
            log: [],
        },
        methods: {
            query: function () {
                const query = this.$refs.query.value;
                this.log.push('Query: ' + query);
                this.ws.send(query);
            },
            initWSConnection: function () {
                this.ws = new WebSocket('ws://127.0.0.1:12345');
                this.ws.onopen = () => {
                    this.log.push('Socket connection opened properly.');
                };

                this.ws.onmessage = (event) => {
                    this.log.push('Response: ' + event.data);
                };
            }
        },
        mounted() {
            this.initWSConnection();
        }
    });
</script>
</body>
</html>
```
Open in browser. Queries:
```
{"@type": "setAuthenticationPhoneNumber", "phone_number": "+380991234567"}
{"@type": "checkAuthenticationCode", "code": "12345"}
```
And [others](https://core.telegram.org/tdlib/docs/classtd_1_1td__api_1_1_function.html).

# Symfony bundle configuration
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
