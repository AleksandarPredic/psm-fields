<?php

namespace PSMFields\Contracts;

/**
 * Interface FacadeInterface used for all facades
 * @package PSMFields\Contracts
 */
interface FacadeInterface
{
    /**
     * Return registered fields array
     * @return array
     */
    public function getFieldsArray();
}
