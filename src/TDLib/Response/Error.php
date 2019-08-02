<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib\Response;

use Yaroslavche\TDLibBundle\TDLib\AbstractResponse;
use Yaroslavche\TDLibBundle\TDLib\ResponseInterface;

class Error extends AbstractResponse implements ResponseInterface
{
    /** @var int|null $code */
    private $code;
    /** @var string|null $message */
    private $message;

    public function __construct(string $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->code = $this->getProperty('code');
        $this->message = $this->getProperty('message');
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
