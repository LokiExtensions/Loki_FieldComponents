<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Loki\Field\Validator\PasswordComplexityValidator;
use Loki\Field\ViewModel\PasswordComplexity;

class PasswordComplexityValidatorTest extends TestCase
{
    #[DataProvider('getTestValues')]
    public function testValidate(mixed $password, bool|string $expectedResult): void
    {
        $passwordComplexity = new PasswordComplexity();
        $validator = new PasswordComplexityValidator($passwordComplexity);
        $result = $validator->validate($password);

        if ($expectedResult === true) {
            $this->assertTrue($result, var_export($result, true));
        }

        if (is_string($expectedResult)) {
            $this->assertIsArray($result);
            $found = false;
            foreach ($result as $r) {
                if (str_contains($r, $expectedResult)) {
                    $found = true;
                }
            }

            $this->assertTrue($found, 'Message with "'.$expectedResult.'" not found: '.var_export($result, true));
        }
    }

    public static function getTestValues(): array
    {
        return [
            ['foo', 'characters'],
            ['P@rFa^tl2243yF!!ne', true],
            ['password', 'numbers'],
            ['password42', 'non-alphanumeric'],
            ['PAssword42', 'non-alphanumeric'],
            [12345678, 'non-alphanumeric'],
        ];
    }

    #[DataProvider('getSkippedValues')]
    public function testEmptyOrNonScalarValuesAreSkipped(mixed $value): void
    {
        $validator = new PasswordComplexityValidator(new PasswordComplexity());
        $this->assertTrue($validator->validate($value));
    }

    public static function getSkippedValues(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'array' => [['foo']],
            'object' => [new \stdClass()],
        ];
    }

    public function testAllErrorsAreReturnedAtOnce(): void
    {
        $validator = new PasswordComplexityValidator(new PasswordComplexity());
        $result = $validator->validate('abc');

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertStringContainsString('at least 8 characters', $result[0]);
        $this->assertStringContainsString('at least 2 numbers', $result[1]);
        $this->assertStringContainsString('at least 2 non-alphanumeric', $result[2]);
    }

    public function testCustomComplexity(): void
    {
        $validator = new PasswordComplexityValidator(new PasswordComplexity(4, 0, 0));
        $this->assertTrue($validator->validate('abcd'));

        $result = $validator->validate('abc');
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertStringContainsString('at least 4 characters', $result[0]);
    }

    public function testLengthIsCountedInCharactersInsteadOfBytes(): void
    {
        $validator = new PasswordComplexityValidator(new PasswordComplexity(4, 0, 0));
        $this->assertTrue($validator->validate('ééé1'));

        $result = $validator->validate('éé1');
        $this->assertIsArray($result, 'Multibyte characters are not counted double');
    }

    public function testMultibyteCharactersCountAsNonAlphanumeric(): void
    {
        $validator = new PasswordComplexityValidator(new PasswordComplexity(1, 0, 2));
        $this->assertTrue($validator->validate('aé€'));
    }

    #[DataProvider('getCharacterClassValues')]
    public function testRequiredCharacterClasses(int $requiredClasses, string $password, bool $expectValid): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnMap([
            ['customer/password/minimum_password_length', 'store', null, '1'],
            ['customer/password/required_character_classes_number', 'store', null, (string)$requiredClasses],
        ]);

        $validator = new PasswordComplexityValidator(new PasswordComplexity(1, 0, 0, $scopeConfig));
        $result = $validator->validate($password);

        if ($expectValid) {
            $this->assertTrue($result, var_export($result, true));
            return;
        }

        $this->assertIsArray($result);
        $this->assertContains(
            'Your password needs to contain at least ' . $requiredClasses
            . ' different classes of characters: lower case, upper case, digits, special characters.',
            $result
        );
    }

    public static function getCharacterClassValues(): array
    {
        return [
            'no classes required' => [0, 'abcdef', true],
            'one class of two' => [2, 'abcdef', false],
            'lower and digits' => [2, 'abc123', true],
            'lower and digits of three' => [3, 'abc123', false],
            'lower, upper and digits' => [3, 'abCD12', true],
            'lower, digits and special' => [3, 'ab12!@', true],
            'multibyte counts as special' => [3, 'ab12é', true],
            'three of four' => [4, 'abCD12', false],
            'all four classes' => [4, 'aB1!', true],
        ];
    }
}
