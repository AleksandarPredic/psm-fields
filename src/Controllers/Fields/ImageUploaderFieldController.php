<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;

/**
 * Class ImageUploaderFieldController represents image uploader field
 * @package PSMFields\Controllers\Fields
 */
class ImageUploaderFieldController extends FieldsController
{
    // Not using $this->args for this field

    /**
     * Preview height in px
     * @var int
     */
    private $previewHeight = 50;

    /**
     * @inheritDoc
     */
    public function __construct(string $name, string $title, $value, string $description, array $args)
    {
        parent::__construct($name, $title, $value, $description, $args);

        wp_enqueue_media();

        // Add script for repeater
        wp_enqueue_script(
            'psmfields-uploader-field',
            esc_url($this->assetsUrl . '/js/uploader.js'),
            [],
            $this->libraryVersion,
            true
        );

        wp_localize_script('psmfields-uploader-field', 'psmfieldsUploaderField', [
            'uploaderTitle' => esc_html__('Upload or select file', $this->textDomain),
            'buttonText' => esc_html__('Select file', $this->textDomain),
            'imgHeight' => $this->previewHeight,
        ]);
    }

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

        $value = sanitize_text_field($this->value);
        $preview = ! empty($value) && intval($value) > 0 ? $this->getPreview($value) : '';

        return sprintf(
            '<section class="psmfields__field psmfields__field--image-uploader">
                <label for="%1$s">%5$s</label>
                <input type="hidden" id="%1$s" class="%2$s psmfields__uploader-input" name="%3$s" value="%4$s" />
                <div class="psmfields__uploader-preview">%9$s</div>
                <button type="button" class="button button-small button-primary psmfields__uploader-add">%7$s</button>
                <button type="button"
                class="button button-small button-secondary psmfields__uploader-remove">%8$s</button>
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
            $this->getFieldDescriptionHtml($this->description), // %6$s
            esc_html__('Add or change', $this->textDomain), // %7$s
            esc_html__('Remove', $this->textDomain), // %8$s
            $preview // %9$s
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
     * Return attachment image html tag
     * @param int $postId
     * @return string
     */
    public function getPreview($postId)
    {
        $url = wp_get_attachment_image_url($postId, 'medium');

        if (empty($url)) {
            return '';
        }

        return sprintf(
            '<img src="%1$s" alt="preview" height="%2$d" />',
            esc_url($url),
            $this->previewHeight
        );
    }

    /**
     * @inheritDoc
     */
    public static function checkRepeaterFieldSupport()
    {
        return true;
    }
}
