<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;

/**
 * Class SelectSingleFieldController represent select field that can select only one value
 * @package PSMFields\Controllers\Fields
 */
class SelectSingleFieldController extends FieldsController
{
    // Using $this->args as array for options in format [value => label, value2 => label2]

    /**
     * @inheritDoc
     */
    public function render()
    {
        // Apply this to $this->name to get css class, id or other without repeater extra chars
        $cssSelector = $this->getCssSelector(
            sprintf(
                'psmfields__select-%s',
                sanitize_text_field($this->name)
            )
        );

        return sprintf(
            '<section class="psmfields__field psmfields__field--select-single">
                <label for="%1$s">%5$s</label>
                <select id="%1$s" class="%2$s" name="%3$s">%4$s</select>
                %6$s
            </section>',
            sprintf(
                '%s_%s',
                $cssSelector,
                uniqid()
            ), // Avoid browser console complaining about multiple ids // %1$s
            $cssSelector, // %2$s
            sanitize_text_field($this->name), // %3$s
            $this->renderOptions(), // %4$s
            esc_html($this->title), // %5$s
            $this->getFieldDescriptionHtml($this->description) // %6$s
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

    /**
     * Render select options html
     * @return string
     */
    private function renderOptions()
    {
        $registeredOptions = $this->args['options'];
        if (empty($registeredOptions)) {
            return '';
        }

        $output = [];
        foreach ($registeredOptions as $value => $text) {
            $output[] = sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                self::sanitizeValue($value),
                $this->value === $value ? ' selected="selected"' : '',
                esc_html($text)
            );
        }

        return implode('', $output);
    }
}
