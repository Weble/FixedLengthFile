<?php

namespace Webleit\FixedLengthFile\Test;

use PHPUnit\Framework\TestCase;

use Webleit\FixedLengthFile\Exception\ValueTooLong;
use Webleit\FixedLengthFile\Exception\WrongStart;
use Webleit\FixedLengthFile\Field;
use Webleit\FixedLengthFile\Record;
use Webleit\FixedLengthFile\RecordStructure;
use Webleit\FixedLengthFile\Document;

/**
 * Class ClassNameGeneratorTest
 * @package Webleit\ZohoBooksApi\Test
 */
class RecordTest extends TestCase
{
    /**
     * @test
     */
    public function can_populate_fields()
    {
        $structure = $this->getRecordStructure();

        $row = new Record($structure);
        $row->set('foo', '123');
        $row->set('bar', '345');

        $this->assertEquals([
            'foo' => '123',
            'bar' => '345'
        ], $row->toArray());
    }

    /**
     * @test
     */
    public function test_strict_mode()
    {
        $this->expectException(ValueTooLong::class);

        $structure = $this->getRecordStructure();

        $row = new Record($structure);
        $row->enableStrictMode();

        $row->set('foo', '123456789012345678901');
    }

    /**
     * @test
     */
    public function test_strict_mode_off()
    {
        $structure = $this->getRecordStructure();

        $row = new Record($structure);
        $row->disableStrictMode();

        $row->set('foo', '123456789012345678901');

        $this->assertEquals('12345678901234567890', $row->get('foo'));
    }

    /**
     * @return $this
     * @throws WrongStart
     */
    protected function getRecordStructure()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20), 40);

        return $structure;
    }
}
