<?php

namespace PSMFields\Controllers;

use PSMFields\Config\BaseConfig;
use PSMFields\Contracts\FieldInterface;

/**
 * Class InputController act as a base for all existing and new fields
 * @package PSMFields\Controllers\Fields
 */
abstract class FieldsController implements FieldInterface
{
    // Config properties

    /**
     * Basic config for the library
     * @var BaseConfig
     */
    protected $config;

    /**
     * Library version
     * @var string
     */
    protected $libraryVersion;

    /**
     * Library textdomain
     * @var string
     */
    protected $textDomain;

    /**
     * Return assets url without backslash at the end
     * @var string
     */
    protected $assetsUrl;

    // Most basic properties

    /**
     * Field name attribute
     * @var string
     */
    protected $name;

    /**
     * Field label
     * @var string
     */
    protected $title;

    /**
     * Field value from database
     * @var string
     */
    protected $value;

    /**
     * Field description
     * @var string
     */
    protected $description;

    // Additional properties

    /**
     * Additional arguments if needed for different types of fields. Some fields may not use this arguments.
     * For example: Select will need options so define them within as $args.
     * For example: Repeater field will will need fields so define them as $args
     * @var array
     */
    protected $args;

    /**
     * @inheritDoc
     */
    public function __construct(
        string $name,
        string $title,
        $value,
        string $description,
        array $args
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
        $this->description = $description;
        $this->args = $args;
        $this->config = BaseConfig::getInstance();
        $this->textDomain = $this->config->getTextDomain();
        $this->assetsUrl = $this->config->getAssetsUrl();
        $this->libraryVersion = $this->config->getVersion();
    }

    /**
     * Remove extra parentheses '[' and ']' to make css id and class more readable, as we need them for the
     * field name parameter in the repeater field.
     * @param string $name Field name param $field['name'] passed in Facade PSMFields\Facades\PSMFieldsMetabox when the
     *                     field was added to the all fields array
     * @return string
     */
    protected function getCssSelector($name)
    {
        return trim(
            str_replace(
                ['[', ']'],
                '_',
                $name
            ),
            '_'
        );
    }

    /**
     * Return html as string for field description
     * @param string $description Support html tags which can be used in the post content
     * @return string
     */
    protected function getFieldDescriptionHtml($description)
    {
        return ! empty($description) ?
            sprintf(
                '<p class="psmfields__field__description">%s</p>',
                wp_kses_post($description)
            ) : '';
    }
}
