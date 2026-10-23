<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Template;

use Loki\Field\Test\Integration\Traits\TestPage;
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
        $this->assertStringContainsString('aria-label="Test input"', $html);
        $this->assertStringContainsString('aria-errormessage="loki-field-components-test-input-messages"', $html);
        $this->assertStringContainsString('placeholder="Type something"', $html);
        $this->assertStringContainsString('value="Hello &quot;world&quot;"', $html);
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

        $this->assertStringContainsString('required required field-type-input', $html);
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
    }

    public function testPasswordField(): void
    {
        $html = $this->renderTestBlock('password');

        $this->assertStringContainsString('field-type-password', $html);
        $this->assertStringContainsString('x-data="LokiPassword"', $html);
        $this->assertStringContainsString(':type="inputType"', $html);
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
        $this->assertStringContainsString('aria-label="Test checkbox"', $html);
        $this->assertDoesNotMatchRegularExpression('/<input[^>]+ checked[ >]/', $html);
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
        $this->assertStringContainsString('x-mask="9999 aa"', $this->renderTestBlock('masked'));
    }
}
