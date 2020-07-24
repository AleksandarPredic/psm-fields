<?php

namespace PSMFields\Contracts;

interface FacadeExternalUsageMethodsInterface
{

    /**
     * Register metabox and save callback
     */
    public function init();

    /**
     * Retrieve the field value from the database
     * @param int $postId Post object ID
     * @param string $name Name of the field (meta_key)
     * @return mixed
     */
    public function getFieldValue($postId, $name);

    /**
     * Register metabox using the add_meta_boxes hook and add save metabox callback.
     *          We can add more fields to the metabox intance after we call this method.
     * @param string $id Id for the metabox
     * @param string $title Title of the meta box.
     * @param array $screens Array of post types to display metabox.
     * @param string $context Available: normal | side | advanced.
     *        The context within the screen where the boxes should display.
     * @param string $priority Available: default | high | low
     * @return FacadeExternalUsageMethodsInterface
     */
    public function register(
        string $id,
        string $title,
        array $screens,
        string $context = 'normal',
        string $priority = 'default'
    );

    /**
     * Add input text field
     * @param string $name Input field name
     * @param string $title Input field title
     * @param string $description Field description
     * @return FacadeExternalUsageMethodsInterface
     */
    public function addInputTextField(
        string $name,
        string $title,
        string $description = ''
    );

    /**
     * Add select field with single selected value
     * @param string $name Select field name
     * @param string $title Select field title
     * @param array $options Array of options in format [value => text, value2 => text2]
     * @param string $description Field description
     * @return FacadeExternalUsageMethodsInterface
     */
    public function addSelectField(
        string $name,
        string $title,
        array $options,
        string $description = ''
    );

    /**
     * Add checkbox field with one value. The saved value in db will be string: if checked 1 else 0.
     * @param string $name Checkbox field name
     * @param string $title Checkbox field title
     * @param string $description Checkbox field description
     * @return FacadeExternalUsageMethodsInterface
     */
    public function addCheckboxField(
        string $name,
        string $title,
        string $description = ''
    );

    /**
     * Add image uploader field. The image id is saved in post_meta.
     * @param string $name Image uploader field name
     * @param string $title Image uploader field title
     * @param string $description Image uploader description
     * @return FacadeExternalUsageMethodsInterface
     */
    public function addImageUploaderField(
        string $name,
        string $title,
        string $description = ''
    );

    /**
     * Start accepting new fields and store them as repeater fields
     * @param string $name Repeater field name
     * @param string $title Repeater field title
     * @param string $description Repeater field description
     * @return FacadeExternalUsageMethodsInterface
     */
    public function startRepeaterField(
        string $name,
        string $title,
        string $description = ''
    );

    /**
     * Stop storing new fields as repeater fields
     * @return FacadeExternalUsageMethodsInterface
     */
    public function endRepeaterField();
}
