<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Config;

use Loki\Field\Config\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    #[DataProvider('getUseRequiredBlockValues')]
    public function testUseRequiredBlock(mixed $value, bool $expected): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('loki_field_components/fields/use_required_block', ScopeInterface::SCOPE_STORE)
            ->willReturn($value);

        $this->assertSame($expected, (new Config($scopeConfig))->useRequiredBlock());
    }

    public static function getUseRequiredBlockValues(): array
    {
        return [
            'enabled' => ['1', true],
            'disabled' => ['0', false],
            'not set' => [null, false],
        ];
    }
}
