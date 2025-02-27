<?php
declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Component\Base\Field;

use Yireo\LokiFieldComponents\Util\OptionModelInterface;
use Yireo\LokiComponents\Component\ComponentViewModelInterface;

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
     * PHTML template to render this specific field
     *
     * @return string
     */
    public function getFieldTemplate(): string;

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
