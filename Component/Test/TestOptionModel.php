<?php declare(strict_types=1);

namespace Loki\Field\Component\Test;

use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionModelInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Option model for the field components on the test page (layout handle `loki_field_components_index_test`)
 */
class TestOptionModel implements OptionModelInterface, ArgumentInterface
{
    public function getOptions(ComponentViewModelInterface $viewModel): array
    {
        $options = [];
        if ($viewModel->getBlock()?->getIncludeEmptyOption()) {
            $options[] = new Option('', 'Choose a color');
        }

        $options[] = new Option('red', 'Red');
        $options[] = new Option('green', 'Green');
        $options[] = new Option('blue', 'Blue');
        $options[] = new Option('black', 'Black', true);

        return $options;
    }
}
