<?php declare(strict_types=1);

namespace Loki\Field\Component\Test;

use Loki\Field\Component\Base\Field\FieldRepository;

/**
 * Repository for the field components on the test page (layout handle `loki_field_components_index_test`)
 */
class TestFieldRepository extends FieldRepository
{
    public function __construct(
        private readonly TestFieldStorage $storage
    ) {
    }

    public function getValue(): mixed
    {
        return $this->storage->getValue($this->getComponentName());
    }

    public function saveValue(mixed $value): void
    {
        $this->storage->saveValue($this->getComponentName(), $value);
    }
}
