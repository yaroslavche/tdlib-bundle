<?php

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\TDLib\Response;

class UpdateOption extends Response
{
    const OPTION_VALUE_INTEGER = 'optionValueInteger';
    const OPTION_VALUE_BOOLEAN = 'optionValueBoolean';
    const OPTION_VALUE_STRING = 'optionValueString';

    /** @var string|null $name */
    private $name;
    /** @var string|null $value */
    private $value;
    /** @var string|null $valueType */
    private $valueType;

    /**
     * UpdateOption constructor.
     * @param string $rawResponse
     * @throws InvalidResponseException
     */
    public function __construct(string $rawResponse)
    {
        parent::__construct($rawResponse);
        $response = json_decode($rawResponse);
        if (!property_exists($response, 'name')) {
            throw new InvalidResponseException('', InvalidResponseException::UNRECOGNIZED_PROPERTY);
        }
        if (!property_exists($response, 'value')) {
            throw new InvalidResponseException('', InvalidResponseException::UNRECOGNIZED_PROPERTY);
        }
        $optionValue = $response->value;
        if (!property_exists($optionValue, 'value') || !property_exists($optionValue, '@type')) {
            throw new InvalidResponseException('', InvalidResponseException::UNRECOGNIZED_PROPERTY);
        }
        $this->name = $response->name;
        $this->value = $optionValue->value;
        $this->valueType = $optionValue->{'@type'};
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getValueType(): ?string
    {
        return $this->valueType;
    }
}
