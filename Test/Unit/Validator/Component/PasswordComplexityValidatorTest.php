<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Test\Unit\Validator\Component;

use PHPUnit\Framework\TestCase;
use Yireo\LokiFieldComponents\Validator\PasswordComplexityValidator;
use Yireo\LokiFieldComponents\ViewModel\PasswordComplexity;

class PasswordComplexityValidatorTest extends TestCase
{
    /**
     * @return void
     * @dataProvider getTestValues
     */
    public function testValidate(string $password, true|string $expectedResult)
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

    public function getTestValues(): array
    {
        return [
            ['foo', 'characters'],
            ['P@rFa^tl2243yF!!ne', true],
            ['password', 'numbers'],
            ['password42', 'non-alphanumeric'],
        ];
    }
}
