<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;

/**
 * Class WpEditorFieldController represent WordPress classis editor field
 * @package PSMFields\Controllers\Fields
 */
class WpEditorFieldController extends FieldsController
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

        $uniqueCssSelector = sprintf(
            '%s_%s',
            $cssSelector,
            uniqid()
        ); // Avoid browser console complaining about multiple ids // %1$s

        ob_start();
        wp_editor(
            self::sanitizeValue($this->value),
            $uniqueCssSelector,
            [
                'textarea_name' => $this->name,
                'editor_class' => $cssSelector,
                'textarea_rows' => 7,
            ]
        );
        $editor = ob_get_clean();

        return sprintf(
            '<section class="psmfields__field psmfields__field--wp-editor">
                <label for="%1$s">%2$s</label>
                %4$s
                %3$s
            </section>',
            $uniqueCssSelector, // %1$s
            esc_html($this->title), // %2$s
            $this->getFieldDescriptionHtml($this->description), // %3$s
            $editor // %4$s
        );
    }

    /**
     * Sanitize editor value
     * @param string $value
     * @return string
     */
    public static function sanitizeValue($value)
    {
        return wp_kses_post($value);
    }

    /**
     * @inheritDoc
     */
    public static function checkRepeaterFieldSupport()
    {
        return false;
    }
}
