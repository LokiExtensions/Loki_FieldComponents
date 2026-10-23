<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Plugin;

use Loki\Components\Validator\Validator;
use Loki\Field\Test\Integration\Traits\TestPage;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class ValidatorPluginTest extends TestCase
{
    use TestPage;

    public function testDisabledFieldIsNeverValid(): void
    {
        $component = $this->getTestComponent('disabled');
        $this->assertFalse($this->getValidator()->validate($component, 'foo'));
    }

    public function testEnabledFieldIsValidated(): void
    {
        $component = $this->getTestComponent('input');
        $this->assertTrue($this->getValidator()->validate($component, 'foo'));
    }

    public function testEnabledRequiredFieldIsPassedToValidators(): void
    {
        // The required validator itself only applies to AJAX requests, see HtmlRoundtripTest
        $component = $this->getTestComponent('required');

        $this->assertContains('required', $component->getValidators());
        $this->assertTrue($this->getValidator()->validate($component, 'foo'));
    }

    private function getValidator(): Validator
    {
        return ObjectManager::getInstance()->get(Validator::class);
    }
}
