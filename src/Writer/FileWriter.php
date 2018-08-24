<?php

namespace Webleit\FixedLengthFile\Writer;

/**
 * Class Writer
 * @package Webleit\FixedLengthFile
 */
class FileWriter extends Writer
{
    /**
     * @var string
     */
    protected $file = '';

    /**
     * Writer constructor.
     */
    public function __construct ($file)
    {
        parent::__construct();

        $this->file = $file;

        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @param Record $record
     * @return $this|void
     */
    public function writeRecord (Record $record)
    {
        $resource = fopen($this->file, 'a');
        fwrite($resource, $this->getRecordContent($record));
        fclose($resource);
    }

    /**
     * @return $this|void
     */
    public function write ()
    {
        $resource = fopen($this->file, 'w');
        fwrite($resource, parent::write());
        fclose($resource);
    }
}