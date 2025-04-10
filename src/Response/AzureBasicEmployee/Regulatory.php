<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureBasicEmployee;

use Spatie\LaravelData\Data;

class Regulatory extends Data
{
    public function __construct(
        public ?object $i9,
        public ?object $canada,
        public ?object $ssnName,
        public ?string $ten42Recipient,
        public ?string $ipedsSoftMoney,
    ) {}
}
