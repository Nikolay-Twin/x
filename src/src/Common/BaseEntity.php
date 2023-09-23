<?php

declare(strict_types=1);

namespace Common;

/**
 *
 */
abstract class BaseEntity
{
    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value)
    {
        $method = 'change'. $name;
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }
}