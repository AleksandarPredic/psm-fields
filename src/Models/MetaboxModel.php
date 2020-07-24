<?php

namespace PSMFields\Models;

use PSMFields\Traits\SingletonTrait;

/**
 * Class MetaboxModel
 * @package PSMFields\Models
 */
class MetaboxModel
{
    use SingletonTrait;

    private function __construct()
    {
    }

    /**
     * Save field in database
     * @hooked on save_post_{$post->post_type} action hook to save data from metabox
     * @return  int|bool The new meta field ID if a field with the given key didn't exist and was therefore added,
     *          true on successful update, false on failure.
     */
    public function saveField($postId, $metaKey, $value)
    {
        /**
         * @hook psmfields_model_save_field_{meta_key}
         * @param mixed $value Value passed to the model
         * @param int $postId Post object id
         */
        $value = apply_filters(
            sprintf(
                'psmfields_model_save_field_%s',
                $metaKey
            ),
            $value,
            $postId
        );

        return update_post_meta($postId, $metaKey, $value);
    }

    /**
     * Retrieve the value from the database
     * @param int $postId Post object ID
     * @param string $metaKey Name of the field (meta_key)
     * @return mixed
     */
    public function getField($postId, $metaKey)
    {
        /**
         * @hook psmfields_model_get_field_{meta_key}
         * @param string $metaKey Registered field name that is actually meta_key in the post_meta table
         * @param int $postId Post object id
         */
        return apply_filters(
            sprintf(
                'psmfields_model_get_field_%s',
                $metaKey
            ),
            get_post_meta($postId, $metaKey, true),
            $postId
        );
    }
}
