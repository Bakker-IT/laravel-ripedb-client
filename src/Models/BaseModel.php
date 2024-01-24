<?php

namespace Bakkerit\LaravelRipedbClient\Models;

use Bakkerit\LaravelRipedbClient\Adapters\GuzzleAdapter;
use Dormilich\WebService\RIPE\AbstractObject;
use Dormilich\WebService\RIPE\WebService;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class BaseModel implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    private AbstractObject $wrappedObject;
    private static $ripe = [];
    private bool $new;
    private string $connection = 'default';

    public static function getRipeApi($connection = 'default') {
        if(isset(self::$ripe[$connection])) {
            return self::$ripe[$connection];
        }

        $config = config('ripe');
        if(!isset($config['connections'][$connection])) {
            throw new \RuntimeException('Unable to resolve connection \'' . $connection . '\'.');
        }

        if($connection != 'default') {
            dd($connection, isset($config['connections'][$connection]));
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
        try {
            $lookup = $this->getNewInstance($primaryKey);
            $ripe = self::getRipeApi($this->connection);
            $object = $ripe->read($lookup);

            $this->wrappedObject = $object;
            $this->new = false;

            return $this;
        } catch (GuzzleException $e) {
            return null;
        }
    }

    public function findOrNew($primaryKey) {
        $find = $this->find($primaryKey);
        if(!is_null($find)) {
            return $find;
        }

        $this->wrappedObject = $this->getNewInstance($primaryKey);
        $this->new = true;

        return $this;
    }

    public function save() {
        $ripe = self::getRipeApi($this->connection);

//        Automatically add maintainer references.
//        $mntner = config('ripe.connections.' . $this->connection . 'maintainer');
//        $attr = $this->wrappedObject['mnt-by'];
//        $found = false;
//        if(is_array($attr)) {
//            foreach($attr as $value) {
//                if($value->getValue() == $mntner) {
//                    $found = true;
//                }
//            }
//        }

        if($this->new) {
            $result = $ripe->create($this->wrappedObject);
        } else {
            $result = $ripe->update($this->wrappedObject);
        }

        $this->wrappedObject = $result;
        $this->new = false;

        return $this;
    }

    public function connection($connection) {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Proxy methods
     */
    public function getPrimaryKey()
    {
        return $this->wrappedObject->getPrimaryKey();
    }

    public function setAttribute($name, $value)
    {
        $this->wrappedObject->setAttribute($name, $value);
        return $this;
    }

    public function addAttribute($name, $value)
    {
        $this->wrappedObject->addAttribute($name, $value);
        return $this;
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
        if(is_array($value)) {
            $this->wrappedObject->setAttribute($offset, null);
            foreach($value as $key => $val) {
                $this->wrappedObject->addAttribute($offset, $val);
            }

            return null;
        }

        if(!is_string($value) && method_exists($value, 'getPrimaryKey')) {
            $value = $value->getPrimaryKey();
        }

        $this->wrappedObject->offsetSet($offset, $value);
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

    public function __set(string $name, mixed $value): void {
        $this->offsetSet($name, $value);
    }

}
