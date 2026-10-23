<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Validator;

use Loki\Components\Messages\LocalMessage;
use Loki\Components\Validator\Validator;
use Loki\Field\Test\Integration\Traits\TestPage;
use Loki\Field\Validator\PasswordComplexityValidator;
use Loki\Field\ViewModel\PasswordComplexity;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class PasswordComplexityValidatorTest extends TestCase
{
    use TestPage;

    public function testPasswordFieldUsesPasswordComplexityValidator(): void
    {
        $component = $this->getTestComponent('password');
        $this->assertContains('password_complexity', $component->getValidators());
    }

    public function testWeakPasswordIsRejectedWithMessages(): void
    {
        $component = $this->getTestComponent('password');
        $validator = ObjectManager::getInstance()->get(Validator::class);

        $this->assertFalse($validator->validate($component, 'password'));

        $messages = array_map(
            fn (LocalMessage $message) => $message->getText(),
            $component->getLocalMessageRegistry()->getMessages()
        );
        $this->assertNotEmpty($messages);
        $this->assertStringContainsString('at least 2 numbers', implode(' ', $messages));
    }

    public function testStrongPasswordIsAccepted(): void
    {
        $component = $this->getTestComponent('password');
        $validator = ObjectManager::getInstance()->get(Validator::class);

        $this->assertTrue($validator->validate($component, 'P@ssw0rd!42'));
        $this->assertEmpty($component->getLocalMessageRegistry()->getMessages());
    }

    public function testPasswordComplexityCanBeConfiguredViaDi(): void
    {
        $objectManager = ObjectManager::getInstance();
        $validator = $objectManager->create(PasswordComplexityValidator::class, [
            'passwordComplexity' => $objectManager->create(PasswordComplexity::class, [
                'minimum' => 4,
                'minimumNumeric' => 0,
                'minimumOther' => 0,
            ]),
        ]);

        $this->assertTrue($validator->validate('abcd'));
        $this->assertIsArray($validator->validate('abc'));
    }
}
