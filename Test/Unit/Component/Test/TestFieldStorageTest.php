<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Test;

use Loki\Field\Component\Test\TestFieldStorage;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Session\Generic as Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TestFieldStorageTest extends TestCase
{
    #[DataProvider('getSaveValueScenarios')]
    public function testSaveValue(string $mode, bool $functionalTestsEnabled, mixed $value, bool $expectSaved): void
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

        $moduleManager = $this->createMock(ModuleManager::class);
        $moduleManager->method('isEnabled')->with('Loki_FunctionalTests')->willReturn($functionalTestsEnabled);

        (new TestFieldStorage($session, $appState, $moduleManager))->saveValue('foo', $value);
    }

    public static function getSaveValueScenarios(): array
    {
        return [
            'developer mode' => [AppState::MODE_DEVELOPER, false, 'bar', true],
            'production mode with functional tests' => [AppState::MODE_PRODUCTION, true, 'bar', true],
            'production mode without functional tests' => [AppState::MODE_PRODUCTION, false, 'bar', false],
            'oversized value' => [AppState::MODE_DEVELOPER, true, str_repeat('a', 5000), false],
        ];
    }
}
