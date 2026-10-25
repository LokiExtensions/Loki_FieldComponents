<?php declare(strict_types=1);

namespace Loki\Field\Component\Test;

use Magento\Framework\Session\Generic as Session;

/**
 * Session storage for the field components on the test page (layout handle `loki_field_components_index_test`)
 */
class TestFieldStorage
{
    private const SESSION_KEY = 'loki_field_components_test';

    public function __construct(
        private readonly Session $session
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
}
