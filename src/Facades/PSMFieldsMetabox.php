<?php

namespace PSMFields\Facades;

use PSMFields\Config\BaseConfig;
use PSMFields\Contracts\FacadeExternalUsageMethodsInterface;
use PSMFields\Contracts\FacadeInterface;
use PSMFields\Controllers\Fields\CheckboxFieldController;
use PSMFields\Controllers\Fields\ImageUploaderFieldController;
use PSMFields\Controllers\Fields\InputTextFieldController;
use PSMFields\Controllers\Fields\RepeaterFieldController;
use PSMFields\Controllers\Fields\SelectSingleFieldController;
use PSMFields\Controllers\Fields\TextareaFieldController;
use PSMFields\Controllers\Fields\WpEditorFieldController;
use PSMFields\Controllers\MetaboxAPiController;
use PSMFields\Models\MetaboxModel;
use PSMFields\Traits\SingletonAbstractClassTrait;

/**
 * Class PSMFieldsMetabox used to interact with this library
 * @package PSMFields\Facades
 */
abstract class PSMFieldsMetabox implements FacadeInterface, FacadeExternalUsageMethodsInterface
{
    use SingletonAbstractClassTrait;

    /**
     * Contain all registered fields
     * @var array
     */
    private $fields = [];

    /**
     * Containt class to interact with WP Metabox API
     * @var MetaboxAPiController
     */
    private $metaboxAPI;

    /**
     * Indicate if we started adding fields to the repeater field
     * @var bool
     */
    private $addingToRepeater = false;

    /**
     * Library textdomain
     * @var string
     */
    private $textDomain;

    /**
     * PSMFieldsMetabox constructor.
     */
    protected function __construct()
    {
        $this->metaboxAPI = new MetaboxAPiController(MetaboxModel::getInstance());
        $this->textDomain = BaseConfig::getInstance()->getTextDomain();
    }

    /**
     * Retrieve the field value from the database
     * @param int $postId Post object ID
     * @param string $name Name of the field (meta_key)
     * @return mixed
     */
    public function getFieldValue($postId, $name)
    {
        return $this->metaboxAPI->getFieldValue(
            $postId,
            $this->sanitizeFieldName($name)
        );
    }

    /**
     * Add input text field
     * @param string $name Input field name
     * @param string $title Input field title
     * @param string $description Field description
     * @return PSMFieldsMetabox
     */
    public function addInputTextField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => InputTextFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [] // Not needed for input
            ]
        );

        return $this;
    }

    /**
     * Add Textarea field
     * @param string $name Textarea field name
     * @param string $title Textarea field title
     * @param string $description Textarea description
     * @return PSMFieldsMetabox
     */
    public function addTextareaField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => TextareaFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [] // Not needed for textarea
            ]
        );

        return $this;
    }

    /**
     * Add WP Editor field
     * This field is not supported by the Repeater
     * @param string $name Editor field name
     * @param string $title Editor field title
     * @param string $description Editor description
     * @return PSMFieldsMetabox
     */
    public function addWpEditorField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => WpEditorFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [] // Not needed for Editor field
            ]
        );

        return $this;
    }

    /**
     * Add select field with single selected value
     * @param string $name Select field name
     * @param string $title Select field title
     * @param array $options Array of options in format [value => text, value2 => text2]
 *                           The key will always be converted to string
     * @param string $description Field description
     * @return PSMFieldsMetabox
     */
    public function addSelectField(
        string $name,
        string $title,
        array $options,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => SelectSingleFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [
                    'options' => $options // Options
                ]
            ]
        );

        return $this;
    }

    /**
     * Add checkbox field with one value. The saved value in db will be string: if checked 1 else 0.
     * @param string $name Checkbox field name
     * @param string $title Checkbox field title
     * @param string $description Checkbox field description
     * @return PSMFieldsMetabox
     */
    public function addCheckboxField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => CheckboxFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => []
            ]
        );

        return $this;
    }

    /**
     * Add image uploader field. The image id is saved in post_meta.
     * @param string $name Image uploader field name
     * @param string $title Image uploader field title
     * @param string $description Image uploader description
     * @return PSMFieldsMetabox
     */
    public function addImageUploaderField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => ImageUploaderFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [] // Not needed for input
            ]
        );

        return $this;
    }

    /**
     * Start accepting new fields and store them as repeater fields
     * @param string $name Repeater field name
     * @param string $title Repeater field title
     * @param string $description Repeater field description
     * @return PSMFieldsMetabox
     */
    public function startRepeaterField(
        string $name,
        string $title,
        string $description = ''
    ) {
        $this->addField(
            [
                'class_name' => RepeaterFieldController::class,
                'name' => $this->sanitizeFieldName($name),
                'title' => $title,
                'description' => $description,
                'args' => [
                    'fields' => [] // Fields
                ],
            ]
        );

        $this->addingToRepeater = true;

        return $this;
    }

    /**
     * Stop storing new fields as repeater fields
     * @return PSMFieldsMetabox
     */
    public function endRepeaterField()
    {
        $this->addingToRepeater = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFieldsArray()
    {
        return $this->fields;
    }

    /**
     * Register metabox using the add_meta_boxes hook and add save metabox callback.
     *          We can add more fields to the metabox intance after we call this method.
     * @param string $id Id for the metabox
     * @param string $title Title of the meta box.
     * @param array $screens Array of post types to display metabox.
     * @param string $context Available: normal | side | advanced.
     *        The context within the screen where the boxes should display.
     * @param string $priority Available: default | high | low
     * @return PSMFieldsMetabox
     */
    public function register(
        string $id,
        string $title,
        array $screens,
        string $context = 'normal',
        string $priority = 'default'
    ) {
        $this->metaboxAPI->setRequiredProperties(
            $id,
            $title,
            $screens,
            $context,
            $priority,
            $this
        );

        add_action('add_meta_boxes', [$this->metaboxAPI, 'add']);
        foreach ($screens as $screen) {
            add_action(
                sprintf(
                    'save_post_%s',
                    // Use same sanitizer here to comply with the one in MetaboxApiController class
                    sanitize_text_field($screen)
                ),
                [$this->metaboxAPI, 'save']
            );
        }

        return $this;
    }

    /**
     * Return all registered fields for the current class instance
     * @param array $args Field arguments
     */
    private function addField(array $args)
    {
        if (! $this->addingToRepeater) {
            // WPCS ok. All fields are sanitized in their own class before any output
            $this->fields[] = $args;

            return;
        }

        // Discard not supported Repeater fields before we add them to the registered fields array
        if (! call_user_func([$args['class_name'], 'checkRepeaterFieldSupport'])) {
            trigger_error(
                sprintf(
                    esc_html__('Field: %s is not allowed in the Repeater'),
                    $args['class_name']
                ),
                E_USER_NOTICE
            );

            return;
        }

        end($this->fields);
        $this->fields[key($this->fields)]['args']['fields'][] = $args;
        reset($this->fields);
    }

    /**
     * Apply strtolower and sanitize_text_field function, replace '-' and spaces with '_';
     * @param string $name Field name
     * @return string
     */
    private function sanitizeFieldName($name)
    {
        return str_replace(['-', ' '], '_', strtolower(sanitize_file_name($name)));
    }
}
