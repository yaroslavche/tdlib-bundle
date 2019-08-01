<?php

namespace Yaroslavche\TDLibBundle\TDLib;

use Symfony\Component\OptionsResolver\OptionsResolver;
use TDApi\TDLibParameters;
use TDLib\JsonClient;
use Yaroslavche\TDLibBundle\Exception\InvalidApiHashException;
use Yaroslavche\TDLibBundle\Exception\InvalidApiIdException;
use Yaroslavche\TDLibBundle\Exception\InvalidArgumentException;
use Yaroslavche\TDLibBundle\Exception\InvalidDatabaseEncryptionKeyException;
use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\Exception\InvalidTdlibParametersException;
use Yaroslavche\TDLibBundle\TDLib\Response\UpdateAuthorizationState;
use Yaroslavche\TDLibBundle\TDLib\Response\UpdateOption;

abstract class AbstractJsonClient
{
    /** @var JsonClient $jsonClient */
    private $jsonClient;
    /** @var OptionsResolver $optionsResolver */
    private $optionsResolver;
    /** @var string[]|bool[]|int[] $tdlibParameters */
    private $tdlibParameters;
    /** @var string[]|bool[]|int[] $clientConfig */
    private $clientConfig;
    /** @var string[]|bool[]|int[] $options */
    private $options;
    /** @var string|null $authorizationState */
    private $authorizationState;

    /**
     * AbstractClient constructor.
     * @param string[]|int[]|bool[] $tdlibParameters
     * @param string[] $clientConfig
     * @throws InvalidApiHashException
     * @throws InvalidApiIdException
     * @throws InvalidArgumentException
     * @throws InvalidDatabaseEncryptionKeyException
     * @throws InvalidResponseException
     * @throws InvalidTdlibParametersException
     */
    public function __construct(array $tdlibParameters, array $clientConfig)
    {
        $this->optionsResolver = new OptionsResolver();
        $this->tdlibParameters = $this->resolve($tdlibParameters, [
            TDLibParameters::USE_TEST_DC => true,
            TDLibParameters::DATABASE_DIRECOTRY => '/var/tmp/tdlib',
            TDLibParameters::FILES_DIRECTORY => '/var/tmp/tdlib',
            TDLibParameters::USE_FILE_DATABASE => true,
            TDLibParameters::USE_CHAT_INFO_DATABASE => true,
            TDLibParameters::USE_MESSAGE_DATABASE => true,
            TDLibParameters::USE_SECRET_CHATS => true,
            TDLibParameters::API_ID => null,
            TDLibParameters::API_HASH => null,
            TDLibParameters::SYSTEM_LANGUAGE_CODE => 'en',
            TDLibParameters::DEVICE_MODEL => php_uname('s'),
            TDLibParameters::SYSTEM_VERSION => php_uname('v'),
            TDLibParameters::APPLICATION_VERSION => '0.0.1',
            TDLibParameters::ENABLE_STORAGE_OPTIMIZER => true,
            TDLibParameters::IGNORE_FILE_NAMES => true,
        ]);
        /**
         * @todo
         *      - check if directories exists and accessible and will be used
         *      - enable debug if test_dc true?
         *      - check other important (?)
         */
        $this->clientConfig = $this->resolve($clientConfig, [
            'encryption_key' => '',
            'default_timeout' => 0.5,
            'auto_init' => true
        ]);
        if (false !== $this->clientConfig['auto_init']) {
            $this->initJsonClient();
        }
    }

    /**
     * @param string $type
     * @param string[]|bool[]|int[]|array[] $params
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    public function query(string $type, array $params = []): ResponseInterface
    {
        $query = json_encode(array_merge(['@type' => $type], $params));
        if (!$query) {
            throw new InvalidArgumentException();
        }
        return Response::fromRaw($this->jsonClient->query($query));
    }

    /**
     * @param string $name
     * @return bool|int|string|null
     */
    public function getOption(string $name)
    {
        return $this->options[$name];
    }

    /**
     * @param bool|null $force
     * @throws InvalidApiHashException
     * @throws InvalidApiIdException
     * @throws InvalidArgumentException
     * @throws InvalidDatabaseEncryptionKeyException
     * @throws InvalidResponseException
     * @throws InvalidTdlibParametersException
     */
    public function initJsonClient(?bool $force = null): void
    {
        if (!$force && $this->jsonClient instanceof JsonClient) {
            return;
        }
        if (!isset($this->tdlibParameters[TDLibParameters::API_ID])) {
            throw new InvalidApiIdException();
        }
        if (!isset($this->tdlibParameters[TDLibParameters::API_HASH])) {
            throw new InvalidApiHashException();
        }
        $this->jsonClient = new JsonClient();
        $this->jsonClient->setDefaultTimeout(floatval($this->clientConfig['default_timeout']));

        $setParametersResponse = $this->query('setTdlibParameters', [
            'parameters' => $this->tdlibParameters,
        ]);
        if ($setParametersResponse->getType() !== 'ok') {
            throw new InvalidTdlibParametersException();
        }
        /** set database encryption key */
        $setEncryptionKeyResult = $this->query('setDatabaseEncryptionKey', [
            'new_encryption_key' => $this->clientConfig['encryption_key'] ?? ''
        ]);
        if ($setEncryptionKeyResult->getType() !== 'ok') {
            throw new InvalidDatabaseEncryptionKeyException();
        }

        /** check all received responses */
        $this->handleResponses();
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $defaults
     * @return mixed[]
     */
    protected function resolve(array $options, array $defaults = []): array
    {
        $this->optionsResolver->clear();
        $this->optionsResolver->setDefaults($defaults);
        return $this->optionsResolver->resolve($options);
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidResponseException
     */
    protected function handleResponses(): void
    {
        $responses = $this->jsonClient->getReceivedResponses();
        foreach ($responses as $rawResponse) {
            $responseObject = Response::fromRaw($rawResponse);
            switch ($responseObject->getType()) {
                case 'updateOption':
                    $this->updateOption(UpdateOption::fromRaw($rawResponse));
                    break;
                case 'updateAuthorizationState':
                    $this->updateAuthorizationState(UpdateAuthorizationState::fromRaw($rawResponse));
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param ResponseInterface $updateOptionResponse
     * @throws InvalidArgumentException
     */
    protected function updateOption(ResponseInterface $updateOptionResponse): void
    {
        if (!$updateOptionResponse instanceof UpdateOption) {
            throw new InvalidArgumentException();
        }
        $value = $updateOptionResponse->getValue();
        switch ($updateOptionResponse->getValueType()) {
            case UpdateOption::OPTION_VALUE_INTEGER:
                $value = (int)$value;
                break;
            case UpdateOption::OPTION_VALUE_BOOLEAN:
                $value = (bool)$value;
                break;
            case UpdateOption::OPTION_VALUE_STRING:
            default:
                break;
        }
        $this->options[$updateOptionResponse->getName()] = $value;
    }

    /**
     * @param ResponseInterface $updateAuthorizationStateResponse
     * @throws InvalidArgumentException
     */
    protected function updateAuthorizationState(ResponseInterface $updateAuthorizationStateResponse): void
    {
        if (!$updateAuthorizationStateResponse instanceof UpdateAuthorizationState) {
            throw new InvalidArgumentException();
        }
        $this->authorizationState = $updateAuthorizationStateResponse->getType();
    }
}