<?php

namespace Webleit\FixedLengthFile\Test;

use PHPUnit\Framework\TestCase;

use Webleit\FixedLengthFile\Exception\WrongStart;
use Webleit\FixedLengthFile\Field;
use Webleit\FixedLengthFile\RecordStructure;
use Webleit\FixedLengthFile\Document;

/**
 * Class ClassNameGeneratorTest
 * @package Webleit\ZohoBooksApi\Test
 */
class RecordStructureTest extends TestCase
{
    /**
     * @test
     */
    public function cannot_insert_overlapping_fields ()
    {
        $this->expectException(WrongStart::class);

        $record = (new RecordStructure())
            ->addField(new Field('Id_tracciato', 20))
            ->addField(new Field('Id_tracciato', 20), 10);
    }

    /**
     * @test
     */
    public function cannot_insert_with_negative_start ()
    {
        $this->expectException(WrongStart::class);

        $record = (new RecordStructure())
            ->addField(new Field('foo', 21), -1);
    }

    /**
     * @test
     */
    public function can_insert_not_overlapping_fields ()
    {
        $record = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20), 21);

        $this->assertEquals(2, $record->getFields()->count());
    }

    /**
     * @test
     */
    public function can_insert_subsequent_fields ()
    {
        $record = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20));

        $this->assertEquals(2, $record->getFields()->count());
        $this->assertEquals(20, $record->getFields()->last()->getStart());
    }

    /**
     * @test
     */
    public function can_insert_spaced_fields ()
    {
        $record = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20), 40);

        $this->assertEquals(2, $record->getFields()->count());
        $this->assertEquals(40, $record->getFields()->last()->getStart());
    }

    /**
     * @test
     */
    public function calculates_end_correctly ()
    {
        $record = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20), 40);

        $this->assertEquals(59, $record->endsAt());
    }

    /**
     * @test
     */
    public function calculates_length_correctly ()
    {
        $record = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20), 40);

        $this->assertEquals(60, $record->getLength());
    }
}