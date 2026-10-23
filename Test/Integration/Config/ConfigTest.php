<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Config;

use Loki\Field\Config\Config;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testUseRequiredBlockIsEnabledByDefault(): void
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertTrue($config->useRequiredBlock());
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 0, 'store', 'default')]
    public function testUseRequiredBlockDisabled(): void
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertFalse($config->useRequiredBlock());
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 1, 'store', 'default')]
    public function testUseRequiredBlockEnabled(): void
    {
        $config = ObjectManager::getInstance()->get(Config::class);
        $this->assertTrue($config->useRequiredBlock());
    }
}
