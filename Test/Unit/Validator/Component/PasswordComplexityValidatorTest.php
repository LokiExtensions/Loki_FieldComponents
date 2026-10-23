<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator\Component;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
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
}
