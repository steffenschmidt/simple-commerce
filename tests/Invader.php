<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests;

use ReflectionClass;

class Invader
{
    public object $obj;
    public ReflectionClass $reflected;

    public function __construct(object $obj)
    {
        $this->obj = $obj;
        $this->reflected = new ReflectionClass($obj);
    }

    public function __get(string $name)
    {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        return $property->getValue($this->obj);
    }

    public function __set(string $name, mixed $value): void
    {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        $property->setValue($this->obj, $value);
    }

    public function __call(string $name, array $params = [])
    {
        $method = $this->reflected->getMethod($name);

        $method->setAccessible(true);

        return $method->invoke($this->obj, ...$params);
    }
}
