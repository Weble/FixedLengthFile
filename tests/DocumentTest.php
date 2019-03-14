<?php

namespace Webleit\FixedLengthFile\Test;

use PHPUnit\Framework\TestCase;

use Webleit\FixedLengthFile\Exception\ValueTooLong;
use Webleit\FixedLengthFile\Exception\WrongStart;
use Webleit\FixedLengthFile\Field;
use Webleit\FixedLengthFile\Record;
use Webleit\FixedLengthFile\RecordStructure;
use Webleit\FixedLengthFile\Document;
use Webleit\FixedLengthFile\Writer\MemoryWriter;

/**
 * Class ClassNameGeneratorTest
 * @package Webleit\ZohoBooksApi\Test
 */
class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function full_test()
    {
        $writer = new MemoryWriter();
        $writer->setCarriageReturn("\r\n");

        $record = new Record($this->getHeaderRecordStructure());
        $record
            ->set('id_tracciato', 'ANAGRCARATTERISTICHE')
            ->set('sigla_metel', 'IES')
            ->set('data_decorrenza', '20180824')
            ->set('data_variazione', '20180824');

        $writer->addRecord($record);

        $record = new Record($this->getDetailRecordStructure());
        $record
            ->set('azienda', 'IES')
            ->set('marchio', 'ONL')
            ->set('id_caratteristica', 'MEDEARN0001')
            ->set('standard', 'METEL')
            ->set('lingua', 'ITA')
            ->set('descrizione', 'DESCRIZIONE ESTESA')
            ->set('qualificatore', 'V')
            ->set('data', '20180824');

        $writer->addRecord($record);

        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . '/data/IESEAC.txt'),
            (string) $writer
        );
    }

    /**
     * @test
     */
    public function full_test_product()
    {
        $writer = new MemoryWriter();
        $writer->setCarriageReturn("\r\n");

        $record = new Record($this->getHeaderRecordStructure());
        $record
            ->set('id_tracciato', 'CARATTERISTAPRODOTTO')
            ->set('sigla_metel', 'IES')
            ->set('data_decorrenza', '20180824')
            ->set('data_variazione', '20180824');

        $writer->addRecord($record);

        $record = new Record($this->getDetailRecordStructureProduct());
        $record
            ->set('azienda', 'IES')
            ->set('marchio', 'ONL')
            ->set('codice_prodotto', 'VIDOMO')
            ->set('lingua', 'ITA')
            ->set('id_caratteristica', 'MEDEARN0001')
            ->set('valore', 'Lorem Ipsum')
            ->set('standard', 'METEL')
            ->set('data', '20180824');

        $writer->addRecord($record);

        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . '/data/IESECP.txt'),
            (string) $writer
        );
    }

    /**
     * @test
     */
    public function full_test_img()
    {
        $writer = new MemoryWriter();
        $writer->setCarriageReturn("\r\n");

        $record = new Record($this->getHeaderRecordStructureImg());
        $record
            ->set('id_tracciato', 'IMMAGINE PRODOTTO')
            ->set('sigla_metel', 'IES')
            ->set('piva', '03579410246')
            ->set('data_decorrenza', '20180824')
            ->set('data_variazione', '20180824')
            ->set('descrizione', 'Immagini Prodotto');

        $writer->addRecord($record);

        $record = new Record($this->getDetailRecordStructureImg());
        $record
            ->set('marchio', 'ONL')
            ->set('codice_prodotto', 'VIDOMO')
            ->set('tipo_link', 'I')
            ->set('lingua', 'IT')
            ->set('link', 'http://www.google.it')
            ->set('sequenza', '1');

        $writer->addRecord($record);

        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . '/data/IESIMG.txt'),
            (string) $writer
        );
    }

    /**
     * @test
     */
    public function test_tostring()
    {
        $writer = new MemoryWriter();
        $record = new Record($this->getTestRecordStructure());
        $record->set('foo', '12345');
        $record->set('bar', '1234567890');

        $writer->addRecord($record);

        $this->assertEquals(40 + strlen($writer->getCarriageReturn()), strlen((string) $writer));

        $record = new Record($this->getTestRecordStructure());
        $record->set('foo', '12345');
        $record->set('bar', '1234567890');

        $writer->addRecord($record);

        $this->assertEquals(80 + ($writer->recordsCount() * strlen($writer->getCarriageReturn())), strlen((string) $writer));
    }

    /**
     * @return RecordStructure
     * @throws WrongStart
     */
    protected function getHeaderRecordStructure()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('id_tracciato', 20))
            ->addField(new Field('sigla_metel', 3))
            ->addField(new Field('numero_tracciato', 11))
            ->addField(new Field('data_decorrenza', 8))
            ->addField(new Field('data_variazione', 8))
            ->addField(new Field('descrizione', 30))
            ->addField(new Field('versione', 3));

        return $structure;
    }

    /**
     * @return RecordStructure
     * @throws WrongStart
     */
    protected function getHeaderRecordStructureImg()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('id_tracciato', 20))
            ->addField(new Field('sigla_metel', 3))
            ->addField(new Field('piva', 11))
            ->addField(new Field('listino', 6))
            ->addField(new Field('data_decorrenza', 8))
            ->addField(new Field('data_variazione', 8))
            ->addField(new Field('descrizione', 30))
            ->addField(new Field('filter', 39))
            ->addField(new Field('versione', 3));

        return $structure;
    }

    /**
     * @return RecordStructure
     * @throws WrongStart
     */
    protected function getDetailRecordStructureProduct()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('azienda', 3))
            ->addField(new Field('marchio', 3))
            ->addField(new Field('codice_prodotto', 16))
            ->addField(new Field('lingua', 3))
            ->addField(new Field('id_caratteristica', 30))
            ->addField(new Field('valore', 100))
            ->addField(new Field('standard', 30))
            ->addField(new Field('sequenza', 6))
            ->addField(new Field('qualificatore', 6))
            ->addField(new Field('data', 8))
            ->addField(new Field('extra', 1000));

        return $structure;
    }

    /**
     * @return RecordStructure
     * @throws WrongStart
     */
    protected function getDetailRecordStructure()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('azienda', 3))
            ->addField(new Field('marchio', 3))
            ->addField(new Field('id_caratteristica', 30))
            ->addField(new Field('standard', 30))
            ->addField(new Field('lingua', 3))
            ->addField(new Field('descrizione', 100))
            ->addField(new Field('qualificatore', 6))
            ->addField(new Field('data', 8))
            ->addField(new Field('codice_um', 10))
            ->addField(new Field('nome_um', 20))
            ->addField(new Field('formato', 20))
            ->addField(new Field('default', 100))
            ->addField(new Field('ricercabile', 1))
            ->addField(new Field('descrizone', 1000));

        return $structure;
    }

    /**
     * @return RecordStructure
     * @throws WrongStart
     */
    protected function getDetailRecordStructureImg()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('marchio', 3))
            ->addField(new Field('codice_prodotto', 16))
            ->addField(new Field('tipo_link', 1))
            ->addField(new Field('lingua', 2))
            ->addField(new Field('link', 512))
            ->addField(new Field('sequenza', 6))
            ->addField(new Field('scopo', 30))
            ->addField(new Field('titolo', 70));

        return $structure;
    }

    /**
     * @return $this
     * @throws WrongStart
     */
    protected function getTestRecordStructure()
    {
        $structure = (new RecordStructure())
            ->addField(new Field('foo', 20))
            ->addField(new Field('bar', 20));

        return $structure;
    }
}
