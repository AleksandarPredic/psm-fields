<?php

namespace PSMFields\Traits;

use PSMFields\Contracts\FieldInterface;

/**
 * Trait RenderMetaboxFieldTrait used to render field class output for single fields and inside a repeater
 * @package PSMFields\Traits
 */
trait RenderMetaboxFieldTrait
{
    /**
     * Render each field for the metabox content
     * @param array $args Field arguments array that are defined in the PSMFields\Facades\PSMFieldsMetabox
     *                    facade method for every field definition. Array must contain this keys:
     *                     [
     *                      'class_name'  => {fieldClass}::class,
     * 'name'        => $name,
     * 'title'       => $title,
     * 'description' => $description,
     * 'args'        => []
     *                     ]
     * @param mixed $value Post object ID
     * @return string Field rendered html as string
     */
    protected function renderFieldArgs($args, $value)
    {
        return $this->renderField(
            new $args['class_name'](
                $args['name'],
                $args['title'],
                $value,
                $args['description'],
                $args['args']
            )
        );
    }

    /**
     * Render field class html output as string
     * @param FieldInterface $fieldClassName Field class instance
     * @return string Field rendered html as string
     */
    private function renderField(FieldInterface $fieldClassName)
    {
        return $fieldClassName->render();
    }
}
