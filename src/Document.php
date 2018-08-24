<?php

namespace Webleit\FixedLengthFile;
use Tightenco\Collect\Contracts\Support\Arrayable;

/**
 * Class Writer
 * @package Webleit\FixedLengthFile
 */
class Document implements Arrayable
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
    protected $carriageReturn = "\n";

    /**
     * @var string
     */
    protected $emptyCharacter = " ";

    /**
     * Writer constructor.
     */
    public function __construct ()
    {
        $this->records = collect([]);
    }

    /**
     * @param Record $record
     */
    public function addRecord (Record $record)
    {
        $this->records->push($record);
    }

    /**
     * @param string $carriageReturn
     */
    public function setCarriageReturn ($carriageReturn)
    {
        $this->carriageReturn = $carriageReturn;
    }

    /**
     * @return string
     */
    public function getCarriageReturn ()
    {
        return $this->carriageReturn;
    }

    /**
     * @param string $emptyCharacter
     */
    public function setEmptyCharacter ($emptyCharacter)
    {
        $this->emptyCharacter = $emptyCharacter;
    }

    /**
     * @return string
     */
    public function getEmptyCharacter ()
    {
        return $this->emptyCharacter;
    }

    /**
     * @return string
     */
    public function __toString ()
    {
        $string = "";
        $firstLine = true;

        /** @var Record $record */
        foreach ($this->records as $record) {

            if (!$firstLine) {
                $string .= $this->getCarriageReturn();
            }

            $firstLine = false;
            $fields = $record->getStructure()->getFields();
            $position = 0;

            /**
             * @var $field RecordField
             */
            foreach ($fields as $key => $field) {
                $start = $field->getStart();
                $end = $field->end;

                if ($start > $position) {
                    $string = str_pad($string, $start - $position, $this->getEmptyCharacter());
                }

                $value = $record->get($key, '');
                $string .= str_pad($value, $field->getLength(), $this->getEmptyCharacter());

                $position = $end;
            }
        }

        return $string;
    }

    /**
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getRecords ()
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function recordsCount ()
    {
        return $this->records->count();
    }

    /**
     * @param $file
     */
    public function write($file)
    {
        $file = fopen($file, 'w');
        fwrite($file, (string) $this);
        fclose($file);
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return $this->records->toArray();
    }
}