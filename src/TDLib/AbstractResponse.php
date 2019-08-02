<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib;

use stdClass;
use Yaroslavche\TDLibBundle\Exception\InvalidResponseException;

class AbstractResponse implements ResponseInterface
{
    /** @var string $rawResponse */
    private $rawResponse;
    /** @var stdClass|null $response */
    private $response;
    /** @var string|null $type */
    private $type;
    /** @var string|null $extra */
    private $extra;

    /**
     * @param string $rawResponse
     * @return ResponseInterface
     * @throws InvalidResponseException
     */
    public static function fromRaw(string $rawResponse): ResponseInterface
    {
        return new static($rawResponse);
    }

    /**
     * AbstractResponse constructor.
     * @param string $rawResponse
     * @throws InvalidResponseException
     */
    public function __construct(string $rawResponse)
    {
        $response = json_decode($rawResponse);
        if (!$response instanceof stdClass) {
            throw new InvalidResponseException('', InvalidResponseException::INVALID_JSON);
        }
        if (!property_exists($response, '@type')) {
            throw new InvalidResponseException('', InvalidResponseException::UNRECOGNIZED_TYPE);
        }
        $this->rawResponse = $rawResponse;
        $this->response = $response;
        $this->type = $response->{'@type'};
        $this->extra = property_exists($response, '@extra') ? $response->{'@extra'} : null;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @param string $name
     * @return mixed|null
     * @throws InvalidResponseException
     */
    protected function getProperty(string $name)
    {
        if (!property_exists($this->response, $name)) {
            throw new InvalidResponseException('', InvalidResponseException::UNRECOGNIZED_PROPERTY);
        }
        return $this->response->{$name};
    }
}
