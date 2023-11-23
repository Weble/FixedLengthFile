<?php

namespace Webleit\FixedLengthFile;

use Illuminate\Support\Collection;
use Webleit\FixedLengthFile\Exception\WrongStart;

/**
 * Class Record
 * @package Webleit\FixedLengthFile
 */
class RecordStructure
{
    /**
     * RecordFields are sorted by start order
     * @var Collection[RecordField]
     */
    protected $fields;

    /**
     * Record constructor.
     */
    public function __construct()
    {
        $this->fields = collect([]);
    }

    /**
     * @param Field $field
     * @param int $start
     * @return $this
     * @throws WrongStart
     */
    public function addField(Field $field, $start = null)
    {
        if ($start === null && $this->fields->count() > 0) {
            $start = $this->fields->last()->end + 1;
        }

        $start = (int) $start;

        if ($start < 0) {
            throw new WrongStart("Start cannot be negative");
        }

        $recordField = new RecordField($field, $start);

        // Can the field fit in the already present structure?

        // If our start is before another field's end, it's overlapping.
        $overlappingFields = $this->fields
            ->where('end', '>=', $start)
            ->where('start', '<=', $recordField->end);

        if ($overlappingFields->count() > 0) {
            /** @var RecordField $overlappingField */
            $overlappingField = $overlappingFields->first();
            throw new WrongStart("This fields overlaps with [" . $overlappingField->getField()->name . "] at position [" . $overlappingField->getStart() . ' / ' . $overlappingField->end . "]");
        }

        $this->fields->put($field->name, $recordField);
        $this->reorderFields();

        return $this;
    }

    /**
     * @param $field
     * @return bool
     */
    public function hasField($field)
    {
        return $this->fields->has($field);
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return $this
     */
    public function reorderFields()
    {
        $this->fields->sortBy('start');
        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->fields->last()->end + 1;
    }

    /**
     * @return int
     */
    public function endsAt()
    {
        return $this->fields->last()->end;
    }
}
