<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Test;

use Loki\Field\Component\Test\TestFieldStorage;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Session\Generic as Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TestFieldStorageTest extends TestCase
{
    #[DataProvider('getSaveValueScenarios')]
    public function testSaveValue(string $mode, mixed $allowProduction, mixed $value, bool $expectSaved): void
    {
        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', '__call'])
            ->getMock();
        $session->method('getData')->willReturn(['existing' => 'value']);
        $session->expects($expectSaved ? $this->once() : $this->never())
            ->method('__call')
            ->with('setData', ['loki_field_components_test', ['existing' => 'value', 'foo' => $value]]);

        $appState = $this->createMock(AppState::class);
        $appState->method('getMode')->willReturn($mode);

        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        if ($allowProduction instanceof RuntimeException) {
            $deploymentConfig->method('get')->willThrowException($allowProduction);
        } else {
            $deploymentConfig->method('get')
                ->with('loki_functional_tests/allow_production')
                ->willReturn($allowProduction);
        }

        (new TestFieldStorage($session, $appState, $deploymentConfig))->saveValue('foo', $value);
    }

    public static function getSaveValueScenarios(): array
    {
        return [
            'developer mode' => [AppState::MODE_DEVELOPER, null, 'bar', true],
            'production mode with allow_production' => [AppState::MODE_PRODUCTION, true, 'bar', true],
            'production mode without allow_production' => [AppState::MODE_PRODUCTION, null, 'bar', false],
            'production mode with truthy allow_production' => [AppState::MODE_PRODUCTION, '1', 'bar', false],
            'production mode with failing deployment config' => [
                AppState::MODE_PRODUCTION,
                new RuntimeException('failure'),
                'bar',
                false,
            ],
            'invalid UTF-8 value' => [AppState::MODE_DEVELOPER, null, "\xB1\x31", false],
            'oversized value' => [AppState::MODE_DEVELOPER, true, str_repeat('a', 5000), false],
        ];
    }
}
