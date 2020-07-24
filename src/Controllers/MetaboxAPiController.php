<?php

namespace PSMFields\Controllers;

use PSMFields\Config\BaseConfig;
use PSMFields\Contracts\FacadeInterface;
use PSMFields\Controllers\Fields\RepeaterFieldController;
use PSMFields\Models\MetaboxModel;
use PSMFields\Traits\RenderMetaboxFieldTrait;

/**
 * Class MetaboxAPiController responsible for using the WP API to interact with metabox
 * @package PSMFields\Controllers
 */
final class MetaboxAPiController
{
    use RenderMetaboxFieldTrait;

    // Config properties

    /**
     * Basic config for the library
     * @var BaseConfig
     */
    private $config;

    /**
     * Library textdomain
     * @var string
     */
    private $textDomain;

    /**
     * Library version
     * @var string
     */
    private $libraryVersion;

    /**
     * Return assets url without backslash at the end
     * @var string
     */
    private $assetsUrl;

    // Other properties

    /**
     * Metabox id that we can reference if needed.
     * @var string Meta box ID
     */
    private $id;

    /**
     * Metabox title
     * @var string Title of the meta box.
     */
    private $title;

    /**
     * Array of post types to display metabox. Can accept other params.
     * @var string|array|\WP_Screen Available: string|array|WP_Screen
     */
    private $screens;

    /**
     * The context within the screen where the boxes should display.
     * @var string Available: normal | side | advanced
     */
    private $context;

    /**
     * Metabox priority
     * @var string Available: default | high | low
     */
    private $priority;

    /**
     * Facade object used to interact with this library by the dev
     * @var FacadeInterface
     */
    private $facadeInstance;

    /**
     * Metabox model to interact with database
     * @var MetaboxModel
     */
    private $model;

    /**
     * Hidden input that will hold json with all registered fields array
     * Which we can get on save callback to check for metabox fields
     * @var string
     */
    private $metaboxFieldsHiddenInputName;

    /**
     * MetaboxAPiController constructor.
     * @param MetaboxModel $model
     */
    public function __construct(MetaboxModel $model)
    {
        $this->model = $model;
        $this->config = BaseConfig::getInstance();
        $this->textDomain = $this->config->getTextDomain();
        $this->assetsUrl = $this->config->getAssetsUrl();
        $this->libraryVersion = $this->config->getVersion();
    }

    /**
     * Set required properties. Most of them for the function add_meta_box so we can later register one
     * @param string $id Id for the metabox
     * @param string $title Title of the meta box.
     * @param array $screens Array of post types to display metabox.
     * @param string $context Available: normal | side | advanced. The context within the screen to display the boxes.
     * @param string $priority Available: default | high | low
     * @param FacadeInterface $facadeInstance Facade instance used to add metabox and fields
     */
    public function setRequiredProperties(
        string $id,
        string $title,
        array $screens,
        string $context,
        string $priority,
        FacadeInterface $facadeInstance
    ) {
        $this->id = sanitize_text_field($id);
        $this->title = sanitize_text_field($title);
        $this->screens = array_map('sanitize_text_field', $screens);
        $this->context = in_array($context, ['normal', 'side', 'advanced'], true) ? $context : 'normal';
        $this->priority = in_array($priority, ['default', 'high', 'low'], true) ? $priority : 'default';
        $this->facadeInstance = $facadeInstance;

        $this->metaboxFieldsHiddenInputName = sprintf(
            '%s_%s',
            'psmfields_metabox_fields',
            $this->id
        );

        // Add metabox styles
        add_action('admin_enqueue_scripts', function ($hook) {
            if (($hook !== 'post.php') && ($hook !== 'post-new.php')) {
                return;
            }

            global $post_type;
            if (! in_array($post_type, $this->screens)) {
                return;
            }

            // Add script for repeater
            wp_enqueue_style(
                'psmfields',
                esc_url($this->assetsUrl . '/css/styles.css'),
                [],
                $this->libraryVersion
            );
        });
    }

    /**
     * Call add_meta_box function to add metabox
     * https://developer.wordpress.org/reference/functions/add_meta_box/
     */
    public function add()
    {
        add_meta_box(
            $this->id, // Unique ID
            $this->title, // Box title
            [$this, 'html'],   // Content callback, must be of type callable
            $this->screens,  // Post type,
            $this->context, // Context
            $this->priority // Priority
        );
    }

