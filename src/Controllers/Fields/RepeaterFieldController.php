<?php

namespace PSMFields\Controllers\Fields;

use PSMFields\Controllers\FieldsController;
use PSMFields\Traits\RenderMetaboxFieldTrait;

/**
 * Class RepeaterFieldController
 * @package PSMFields\Controllers\Fields
 */
class RepeaterFieldController extends FieldsController
{
    use RenderMetaboxFieldTrait;

    /**
     * @inheritDoc
     */
    public function __construct(string $name, string $title, $value, string $description, array $args)
    {
        parent::__construct($name, $title, $value, $description, $args);

        /**
         * Prevent setting string for repeater value if we have no meta value on first save
         * It will cause an error on some PHP versions
         */
        if (empty($this->value) || ! is_array($this->value)) {
            $this->value = [];
        }

        // Add script for repeater
        wp_enqueue_script(
            'psmfields-repeater-field',
            esc_url($this->assetsUrl . '/js/repeater.js'),
            [],
            $this->libraryVersion,
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return sprintf(
            '<div class="psmfields__field psmfields__field--repeater" style="background-color: #f2f2f2; padding: 10px;">
                <h4>%3$s</h4>
                <div class="psmfields__repeater">%1$s</div>
                %4$s
                <button type="button"
                class="button button-small button-primary psmfields__repeater-add">%2$s</button>
            </div>',
            $this->renderAddedFields(),
            esc_html__('Add new', $this->textDomain),
            esc_html($this->title),
            $this->getFieldDescriptionHtml($this->description)
        );
    }

    /**
     * Format the saved value to the more readable format and sanitize the values just before it is saved to the DB
     * @param array $values
     * @return array
     */
    public static function sanitizeValue($values)
    {
        if (! is_array($values)) {
            // TODO: Log error here not to interupt save post process but don't save repeater values
            return [];
        }

        if (! isset($values['fieldsToSave']) || ! isset($values['registeredFields']['args']['fields'])) {
            // TODO: Log error here not to interupt save post process but don't save repeater values
            // We must have this set here so can not proceed without it
            return [];
        }

        $fieldsValues = $values['fieldsToSave'];
        $registeredFields = $values['registeredFields']['args']['fields'];
        $keys = \array_keys($fieldsValues);
        // Count how many items we have in the first array item, that is how many groups we need to generate
        $repeaterGroupsCount = count($fieldsValues[$keys[0]]);

        // Add sanatize class to each $keys array as class name to call sanatize method
        $keys = array_combine(
            array_map(
                function ($field) use ($registeredFields) {
                    return $field['class_name'];
                },
                $registeredFields
            ),
            $keys
        );

        /**
         * We need to parse this structure of a array passed from the metabox form to more structured one
         * that is devided by repeater group fields. This is the value structure that is passed to us from the metabox
         * [
         *  'some_name' => [0 => 'value', 1 => 'value 2'...]
         *  'some_other_name' => [0 => 'value', 1 => 'value 2'...]
         * ]
         *
         * So we loop through all of them and reorder the array to the natural state
         * [
         *  // Group one
         *     0 => [
         *          'some_name' => 'value',
         *          'some_other_name' => 'value2',
         *     ],
         *     1 => [
         *          'some_name' => 'value',
         *          'some_other_name' => 'value2',
         *     ]
         * ]
         */
        $sorted = [];
        for ($i = 0; $i < $repeaterGroupsCount; $i++) {
            $group = [];

            foreach ($keys as $className => $key) {
                /**
                 * Call each field class sanitize method
                 */
                $group[$key] = call_user_func_array(
                    [$className, 'sanitizeValue'],
                    [$fieldsValues[$key][$i]]
                );
            }

            $sorted[] = $group;
        }

        return $sorted;
    }

    /**
     * @inheritDoc
     */
    public static function checkRepeaterFieldSupport()
    {
        return false;
    }

    /**
     * Render html output for all repeater field groups
     * @return string
     */
    private function renderAddedFields()
    {
        $registeredFields = $this->args['fields'];
        if (empty($registeredFields)) {
            return esc_html__('No fields were added to this repeater!', $this->textDomain);
        }

        // For initial display where post meta value is '', set default group values
        if (empty($this->value)) {
            $default = [];
            foreach ($registeredFields as $field) {
                $default[$field['name']] = '';
            }
            $this->value[] = $default;
        }

        $group = [];
        foreach ($this->value as $groupFields) {
            $fields = [];
            // We loop through $registeredFields to get class_name for each field which is not available in the value
            foreach ($registeredFields as $field) {
                // make name belong to the repeater field by prefixing it with repeater name
                $name = sprintf(
                    '%s[%s][]',
                    $this->name,
                    $field['name']
                );

                // Get separate field value from the repeater array value before assigning a new name
                // Developer can rename the value so we can have previous saved name and Notice: Undefined index.
                $value = isset($groupFields[$field['name']]) ? $groupFields[$field['name']] : '';

                // Assign new name for in the repeater field
                $field['name'] = $name;

                $fields[] = $this->renderFieldArgs($field, $value);
            }

            // Add fields to the groups array
            $group[] = sprintf(
                '<div class="psmfields__repeater-group">
                    %1$s
                    <button type="button"
                    class="button button-small button-secondary psmfields__repeater-remove">%2$s</button>
                    <span class="dashicons dashicons-arrow-up-alt psmfields__repeater-up"></span>
                    <span class="dashicons dashicons-arrow-down-alt psmfields__repeater-down"></span>
                </div>',
                implode('', $fields),
                '<span class="dashicons dashicons-trash"></span>'
            );
        }

        return implode('', $group);
    }
}
