<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;

/**
 * Class CheckboxFieldController represents one checkbox field
 * @package PSMFields\Controllers\Fields
 */
class CheckboxFieldController extends FieldsController
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
                'psmfields__checkbox-%s',
                sanitize_text_field($this->name)
            )
        );

        $checked = self::sanitizeValue($this->value) === '1';

        /**
         * Use hidden input to send value if not checked as we need to keep the same array structure for the repeater
         * Use inline javascript to disable hidden input so it don't send value if checkbox is checked
         */
        return sprintf(
            '<section class="psmfields__field psmfields__field--checkbox">
                <label for="%1$s">%4$s</label>
                <input type="hidden" name="%3$s" value="0" %7$s/>
                <input
                type="checkbox"
                id="%1$s"
                class="%2$s"
                name="%3$s"
                value="1"
                %6$s
                onChange="this.previousElementSibling.disabled=this.checked;"
                />
                %5$s
            </section>',
            sprintf(
                '%s_%s',
                $cssSelector,
                uniqid()
            ), // %1$s
            $cssSelector, // %2$s
            sanitize_text_field($this->name), // %3$s
            esc_html($this->title), // %4$s
            $this->getFieldDescriptionHtml($this->description), // %5$s
            $checked ? ' checked="checked"' : '', // %6$s
            $checked ? 'disabled' : '' // %7$s // Add disabled input so we don't send value twice in the repeater
        );
    }

    /**
     * Sanitize selected value
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
