<?php

namespace Webleit\FixedLengthFile\Writer;

use Tightenco\Collect\Contracts\Support\Arrayable;
use Webleit\FixedLengthFile\Record;

/**
 * Class Writer
 * @package Webleit\FixedLengthFile
 */
abstract class Writer implements Arrayable
{
    /**
     * @var string
     */
    protected $file = '';

    /**
     * @var bool|resource
     */
    protected $resource;

    /**
     * @var \Tightenco\Collect\Support\Collection
     */
    protected $records;

    /**
     * @var string
     */
    protected $carriageReturn = "\r\n";

    /**
     * @var string
     */
    protected $emptyCharacter = " ";

    /**
     * Writer constructor.
     */
    public function __construct()
    {
        $this->records = collect([]);
    }

    /**
     * @param Record $record
     */
    public function addRecord(Record $record)
    {
        $this->records->push($record);
    }

    /**
     * @param $carriageReturn
     * @return $this
     */
    public function setCarriageReturn($carriageReturn)
    {
        $this->carriageReturn = $carriageReturn;

        return $this;
    }

    /**
     * @return string
     */
    public function getCarriageReturn()
    {
        return $this->carriageReturn;
    }

    /**
     * @param string $emptyCharacter
     */
    public function setEmptyCharacter($emptyCharacter)
    {
        $this->emptyCharacter = $emptyCharacter;
    }

    /**
     * @return string
     */
    public function getEmptyCharacter()
    {
        return $this->emptyCharacter;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->write();
    }

    /**
     * @param Record $record
     * @return string
     */
    public function getRecordContent(Record $record)
    {
        $row = '';
        $position = 0;

        $fields = $record->getStructure()->getFields();

        /**
         * @var $field RecordField
         */
        foreach ($fields as $key => $field) {
            $start = $field->getStart();
            $end = $field->end;

            if ($start > $position) {
                $row = str_pad($row, $start - $position, $this->getEmptyCharacter());
            }

            $position = $end;
            $value = $record->get($key, '');

            $row .= str_pad($value, $field->getLength(), $this->getEmptyCharacter());
        }

        $row .= $this->getCarriageReturn();

        return $row;
    }

    /**
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function recordsCount()
    {
        return $this->records->count();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->records->toArray();
    }

    /**
     * @param Record $record
     * @return $this
     */
    abstract public function writeRecord(Record $record);

    /**
     * Write the full document
     */
    public function write()
    {
        foreach ($this->records as $record) {
            $this->writeRecord($record);
        }

        return $this;
    }
}
