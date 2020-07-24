<?php

namespace PSMFields\Contracts;

/**
 * Interface FieldInputInterface used for to force developers which methods to implement for newly created fields.
 * @package PSMFields\Contracts
 */
interface FieldInterface
{
    /**
     * Fields constructor.
     * @param string $name Field input name attribute
     * @param string $title Field label
     * @param mixed $value Field default value that will be set and returned initially until the field is saved
     * @param string $description Field description
     * @param array $args Additional arguments if needed for different types of fields.
     *                    Some fields may not use this arguments.
     */
    public function __construct(
        string $name,
        string $title,
        $value,
        string $description,
        array $args
    );

    /**
     * Return field html
     * @return string
     */
    public function render();

    /**
     * Sanitize the field values for save in db or output
     * @param mixed $value
     * @return mixed
     */
    public static function sanitizeValue($value);

    /**
     * Return if the field can be used in the Repeater field
     * @return bool
     */
    public static function checkRepeaterFieldSupport();
}
