<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testModuleIsRegistered(): void
    {
        $componentRegistrar = new ComponentRegistrar();
        $this->assertArrayHasKey('Loki_FieldComponents', $componentRegistrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testModuleAndDependenciesAreEnabled(): void
    {
        $moduleList = ObjectManager::getInstance()->get(ModuleList::class);
        foreach (['Loki_Base', 'Loki_Components', 'Loki_CssUtils', 'Loki_FieldComponents'] as $moduleName) {
            $this->assertTrue($moduleList->has($moduleName), 'Module "' . $moduleName . '" is not enabled');
        }
    }

    public function testDefaultUseRequiredBlock(): void
    {
        $scopeConfig = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        $this->assertSame('1', (string)$scopeConfig->getValue('loki_field_components/fields/use_required_block'));
    }
}
