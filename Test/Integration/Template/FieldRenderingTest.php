<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Template;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Field\Test\Integration\Traits\TestPage;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionModelInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class FieldRenderingTest extends TestCase
{
    use TestPage;

    protected function setUp(): void
    {
        $this->getTestStorage()->reset();
    }

    public function testInputField(): void
    {
        $this->getTestStorage()->saveValue('loki-field-components.test.input', 'Hello "world"');
        $html = $this->renderTestBlock('input');

        $this->assertStringContainsString('field-type-input', $html);
        $this->assertStringContainsString('optional', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+type="text"/', $html);
        $this->assertStringContainsString('name="input"', $html);
        $this->assertStringContainsString('id="loki-field-components-test-input-field"', $html);
        $this->assertStringContainsString('aria-label="Test&#x20;input"', $html);
        $this->assertStringContainsString('aria-errormessage="loki-field-components-test-input-messages"', $html);
        $this->assertStringContainsString('placeholder="Type&#x20;something"', $html);
        $this->assertStringContainsString('value="Hello&#x20;&quot;world&quot;"', $html);
        $this->assertStringContainsString('for="loki-field-components-test-input-field"', $html);
        $this->assertStringContainsString('Some help text', $html);
        $this->assertDoesNotMatchRegularExpression('/<input[^>]+ required[ >]/', $html);
    }

    public function testComponentAttributesAreAdded(): void
    {
        $html = $this->renderTestBlock('input');

        $this->assertStringContainsString('id="loki-field-components-test-input"', $html);
        $this->assertStringContainsString('x-data="LokiFieldComponent"', $html);
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 1, 'store', 'default')]
    public function testRequiredFieldWithRequiredBlock(): void
    {
        $html = $this->renderTestBlock('required');

        $this->assertStringContainsString('required&#x20;required&#x20;field-type-input', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+ required[ >]/', $html);
        $this->assertMatchesRegularExpression('/<label[^>]*>\s*Required input\s*<sub[^>]*>\*<\/sub>/', $html);
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 0, 'store', 'default')]
    public function testRequiredFieldWithoutRequiredBlock(): void
    {
        $html = $this->renderTestBlock('required');

        $this->assertMatchesRegularExpression('/<input[^>]+ required[ >]/', $html);
        $this->assertStringNotContainsString('<sub', $html);
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 1, 'store', 'default')]
    public function testOptionalFieldHasNoRequiredBlock(): void
    {
        $this->assertStringNotContainsString('<sub', $this->renderTestBlock('input'));
    }

    public function testTextareaField(): void
    {
        $this->getTestStorage()->saveValue('loki-field-components.test.textarea', 'Say "hi" & <b>bye</b>');
        $html = $this->renderTestBlock('textarea');

        $this->assertStringContainsString('field-type-textarea', $html);
        $this->assertMatchesRegularExpression('/<textarea[^>]*>Say &quot;hi&quot; &amp; bye<\/textarea>/', $html);
        $this->assertMatchesRegularExpression('/<textarea[^>]+id="loki-field-components-test-textarea-field"/', $html);
        $this->assertMatchesRegularExpression('/<textarea[^>]+name="textarea"/', $html);
        $this->assertStringContainsString('for="loki-field-components-test-textarea-field"', $html);
    }

    public function testTextareaIsNotTheFirstElementOfTheFieldContainer(): void
    {
        $html = $this->renderTestBlock('textarea');

        $this->assertMatchesRegularExpression(
            '/<div[^>]*>\s*<textarea[^>]+id="loki-field-components-test-textarea-field"/',
            $html,
            'Attributes that are added to the first element of the field container must not replace the textarea ID'
        );
    }

    public function testPasswordField(): void
    {
        $html = $this->renderTestBlock('password');

        $this->assertStringContainsString('field-type-password', $html);
        $this->assertStringContainsString('x-data="LokiPassword"', $html);
        $this->assertStringContainsString(':type="inputType"', $html);
    }

    public function testStoredPasswordIsNotRendered(): void
    {
        foreach (['password', 'password_repeat'] as $fieldName) {
            $this->getTestStorage()->saveValue(self::TEST_PREFIX . $fieldName, 'S3cr3t!!pass');
            $html = $this->renderTestBlock($fieldName);

            $this->assertStringNotContainsString('S3cr3t', $html, 'Password is rendered in field "' . $fieldName . '"');
        }
    }

    public function testStoredValueOfPasswordInputIsNotRendered(): void
    {
        $this->getTestStorage()->saveValue(self::TEST_PREFIX . 'input', 'S3cr3t!!pass');
        $block = $this->getTestBlock('input');
        $block->setData('input_type', 'password');
        $html = (string)$block->toHtml();

        $this->assertMatchesRegularExpression('/<input[^>]+type="password"/', $html);
        $this->assertStringNotContainsString('S3cr3t', $html);
        $this->assertDoesNotMatchRegularExpression('/<input[^>]+ value="/', $html);
    }

    public function testPasswordRepeatField(): void
    {
        $html = $this->renderTestBlock('password_repeat');

        $this->assertStringContainsString('field-type-password_repeat', $html);
        $this->assertStringContainsString('x-data="LokiPasswordRepeat"', $html);
        $this->assertMatchesRegularExpression('/aria-label="Repeat(&#x20;| )password"/', $html);
    }

    public function testSelectFieldPreselectsFirstEnabledOption(): void
    {
        $html = $this->renderTestBlock('select');

        $this->assertStringContainsString('field-type-select', $html);
        $this->assertMatchesRegularExpression('/<select[^>]+name="select"/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="red"\s+selected\s*>\s*Red/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="black"\s+disabled\s*>\s*Black/', $html);
        $this->assertSame('red', $this->getTestStorage()->getValue('loki-field-components.test.select'));
    }

    public function testSelectFieldUsesStoredValue(): void
    {
        $this->getTestStorage()->saveValue('loki-field-components.test.select', 'blue');
        $html = $this->renderTestBlock('select');

        $this->assertMatchesRegularExpression('/<option\s+value="blue"\s+selected\s*>/', $html);
        $this->assertDoesNotMatchRegularExpression('/<option\s+value="red"\s+selected\s*>/', $html);
    }

    public function testMultipleSelectField(): void
    {
        $this->getTestStorage()->saveValue('loki-field-components.test.select_multiple', ['green', 'blue']);
        $html = $this->renderTestBlock('select_multiple');

        $this->assertMatchesRegularExpression('/<select[^>]+ multiple[ >]/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="green"\s+selected\s*>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="blue"\s+selected\s*>/', $html);
        $this->assertDoesNotMatchRegularExpression('/<option\s+value="red"\s+selected\s*>/', $html);
    }

    public function testCheckboxFieldHasNoSeparateLabel(): void
    {
        $html = $this->renderTestBlock('checkbox');

        $this->assertStringContainsString('field-type-checkbox', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+type="checkbox"/', $html);
        $this->assertStringContainsString('Subscribe to test', $html);
        $this->assertStringNotContainsString(
            'for="loki-field-components-test-checkbox-field"',
            $html,
            'Field label is not shown for checkboxes'
        );
        $this->assertStringContainsString('aria-label="Test&#x20;checkbox"', $html);
        $this->assertDoesNotMatchRegularExpression('/<input[^>]+ checked[ >]/', $html);
    }

    public function testCheckboxLabelDoesNotAllowSpanTags(): void
    {
        $block = $this->getTestBlock('checkbox');
        $block->setData('input_label', '<span style="position:fixed">Overlay</span> <b>Bold</b>');
        $html = (string)$block->toHtml();

        $this->assertStringNotContainsString('position:fixed', $html);
        $this->assertStringNotContainsString('<span style', $html);
        $this->assertStringContainsString('<b>Bold</b>', $html);
    }

    public function testInputPrefixDoesNotAllowSpanTags(): void
    {
        $block = $this->getTestBlock('input');
        $block->setData('input_prefix', '<span style="position:fixed">Overlay</span> <b>+31</b>');
        $html = (string)$block->toHtml();

        $this->assertStringNotContainsString('position:fixed', $html);
        $this->assertStringNotContainsString('<span style', $html);
        $this->assertStringContainsString('<b>+31</b>', $html);
    }

    public function testCheckedCheckbox(): void
    {
        $this->getTestStorage()->saveValue('loki-field-components.test.checkbox', true);
        $this->assertMatchesRegularExpression('/<input[^>]+ checked[ >]/', $this->renderTestBlock('checkbox'));
    }

    public function testSwitchField(): void
    {
        $html = $this->renderTestBlock('switch');

        $this->assertStringContainsString('field-type-switch', $html);
        $this->assertStringContainsString('role="switch"', $html);
        $this->assertStringContainsString('Enable test', $html);
        $this->assertStringContainsString('Test switch', $html);
    }

    public function testRadioFieldUsesNoLabelTag(): void
    {
        $html = $this->renderTestBlock('radio');

        $this->assertStringContainsString('field-type-radio', $html);
        $this->assertStringContainsString('role="radiogroup"', $html);
        $this->assertStringNotContainsString('for="loki-field-components-test-radio-field"', $html);
        $this->assertMatchesRegularExpression('/<span[^>]*>\s*Test radio/', $html);
        $this->assertStringContainsString('id="loki-field-components-test-radio-field-red"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+value="red"[^>]* checked[ >]/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+ disabled[^>]+value="black"/', $html);
    }

    public function testButtonGroupField(): void
    {
        $html = $this->renderTestBlock('button_group');

        $this->assertStringContainsString('field-type-button_group', $html);
        $this->assertStringNotContainsString('for="loki-field-components-test-button-group-field"', $html);
        $this->assertMatchesRegularExpression('/<span[^>]*>\s*Test button group/', $html);
        $this->assertMatchesRegularExpression(
            '/<div\s+id="loki-field-components-test-button-group-field"[^>]+role="radiogroup"'
            . '[^>]+aria-label="Test&#x20;button&#x20;group"/',
            $html
        );
        $this->assertStringContainsString('@keydown="onButtonGroupKeydown"', $html);
        $this->assertSame(4, preg_match_all('/<button[^>]+type="button"[^>]+role="radio"/', $html));
        $this->assertMatchesRegularExpression('/id="loki-field-components-test-button-group-field-red-[0-9a-f]{8}"/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+value="red"[^>]+aria-checked="true"/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+value="green"[^>]+aria-checked="false"/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+ disabled [^>]*value="black"/', $html);
        $this->assertStringContainsString(':aria-checked="isChecked"', $html);
        $this->assertStringContainsString(':tabindex="getButtonTabIndex"', $html);
        $this->assertStringContainsString('@click="submitButtonValue"', $html);
        $this->assertDoesNotMatchRegularExpression('/<button[^>]+aria-label=/', $html);
        $this->assertDoesNotMatchRegularExpression('/<button[^>]+ required[ >]/', $html);
        $this->assertSame('red', $this->getTestStorage()->getValue('loki-field-components.test.button_group'));
    }

    public function testButtonGroupFieldWithOptionGroup(): void
    {
        $block = $this->getTestBlock('button_group');
        $block->setData('option_model', $this->getOptionModel([
            new Option('red', 'Red'),
            new OptionGroup('Dark colors', [new Option('black', 'Black')], true),
        ]));
        $html = (string)$block->toHtml();

        $this->assertMatchesRegularExpression('/<fieldset[^>]+disabled\s*>\s*<legend[^>]*>\s*Dark colors/', $html);
        $this->assertMatchesRegularExpression('/<button[^>]+value="black"/', $html);
    }

    public function testButtonGroupOptionIdsAreUnique(): void
    {
        $block = $this->getTestBlock('button_group');
        $block->setData('option_model', $this->getOptionModel([
            new Option('Red', 'Red'),
            new Option('red', 'red'),
            new Option('a b', 'A B'),
            new Option('a-b', 'A-B'),
        ]));
        $html = (string)$block->toHtml();

        preg_match_all('/<button[^>]+id="([^"]+)"/', $html, $matches);
        $this->assertCount(4, $matches[1]);
        $this->assertCount(4, array_unique($matches[1]));
    }

    public function testButtonGroupFieldEscapesOptions(): void
    {
        $maliciousValue = '"><img src=x onerror=alert(1)>\'';
        $maliciousLabel = '<script>alert("label")</script>';
        $block = $this->getTestBlock('button_group');
        $block->setData('option_model', $this->getOptionModel([
            new Option($maliciousValue, $maliciousLabel),
        ]));
        $this->getTestStorage()->saveValue('loki-field-components.test.button_group', $maliciousValue);
        $html = (string)$block->toHtml();

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<script>alert', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;label&quot;)&lt;/script&gt;', $html);
        $this->assertStringContainsString(
            'value="&quot;&gt;&lt;img&#x20;src&#x3D;x&#x20;onerror&#x3D;alert&#x28;1&#x29;&gt;&#x27;"',
            $html
        );
        $this->assertMatchesRegularExpression('/id="loki-field-components-test-button-group-field-[a-z0-9_-]+"/', $html);
        $this->assertDoesNotMatchRegularExpression('/<button[^>]+aria-label=/', $html);
        $this->assertDoesNotMatchRegularExpression('/(@click|:aria-checked|:tabindex)="[^"]*alert/', $html);
    }

    public function testComboboxField(): void
    {
        $html = $this->renderTestBlock('combobox');

        $this->assertStringContainsString('field-type-combobox', $html);
        $this->assertStringContainsString('x-data="LokiCombobox"', $html);
        $this->assertStringContainsString('role="combobox"', $html);
        $this->assertStringContainsString('aria-controls="loki-field-components-test-combobox-field-listbox"', $html);
        $this->assertMatchesRegularExpression('/<option\s+value=""\s*>\s*Choose a color/', $html);
        $this->assertNull(
            $this->getTestStorage()->getValue('loki-field-components.test.combobox'),
            'An empty first option is not saved as default value'
        );
    }

    public function testDisabledField(): void
    {
        $html = $this->renderTestBlock('disabled');

        $this->assertMatchesRegularExpression('/<input[^>]+ disabled[ >]/', $html);
        $this->assertStringContainsString('autocomplete="off"', $html);
    }

    public function testMaskedField(): void
    {
        $this->assertStringContainsString('x-mask="9999&#x20;aa"', $this->renderTestBlock('masked'));
    }

    private function getOptionModel(array $options): OptionModelInterface
    {
        return new class($options) implements OptionModelInterface {
            public function __construct(private readonly array $options)
            {
            }

            public function getOptions(ComponentViewModelInterface $viewModel): array
            {
                return $this->options;
            }
        };
    }
}
