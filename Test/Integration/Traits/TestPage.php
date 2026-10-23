<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Traits;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRegistry;
use Loki\Components\Util\Controller\LayoutLoader;
use Loki\Field\Component\Test\TestFieldStorage;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;

/**
 * Helpers for the test components of the layout handle `loki_field_components_index_test`
 */
trait TestPage
{
    private const TEST_HANDLE = 'loki_field_components_index_test';
    private const TEST_PREFIX = 'loki-field-components.test.';

    private ?LayoutInterface $testLayout = null;

    private function loadTestLayout(array $handles = [self::TEST_HANDLE], string $theme = 'Magento/luma'): LayoutInterface
    {
        $objectManager = ObjectManager::getInstance();
        $objectManager->get(DesignInterface::class)->setDesignTheme($theme, 'frontend');
        $this->testLayout = $objectManager->get(LayoutLoader::class)->load($handles, ['1column']);

        return $this->testLayout;
    }

    private function getTestBlock(string $fieldName): AbstractBlock
    {
        if (false === $this->testLayout instanceof LayoutInterface) {
            $this->loadTestLayout();
        }

        $block = $this->testLayout->getBlock(self::TEST_PREFIX . $fieldName);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'No test block "' . $fieldName . '" found');

        return $block;
    }

    private function getTestComponent(string $fieldName): ComponentInterface
    {
        $block = $this->getTestBlock($fieldName);
        $component = ObjectManager::getInstance()->get(ComponentRegistry::class)->getComponentFromBlock($block);
        $component->getLocalMessageRegistry()->clearMessages();

        return $component;
    }

    private function renderTestBlock(string $fieldName): string
    {
        return (string)$this->getTestBlock($fieldName)->toHtml();
    }

    private function getTestStorage(): TestFieldStorage
    {
        return ObjectManager::getInstance()->get(TestFieldStorage::class);
    }
}
