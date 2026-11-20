<?php declare(strict_types=1);

namespace Loki\Field\Test\Integration;

use Loki\Components\Util\Block\ImageRenderer;
use Loki\Components\Validator\Validator;
use Loki\Components\Validator\ValidatorRegistry;
use Loki\Field\Field\FieldTypeManager;
use Loki\Field\Plugin\ValidatorPlugin;
use Loki\Field\Validator\PasswordComplexityValidator;
use Loki\Field\ViewModel\ImageOutput;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Framework\ObjectManager\Config\Reader\Dom as DiConfigReader;
use Magento\Framework\View\Element\Template\File\Resolver as TemplateFileResolver;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class DiConfigurationTest extends TestCase
{
    private const FIELD_TYPES = [
        'checkbox',
        'combobox',
        'input',
        'password',
        'password_repeat',
        'radio',
        'range',
        'select',
        'switch',
        'text',
        'textarea',
    ];

    public function testPasswordComplexityValidatorIsRegistered(): void
    {
        $validatorRegistry = ObjectManager::getInstance()->get(ValidatorRegistry::class);
        $validators = $validatorRegistry->getApplicableValidators(['password_complexity']);

        $this->assertArrayHasKey('password_complexity', $validators);
        $this->assertInstanceOf(PasswordComplexityValidator::class, $validators['password_complexity']);
    }

    public function testFieldTypeMapping(): void
    {
        $fieldTypeManager = ObjectManager::getInstance()->get(FieldTypeManager::class);
        $mapping = $fieldTypeManager->getFieldTemplateMapping();

        foreach (self::FIELD_TYPES as $fieldType) {
            $this->assertArrayHasKey($fieldType, $mapping, 'Field type "' . $fieldType . '" is not mapped');
        }

        $this->assertSame($mapping['input'], $mapping['text']);
    }

    public function testFieldTemplatesExist(): void
    {
        $objectManager = ObjectManager::getInstance();
        $fieldTypeManager = $objectManager->get(FieldTypeManager::class);
        $templateFileResolver = $objectManager->get(TemplateFileResolver::class);

        foreach ($fieldTypeManager->getFieldTemplateMapping() as $fieldType => $template) {
            $file = $templateFileResolver->getTemplateFileName($template, ['area' => 'frontend']);
            $this->assertNotEmpty($file, 'Template "' . $template . '" of field type "' . $fieldType . '" not found');
            $this->assertFileExists((string)$file);
        }
    }

    public function testValidatorPluginIsRegistered(): void
    {
        $pluginList = ObjectManager::getInstance()->get(PluginList::class);
        $plugins = $pluginList->get(Validator::class, []);

        $this->assertArrayHasKey('Loki_FieldComponents::validator', $plugins);
        $this->assertSame(ValidatorPlugin::class, $plugins['Loki_FieldComponents::validator']['instance']);
    }

    #[DataProvider('getValidatorPluginScopes')]
    public function testValidatorPluginScope(string $scope, bool $expectRegistered): void
    {
        $diConfig = ObjectManager::getInstance()->get(DiConfigReader::class)->read($scope);
        $plugins = $diConfig[Validator::class]['plugins'] ?? [];

        $this->assertSame($expectRegistered, array_key_exists('Loki_FieldComponents::validator', $plugins));
    }

    public static function getValidatorPluginScopes(): array
    {
        return [
            'global' => ['global', false],
            'frontend' => ['frontend', true],
            'adminhtml' => ['adminhtml', true],
        ];
    }

    public function testDeprecatedImageOutputIsImageRenderer(): void
    {
        $imageOutput = ObjectManager::getInstance()->get(ImageOutput::class);
        $this->assertInstanceOf(ImageRenderer::class, $imageOutput);
    }
}
