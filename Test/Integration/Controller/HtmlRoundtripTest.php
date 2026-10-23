<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration\Controller;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Loki\Components\Controller\Index\Html;
use Loki\Field\Component\Test\TestFieldStorage;
use Loki\Components\Util\Security\AjaxSignature;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Controller\ResultInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
#[AppIsolation(true)]
class HtmlRoundtripTest extends TestCase
{
    private const HANDLE = 'loki_field_components_index_test';
    private const PREFIX = 'loki-field-components.test.';

    protected function setUp(): void
    {
        $this->getStorage()->reset();
    }

    public function testValidValueIsSavedAndFieldIsRendered(): void
    {
        $response = $this->post('input', 'Hello world');

        $this->assertSame('Hello world', $this->getStorage()->getValue(self::PREFIX . 'input'));
        $this->assertStringContainsString('id="loki-field-components-test-input"', (string)$response->getBody());
        $this->assertStringContainsString('value="Hello world"', (string)$response->getBody());
    }

    public function testEmptyRequiredValueIsNotSaved(): void
    {
        $response = $this->post('required', '');

        $this->assertFalse($this->getStorage()->hasValue(self::PREFIX . 'required'));
        $this->assertStringContainsString('aria-invalid', (string)$response->getBody());
    }

    public function testDisabledFieldIsNotSaved(): void
    {
        $this->post('disabled', 'foo');
        $this->assertFalse($this->getStorage()->hasValue(self::PREFIX . 'disabled'));
    }

    public function testWeakPasswordIsNotSaved(): void
    {
        $response = $this->post('password', 'password');

        $this->assertFalse($this->getStorage()->hasValue(self::PREFIX . 'password'));
        $this->assertStringContainsString('at least 2 numbers', (string)$response->getBody());
    }

    public function testStrongPasswordIsSaved(): void
    {
        $this->post('password', 'P@ssw0rd!42');
        $this->assertSame('P@ssw0rd!42', $this->getStorage()->getValue(self::PREFIX . 'password'));
    }

    public function testCheckboxValueIsSaved(): void
    {
        $this->post('checkbox', true);
        $this->assertTrue($this->getStorage()->getValue(self::PREFIX . 'checkbox'));
    }

    public function testMultipleSelectValuesAreSaved(): void
    {
        $this->post('select_multiple', ['green', 'blue']);
        $this->assertSame(['green', 'blue'], $this->getStorage()->getValue(self::PREFIX . 'select_multiple'));
    }

    private function post(string $fieldName, mixed $value): HttpResponse
    {
        $blockName = self::PREFIX . $fieldName;
        $payload = [
            'targets' => [$blockName],
            'handles' => [self::HANDLE],
            'pageHandles' => ['1column'],
            'updates' => [['blockName' => $blockName, 'update' => $value]],
            'request' => [],
        ];

        $payload['signature'] = ObjectManager::getInstance()->get(AjaxSignature::class)->sign([
            'handles' => $payload['handles'],
            'pageHandles' => $payload['pageHandles'],
            'request' => $payload['request'],
        ]);

        $headers = new Headers();
        $headers->addHeader(GenericHeader::fromString('X-Alpine-Request: 1'));

        $request = ObjectManager::getInstance()->get(HttpRequest::class);
        $request->setHeaders($headers);
        $request->setContent((string)json_encode($payload));

        $result = ObjectManager::getInstance()->create(Html::class)->execute();
        $this->assertInstanceOf(ResultInterface::class, $result);

        $response = ObjectManager::getInstance()->create(HttpResponse::class);
        $result->renderResult($response);

        return $response;
    }

    private function getStorage(): TestFieldStorage
    {
        return ObjectManager::getInstance()->get(TestFieldStorage::class);
    }
}
