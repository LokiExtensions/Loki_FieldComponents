<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Layout;

use Loki\Field\Test\Integration\Traits\TestPage;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class LokiFieldComponentsHandleTest extends TestCase
{
    use TestPage;

    private const SCRIPT_BLOCKS = [
        'loki-field-components.script.validator' => 'loki.script',
        'loki-field-components.script.validators.password_complexity' => 'loki.script',
        'loki-field-components.script.component-type.field' => 'loki.script.component-type',
        'loki-field-components.script.component-type.combobox' => 'loki.script.component-type',
        'loki-field-components.script.components.field-component' => 'loki.script.component',
        'loki-field-components.script.components.password' => 'loki.script.component',
        'loki-field-components.script.components.password-repeat' => 'loki.script.component',
        'loki-field-components.script.components.combobox' => 'loki.script.component',
    ];

    private const UTILITY_BLOCKS = [
        'loki-field-components.utils.loader',
        'loki-field-components.utils.loader-small',
        'loki-field-components.utils.loader-overlay',
        'loki-field-components.utils.overlay',
        'loki-field-components.utils.required',
    ];

    public function testScriptBlocksAreAddedToLokiScriptContainers(): void
    {
        $layout = $this->loadTestLayout(['loki_field_components']);

        foreach (self::SCRIPT_BLOCKS as $blockName => $parentName) {
            $this->assertInstanceOf(AbstractBlock::class, $layout->getBlock($blockName), 'Block "' . $blockName . '"');
            $this->assertSame($parentName, $layout->getParentName($blockName), 'Parent of "' . $blockName . '"');
        }
    }

    public function testUtilityBlocksAreAdded(): void
    {
        $layout = $this->loadTestLayout(['loki_field_components']);

        foreach (self::UTILITY_BLOCKS as $blockName) {
            $this->assertInstanceOf(AbstractBlock::class, $layout->getBlock($blockName), 'Block "' . $blockName . '"');
        }

        $this->assertSame(20, (int)$layout->getBlock('loki-field-components.utils.loader-small')->getSize());
    }

    public function testValidatorScriptIsRendered(): void
    {
        $layout = $this->loadTestLayout(['loki_field_components']);

        $html = (string)$layout->getBlock('loki-field-components.script.validator')->toHtml();
        $this->assertStringContainsString('LokiComponentValidator', $html);
        $this->assertStringContainsString('required: (component) =>', $html);

        $html = (string)$layout->getBlock('loki-field-components.script.validators.password_complexity')->toHtml();
        $this->assertStringContainsString('LokiComponentValidator.validators.password_complexity', $html);
        $this->assertStringContainsString('< 8', $html);
    }

    public function testDefaultFieldCssClasses(): void
    {
        $layout = $this->loadTestLayout(['loki_field_components']);
        $block = $layout->getBlock('loki-field-components.defaults.form.field');

        $this->assertInstanceOf(AbstractBlock::class, $block);
        $this->assertSame('col-span-12 md:col-span-6', $block->getCssClasses()['block']['grid']);
    }

    public function testHyvaHandleAddsAlpineMaskPlugin(): void
    {
        ObjectManager::getInstance()->get(DesignInterface::class)->setDesignTheme('Magento/luma', 'frontend');
        $update = ObjectManager::getInstance()->create(LayoutInterface::class)->getUpdate();
        $update->load(['hyva_loki_field_components']);

        $xml = $update->asString();
        $this->assertMatchesRegularExpression(
            '/<referenceBlock name="script-alpine-js">\s*<block name="loki-components.alpine-plugin-mask"'
            . ' template="Loki_FieldComponents::script\/alpine-mask.phtml"\/>/',
            $xml
        );
    }

    public function testLumaAddsAlpineMaskPlugin(): void
    {
        $layout = $this->loadTestLayout(['loki_field_components']);

        $html = (string)$layout->getBlock('loki.alpine-plugin-mask')?->toHtml();
        $this->assertStringContainsString('alpine-mask', $html);
    }
}
