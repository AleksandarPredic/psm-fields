<?php

namespace PSMFields\Traits;

/**
 * Trait SingletonTrait
 * @package PSMFields\Traits
 */
trait SingletonTrait
{
    /**
     * Class instance
     * @var SingletonTrait
     */
    private static $instance;

    /**
     * Return Class instance
     * @return $this
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
