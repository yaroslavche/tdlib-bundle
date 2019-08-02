<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;
use Yaroslavche\TDLibBundle\TDLib\AbstractResponse;
use Yaroslavche\TDLibBundle\TDLib\ResponseInterface;

class UpdateOption extends AbstractResponse implements ResponseInterface
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
        $this->name = $this->getProperty('name');
        $optionValue = $this->getProperty('value');
        $this->value = $optionValue->value;
        $this->valueType = $optionValue->{'@type'};
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|bool|int|null
     */
    public function getValue()
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
