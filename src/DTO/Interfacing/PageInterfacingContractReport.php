<?php

declare(strict_types=1);

namespace App\Paging\DTO\Interfacing;

final readonly class PageInterfacingContractReport
{
    public function __construct(
        public string $providerInterface,
        public string $payloadClass,
        public string $renderViewClass,
        public string $publicController,
        public array $requiredPayloadFields,
        public array $requiredRenderHintFields,
        public array $publicRoutes,
        public array $templateContracts,
    ) {
    }

    public function passed(): bool
    {
        return [] !== $this->requiredPayloadFields && [] !== $this->requiredRenderHintFields && [] !== $this->publicRoutes && [] !== $this->templateContracts;
    }

    public function toArray(): array
    {
        return [
            'component' => 'paging',
            'consumer' => 'interfacing',
            'status' => $this->passed() ? 'ready' : 'incomplete',
            'providerInterface' => $this->providerInterface,
            'payloadClass' => $this->payloadClass,
            'renderViewClass' => $this->renderViewClass,
            'publicController' => $this->publicController,
            'requiredPayloadFields' => $this->requiredPayloadFields,
            'requiredRenderHintFields' => $this->requiredRenderHintFields,
            'publicRoutes' => $this->publicRoutes,
            'templateContracts' => $this->templateContracts,
        ];
    }
}
