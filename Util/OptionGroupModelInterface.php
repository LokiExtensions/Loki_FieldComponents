<?php
declare(strict_types=1);

namespace Loki\Field\Util;

use Loki\Components\Component\ComponentViewModelInterface;

interface OptionGroupModelInterface extends OptionModelInterface
{
    /**
     * @param ComponentViewModelInterface $viewModel
     *
     * @return OptionGroupInterface[]
     */
    public function getOptions(ComponentViewModelInterface $viewModel): array;
}
