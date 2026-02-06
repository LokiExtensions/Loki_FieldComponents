<?php
declare(strict_types=1);

namespace Loki\Field\Util;

use Loki\Components\Component\ComponentViewModelInterface;

interface OptionModelInterface
{
    /**
     * @param ComponentViewModelInterface $viewModel
     *
     * @return string[]|OptionInterface[]
     */
    public function getOptions(ComponentViewModelInterface $viewModel): array;
}
