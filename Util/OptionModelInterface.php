<?php
declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Util;

use Yireo\LokiComponents\Component\ComponentViewModelInterface;

interface OptionModelInterface
{
    public function getOptions(ComponentViewModelInterface $viewModel): array;
}
