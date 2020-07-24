<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;

/**
 * Class InputTextController represent input text field
 * @package PSMFields\Controllers\Fields
 */
class InputTextFieldController extends FieldsController
{
    // Not using $this->args for this field

    /**
     * @inheritDoc
     */
    public function render()
    {
        // Apply this to $this->name to get css class, id or other without repeater extra chars
        $cssSelector = $this->getCssSelector(
            sprintf(
                'psmfields__input-%s',
                sanitize_text_field($this->name)
            )
        );

        return sprintf(
            '<section class="psmfields__field psmfields__field--input-text">
                <label for="%1$s">%5$s</label>
                <input type="text" id="%1$s" class="%2$s" name="%3$s" value="%4$s" />
                %6$s
            </section>',
            sprintf(
                '%s_%s',
                $cssSelector,
                uniqid()
            ), // Avoid browser console complaining about multiple ids // %1$s
            $cssSelector, // %2$s
            sanitize_text_field($this->name), // %3$s
            self::sanitizeValue($this->value), // %4$s
            esc_html($this->title), // %5$s
            $this->getFieldDescriptionHtml($this->description) // %6$s
        );
    }

    /**
     * Sanitize input value
     * @param string $value
     * @return string
     */
    public static function sanitizeValue($value)
    {
        return sanitize_text_field($value);
    }

    /**
     * @inheritDoc
     */
    public static function checkRepeaterFieldSupport()
    {
        return true;
    }
}
