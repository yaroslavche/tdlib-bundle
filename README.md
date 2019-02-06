## Installation and configuration
Install bundle with `composer`
```bash
composer require yaroslavche/tdlib-bundle
```
and create file `config/packages/tdlib.yaml` with following content
```yaml
# config/packages/tdlib.yaml

yaroslavche_tdlib:
  api_id: 11111
  api_hash: 'abcdef1234567890abcdef1234567890'
```

## Usage

### TDLibService
Inject service and use as you need. For example:
```php
use Yaroslavche\TDLibBundle\Service\TDLibService;

final class SearchPublicChatController
{
    /**
     * @Route("/searchPublicChat", name="searchPublicChat")
     */
    public function __invoke(TDLibService $tdlibService): Response
    {
        return $tdlibService->searchPublicChat('telegram')
    }
}
```