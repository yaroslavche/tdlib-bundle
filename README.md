## Installation and configuration
Install bundle with `composer`
```bash
composer require yaroslavche/tdlib-bundle
```
and create file `config/packages/tdlib.yaml` with following content
```yaml
# config/packages/tdlib.yaml

yaroslavche_td_lib:
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
    system_version: "7.1"
    application_version: "0.1.0"
    enable_storage_optimizer: true
    ignore_file_names: true
  client:
    encryption_key: "some_secret_key"
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
        // searchPublicChat method not implemented yet - but you can do it by yourself in Service/TDLibService.php
        return $tdlibService->searchPublicChat('telegram')
    }
}
```

Controller example:
```php
<?php

namespace App\Controller;

use App\Form\AuthenticationCodeType;
use App\Form\PhoneNumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Yaroslavche\TDLibBundle\Service\TDLibService;

class TDLibController extends AbstractController
{
    /**
     * @var TDLibService $tdlibService
     */
    protected $tdlibService;

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * TDLibController constructor.
     * @param TDLibService $tdlibService
     * @param RequestStack $requestStack
     */
    public function __construct(TDLibService $tdlibService, RequestStack $requestStack)
    {
        $this->tdlibService = $tdlibService;
        $this->requestStack = $requestStack;
    }

    public function getAuthorizationState(): ?Response
    {
        $authorizationState = $this->tdlibService->getAuthorizationState();
        if ($authorizationState === TDLibService::AUTHORIZATION_STATE_READY) {
            return null;
        }
        switch ($authorizationState) {
            case TDLibService::AUTHORIZATION_STATE_WAIT_PHONE_NUMBER:
                return $this->showPhoneNumberForm();
            case TDLibService::AUTHORIZATION_STATE_WAIT_CODE:
                return $this->showAuthenticationCodeForm();
            default:
                dump('implement handling ' . $authorizationState);
                return null;
        }
    }

    public function showPhoneNumberForm(): ?Response
    {
        $form = $this->createForm(PhoneNumberType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->tdlibService->setAuthenticationPhoneNumber($formData['phone_number']);
            return $this->getAuthorizationState();
        } else {
            return $this->render('telegram/authorization/phone_number.html.twig', ['form' => $form->createView()]);
        }
    }

    public function showAuthenticationCodeForm(): ?Response
    {
        $form = $this->createForm(AuthenticationCodeType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->tdlibService->checkAuthenticationCode($formData['code'], $formData['first_name'], $formData['last_name']);
            return null;
        } else {
            return $this->render('telegram/authorization/authentication_code.html.twig', ['form' => $form->createView()]);
        }
    }
}

```