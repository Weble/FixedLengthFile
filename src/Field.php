<?php

namespace Webleit\FixedLengthFile;

/**
 * Class Field
 * @package Webleit\FixedLengthFile
 */
class Field
{
    /**
     * @var int
     */
    protected $length = 1;

    /**
     * @var string
     */
    public $name = '';

    /**
     * Field constructor.
     * @param string $name
     * @param $length
     */
    public function __construct($name, $length)
    {
        $this->name = $name;
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
