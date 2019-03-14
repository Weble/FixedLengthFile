<?php

namespace Webleit\FixedLengthFile\Writer;

use Webleit\FixedLengthFile\Record;

/**
 * Class Writer
 * @package Webleit\FixedLengthFile
 */
class MemoryWriter extends Writer
{
    /**
     * @var string
     */
    protected $string = '';

    /**
     * @param Record $record
     * @return $this|mixed
     */
    public function writeRecord(Record $record)
    {
        $this->string .= $this->getRecordContent($record);

        return $this;
    }

    /**
     * @return $this
     */
    public function write()
    {
        $this->string = '';

        return parent::write();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->write();

        return $this->string;
    }
}