    /**
     * Save meta box content.
     * @param int $post_id Post ID
     */
    public function save(int $post_id)
    {
        if (! isset($_POST[$this->metaboxFieldsHiddenInputName])) {
            // TODO: Log error here or throw notice - maybe not throwing notice on save as of gutenberg saving via ajax
            return;
        }

        $registeredFields = json_decode(urldecode($_POST[$this->metaboxFieldsHiddenInputName]), true);

        if (empty($registeredFields) || ! is_array($registeredFields)) {
            // TODO: log error here so we know json parse failed
            return;
        }

        foreach ($registeredFields as $field) {
            if (! isset($field['name']) || ! array_key_exists($field['name'], $_POST)) {
                // TODO: Log error here or throw exception
                continue;
            }

            /**
             * Sanitize all fields before save
             */
            try {
                $name = $field['name'];
                $value = $_POST[$name];
                $type = $field['class_name'];

                if ($type === RepeaterFieldController::class) {
                    $value = call_user_func_array([$type, 'sanitizeValue'], [
                        [
                            'fieldsToSave' => $value,
                            'registeredFields' => $field
                        ]
                    ]);
                } else {
                    $value = call_user_func_array([$type, 'sanitizeValue'], [$value]);
                }
            } catch (\Exception $e) {
                // TODO: Log exception here not to interupt save
            }

            $this->model->saveField(
                $post_id,
                $name,
                $value
            );
        }
    }

    /**
     * Meta box display callback.
     * @param \WP_Post $post Current post object.
     */
    public function html($post)
    {
        $postId = get_the_ID();

        if (empty($postId)) {
            esc_html_e('Global post object id is empty! Can not display metabox!', $this->textDomain);

            return;
        }

        /**
         * @hook psmfields_metabox_prerender_{metabox_id}
         * @param FacadeInterface $facadeInstance
         * @param \WP_Post $post
         */
        do_action(
            sprintf('psmfields_metabox_prerender_%s', $this->id),
            $this->facadeInstance,
            $post
        );

        $fields = $this->getRegisteredFields();

        if (empty($fields)) {
            esc_html_e('No fields registered!', $this->textDomain);

            return;
        }

        /**
         * Print hidden input so we can pickup all registered fields.
         *
         * If we use current instance, we will not have fields registered after admin_init hook
         * which is not good if we want to preform some logic while rendering fields using html callback here
         */
        printf(
            '<textarea name="%1$s" style="display: none !important;">%2$s</textarea>',
            $this->metaboxFieldsHiddenInputName,
            urlencode(json_encode($fields))
        );

        if (empty($fields)) {
            return;
        }

        /**
         * Output all registered fields
         */
        $output = [];

        try {
            foreach ($fields as $field) {
                $value = $this->model->getField($postId, $field['name']);
                $output[] = $this->renderFieldArgs($field, $value);
            }
        } catch (\Exception $e) {
            printf(
                $output[] = esc_html__('Error displaying field: %s', $this->textDomain),
                $e->getMessage()
            );
        }

        printf(
            '<div class="psmfields">%s</div>',
            implode('', $output)
        );

        /**
         * @hook psmfields_metabox_after_render_{metabox_id}
         * @param FacadeInterface $facadeInstance
         * @param \WP_Post $post
         */
        do_action(
            sprintf('psmfields_metabox_after_render_%s', $this->id),
            $this->facadeInstance,
            $post
        );
    }

    /**
     * Retrieve the field value from the model
     * @param int $postId Post object ID
     * @param string $name Name of the field (meta_key)
     * @return mixed
     */
    public function getFieldValue($postId, $name)
    {
        return $this->model->getField($postId, $name);
    }

    /**
     * Return array of all the registered fields in the FacadeInterface object property $fields
     * @return array
     */
    private function getRegisteredFields()
    {
        /**
         * @hook psmfields_metabox_fields_{metabox_id}
         * @param array $registeredFields All registered field for the current class instance
         */
        return apply_filters(
            sprintf('psmfields_metabox_fields_%s', $this->id),
            $this->facadeInstance->getFieldsArray()
        );
    }
}
