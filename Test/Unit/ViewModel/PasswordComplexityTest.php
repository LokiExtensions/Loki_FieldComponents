<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\ViewModel;

use Loki\Field\ViewModel\PasswordComplexity;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;

class PasswordComplexityTest extends TestCase
{
    public function testDefaults(): void
    {
        $passwordComplexity = new PasswordComplexity();

        $this->assertSame(8, $passwordComplexity->getMinimum());
        $this->assertSame(2, $passwordComplexity->getMinimumNumeric());
        $this->assertSame(2, $passwordComplexity->getMinimumOther());
    }

    public function testCustomValues(): void
    {
        $passwordComplexity = new PasswordComplexity(12, 3, 1);

        $this->assertSame(12, $passwordComplexity->getMinimum());
        $this->assertSame(3, $passwordComplexity->getMinimumNumeric());
        $this->assertSame(1, $passwordComplexity->getMinimumOther());
    }

    public function testMinimumFromCustomerConfig(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn('10');

        $this->assertSame(10, (new PasswordComplexity(8, 2, 2, $scopeConfig))->getMinimum());
    }

    public function testCustomerConfigDoesNotLowerTheMinimum(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn('6');

        $this->assertSame(8, (new PasswordComplexity(8, 2, 2, $scopeConfig))->getMinimum());
    }
}
