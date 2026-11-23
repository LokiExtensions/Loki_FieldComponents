<?php declare(strict_types=1);

namespace Loki\Field\Component\Test;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionModelInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Option model with option groups for the field components on the test page (layout handle `loki_field_components_index_test`)
 */
class TestOptionGroupModel implements OptionModelInterface, ArgumentInterface
{
    public function getOptions(ComponentViewModelInterface $viewModel): array
    {
        return [
            new OptionGroup('Dark colors', [new Option('black', 'Black'), new Option('brown', 'Brown')], true),
            new OptionGroup('Light colors', [new Option('yellow', 'Yellow'), new Option('white', 'White')]),
            new OptionGroup('Grey colors', [new Option('grey', 'Grey')], true),
        ];
    }
}
