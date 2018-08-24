<?php

namespace Webleit\FixedLengthFile;


/**
 * Class RecordField
 * @package Webleit\FixedLengthFile
 */
class RecordField
{
    /**
     * @var int
     */
    protected $start = 0;

    /**
     * @var Field
     */
    protected $field;

    /**
     * @var int
     */
    public $end;

    /**
     * RecordField constructor.
     * @param Field $field
     * @param int $start
     */
    public function __construct (Field $field, $start = 0)
    {
        $this->start = $start;
        $this->field = $field;
        $this->end = $start + $field->getLength() - 1;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->getField()->getLength();
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return Field
     */
    public function getField ()
    {
        return $this->field;
    }
}