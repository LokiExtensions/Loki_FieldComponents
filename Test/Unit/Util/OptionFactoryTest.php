<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Util;

use Loki\Field\Util\Option;
use Loki\Field\Util\OptionFactory;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionGroupFactory;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;

class OptionFactoryTest extends TestCase
{
    public function testCreateOption(): void
    {
        $option = new Option('nl', 'Netherlands', true, true);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(Option::class, [
                'value' => 'nl',
                'label' => 'Netherlands',
                'disabled' => true,
                'selected' => true,
            ])
            ->willReturn($option);

        $this->assertSame($option, (new OptionFactory($objectManager))->create('nl', 'Netherlands', true, true));
    }

    public function testCreateOptionWithDefaults(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(Option::class, [
                'value' => 'nl',
                'label' => 'Netherlands',
                'disabled' => false,
                'selected' => false,
            ])
            ->willReturn(new Option('nl', 'Netherlands'));

        (new OptionFactory($objectManager))->create('nl', 'Netherlands');
    }

    public function testCreateOptionGroup(): void
    {
        $options = [new Option('nl', 'Netherlands')];
        $group = new OptionGroup('Europe', $options, true);

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(OptionGroup::class, [
                'label' => 'Europe',
                'options' => $options,
                'disabled' => true,
            ])
            ->willReturn($group);

        $this->assertSame($group, (new OptionGroupFactory($objectManager))->create('Europe', $options, true));
    }
}
