<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\TDLib;

interface ResponseInterface
{
    public static function fromRaw(string $rawResponse): ResponseInterface;
    public function __construct(string $rawResponse);
    public function getType(): ?string;
    public function getExtra(): ?string;
}
