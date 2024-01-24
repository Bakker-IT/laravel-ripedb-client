<?php

namespace Bakkerit\LaravelRipedbClient\Models;

use Bakkerit\LaravelRipedbClient\Adapters\GuzzleAdapter;
use Dormilich\WebService\RIPE\WebService;

class BaseModel implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    private mixed $wrappedObject;
    private static $ripe = [];

    public static function getRipeApi($connection = 'default') {
        if(isset(self::$ripe[$connection])) {
            return self::$ripe[$connection];
        }

        $config = config('ripe');
        if(!isset($config['connections']) || !isset($config['connections'][$connection])) {
            throw new \RuntimeException('Unable to resolve connection \'' . $connection . '\'.');
        }

        $config = $config['connections'][$connection];

        $adapter = new GuzzleAdapter([]);
        self::$ripe[$connection] = new WebService($adapter, [
            'environment' => $config['environment'],
            'password'    => $config['password'],
        ]);

        return self::$ripe[$connection];
    }

    public function getNewInstance(...$args) {
        $classNameParts = explode('\\', static::class);
        $shortClassName = end($classNameParts);
        $targetClassName = "\\Dormilich\\WebService\\RIPE\\RPSL\\$shortClassName";

        if (!class_exists($targetClassName)) {
            throw new \RuntimeException("Target class $targetClassName not found");
        }

        $instance = new $targetClassName(...$args);
        return $instance;
    }

    public function find($primaryKey) {
        $lookup = $this->getNewInstance($primaryKey);
        $ripe = self::getRipeApi();
        $object = $ripe->read($lookup);

        $this->wrappedObject = $object;

        return $this;
    }

    /**
     * Proxy methods
     */
    public function getPrimaryKey()
    {
        return $this->wrappedObject->getPrimaryKey();
    }

    public function offsetExists($offset)
    {
        return $this->wrappedObject->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->wrappedObject->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->wrappedObject->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->wrappedObject->offsetUnset($offset);
    }

    public function getIterator()
    {
        return $this->wrappedObject->getIterator();
    }

    public function count()
    {
        return $this->wrappedObject->count();
    }

    public function jsonSerialize()
    {
        return $this->wrappedObject->jsonSerialize();
    }

}
