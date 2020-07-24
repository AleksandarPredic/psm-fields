<?php
// If you are using the namespaces

namespace NamespaceNameExample;

use PSMFields\Facades\PSMFieldsMetabox;

/**
 * Class ExampleMetaboxController used in child theme for one metabox
 */
class ExampleMetaboxController extends PSMFieldsMetabox
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->register(
            'example_metabox_id', // Metabox unique id
            esc_html__('Example metabox', 'textdomain'), // Title
            ['post', 'page'] // Array of post types that will use the metabox
            // Context is optional parameter
            // Priority is optional parameter
        )
            ->addInputTextField(
                'prefix_input_text_field', // Unique post_meta key
                esc_html__('Example input text field', 'textdomain'),
                sprintf(
                    esc_html__('Some description with %s', 'textdomain'),
                    sprintf('<a href="https://google.com" target="_blank">%s</a>', esc_html__('link', 'textdomain'))
                ) // Optional
            )
            ->addWpEditorField(
                'prefix_wpeditor_field', // Unique post_meta key
                esc_html__('Example WP editor field', 'textdomain'),
                esc_html__('Example description for WP editor field', 'textdomain') // Optional
            )
            ->addTextareaField(
                'prefix_textarea_field', // Unique post_meta key
                esc_html__('Example textarea', 'textdomain'),
                esc_html__('Example description for textarea field', 'textdomain') // Optional
            )
            ->addSelectField(
                'prefix_select_field', // Unique post_meta key
                esc_html__('Example select field', 'textdomain'),
                [
                    // Key is the value that is saved in database
                    '' => esc_html__('None', 'textdomain'),
                    'value1' => esc_html__('First value', 'textdomain'),
                    'value2' => esc_html__('Second value', 'textdomain'),
                    'value3' => esc_html__('Third value', 'textdomain'),
                ],
                esc_html__('Example description for select field', 'textdomain') // Optional
            )
            ->addCheckboxField( // Store string 1 or 0 in database
                'prefix_checkbox_field', // Unique post_meta key
                esc_html__('Example checkbox field', 'textdomain'),
                esc_html__('Example description for checkbox field', 'textdomain') // Optional
            )
            ->addImageUploaderField( // Store image attachment id in database
                'prefix_image_uploader_field', // Unique post_meta key
                esc_html__('Example image uploader field', 'textdomain'),
                esc_html__('Example description for image uploader field', 'textdomain') // Optional
            )
            // You can have multiple repeater fields in the same metabox
            ->startRepeaterField( // Store array of all fields in database
                'prafix_repeater_field_1', // Unique post_meta key
                esc_html__('Example repeater field', 'textdomain'),
                esc_html__('Example repeater field description', 'textdomain') // Optional
            )
                ->addInputTextField(
                    'input_text_', // Don't need to be unique post_meta key in the repeater field
                    esc_html__('Example input text field', 'textdomain')
                    // We can add description as next parameter if needed
                )
                ->addTextareaField(
                    'textarea', // Unique post_meta key
                    esc_html__('Example textarea', 'textdomain'),
                    esc_html__('Example description for textarea field', 'textdomain') // Optional
                )
                ->addSelectField(
                    'select_field', // Don't need to be unique post_meta key in the repeater field
                    esc_html__('Example select field', 'textdomain'),
                    [
                        // Key is the value that is saved in database
                        'value1' => esc_html__('First value', 'textdomain'),
                        'value2' => esc_html__('Second value', 'textdomain'),
                        'value3' => esc_html__('Third value', 'textdomain'),
                    ]
                    // We can add description as next parameter if needed
                )
                ->addCheckboxField(
                    'checkbox_field', // Don't need to be unique post_meta key in the repeater field
                    esc_html__('Example select field', 'textdomain')
                    // We can add description as next parameter if needed
                )
                ->addImageUploaderField(
                    'image_uploader_field', // Don't need to be unique post_meta key in the repeater field
                    esc_html__('Example image uploader field', 'textdomain')
                    // We can add description as next parameter if needed
                )
            ->endRepeaterField();
    }
}
