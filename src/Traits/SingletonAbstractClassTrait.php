<?php

namespace PSMFields\Traits;

/**
 * Trait SingletonTrait to use in abstract singleton classes
 * @package PSMFields\Traits
 */
trait SingletonAbstractClassTrait
{
    /**
     * Holds all instances of a static child classes
     * @var SingletonAbstractClassTrait
     */
    private static $instances = [];

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * Return child class instance
     * @return $this
     */
    public static function getInstance()
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }
}
