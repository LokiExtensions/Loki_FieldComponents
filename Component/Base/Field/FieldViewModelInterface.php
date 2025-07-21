<?php
declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Field\Util\OptionModelInterface;
use Loki\Components\Component\ComponentViewModelInterface;

interface FieldViewModelInterface extends ComponentViewModelInterface
{
    /**
     * Name of the field itself.
     *
     * @return string
     */
    public function getFieldName(): string;

    /**
     * Name of the field itself.
     *
     * @return string
     */
    public function getFieldLabel(): string;

    /**
     * In case the field is a text input, this represents the text type (aka `type="email"). By default, this is `text.
     *
     * @return string
     */
    public function getInputType(): string;

    /**
     * Determine if this field is required or not.
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * @return bool
     */
    public function isValid(): bool;

    public function getOptionModel(): ?OptionModelInterface;
}
