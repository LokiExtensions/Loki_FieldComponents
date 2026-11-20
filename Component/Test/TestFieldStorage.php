<?php declare(strict_types=1);

namespace Loki\Field\Component\Test;

use JsonException;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Session\Generic as Session;
use Throwable;

/**
 * Session storage for the field components on the test page (layout handle `loki_field_components_index_test`).
 * Writes are only allowed in developer mode, or in production mode when the deployment configuration
 * `loki_functional_tests/allow_production` is set to true.
 */
class TestFieldStorage
{
    private const SESSION_KEY = 'loki_field_components_test';
    private const MAX_VALUE_SIZE = 4096;
    private const ALLOW_PRODUCTION_CONFIG_PATH = 'loki_functional_tests/allow_production';

    public function __construct(
        private readonly Session $session,
        private readonly AppState $appState,
        private readonly DeploymentConfig $deploymentConfig,
    ) {
    }

    public function getValue(string $componentName): mixed
    {
        return $this->getValues()[$componentName] ?? null;
    }

    public function hasValue(string $componentName): bool
    {
        return array_key_exists($componentName, $this->getValues());
    }

    public function saveValue(string $componentName, mixed $value): void
    {
        if (false === $this->isAllowed($value)) {
            return;
        }

        $values = $this->getValues();
        $values[$componentName] = $value;
        $this->session->setData(self::SESSION_KEY, $values);
    }

    public function reset(): void
    {
        $this->session->setData(self::SESSION_KEY, []);
    }

    private function getValues(): array
    {
        return (array)$this->session->getData(self::SESSION_KEY);
    }

    private function isAllowed(mixed $value): bool
    {
        if (false === $this->isTestEnvironment()) {
            return false;
        }

        try {
            return strlen(json_encode($value, JSON_THROW_ON_ERROR)) <= self::MAX_VALUE_SIZE;
        } catch (JsonException) {
            return false;
        }
    }

    private function isTestEnvironment(): bool
    {
        if ($this->appState->getMode() === AppState::MODE_DEVELOPER) {
            return true;
        }

        try {
            return true === $this->deploymentConfig->get(self::ALLOW_PRODUCTION_CONFIG_PATH);
        } catch (Throwable) {
            return false;
        }
    }
}
