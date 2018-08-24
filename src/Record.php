<?php

namespace Webleit\FixedLengthFile;

use Tightenco\Collect\Contracts\Support\Arrayable;
use Tightenco\Collect\Support\Collection;
use Webleit\FixedLengthFile\Exception\FieldNotPresentException;
use Webleit\FixedLengthFile\Exception\ValueTooLong;

/**
 * Class Record
 * @package Webleit\FixedLengthFile
 */
class Record implements Arrayable, \ArrayAccess
{
    /**
     * @var RecordStructure
     */
    protected $structure;

    /**
     * @var Collection
     */
    protected $values;

    /**
     * @var bool
     */
    protected $strict = false;

    /**
     * Record constructor.
     * @param RecordStructure $structure
     */
    public function __construct (RecordStructure $structure)
    {
        $this->structure = $structure;
        $this->values = collect([]);
    }

    /**
     * @return $this
     */
    public function enableStrictMode ()
    {
        $this->strict = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableStrictMode ()
    {
        $this->strict = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStrictModeEnabled ()
    {
        return $this->strict;
    }


    /**
     * @param $key
     * @param $value
     * @return $this
     * @throws FieldNotPresentException
     * @throws ValueTooLong
     */
    public function set ($key, $value)
    {
        if (!$this->structure->hasField($key)) {
            throw new FieldNotPresentException($key);
        }

        /** @var RecordField $field */
        $field = $this->structure->getFields()->get($key);
        $length = $field->getField()->getLength();

        // Strict mode => forbid
        if ($this->strict && strlen($value) > $length) {
            throw new ValueTooLong("Field [". $key ."] cannot be longer than " . $length);
        }

        // Non strict mode => truncate
        $value = substr($value, 0, $length);
        $this->values->put($key, $value);

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->values->get($key, $default);
    }

    /**
     * @return RecordStructure
     */
    public function getStructure ()
    {
        return $this->structure;
    }

    /**
     * @param $name
     * @param $value
     * @return Record
     * @throws FieldNotPresentException
     * @throws ValueTooLong
     */
    public function __set ($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get ($name)
    {
        return $this->get($name);
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return $this->values->toArray();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists ($offset)
    {
        return $this->values->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet ($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void|Record
     * @throws FieldNotPresentException
     * @throws ValueTooLong
     */
    public function offsetSet ($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset ($offset)
    {
        return $this->values->offsetUnset($offset);
    }

}