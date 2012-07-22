<?php

namespace Ddeboer\DataImportBundle\Writer;

use Ddeboer\DataImportBundle\WriterInterface;

/**
 * Persists data in a storage medium, such as a database, XML file, etc.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * Prepare the writer before writing the items
     *
     * This template method can be overridden in concrete writer
     * implementations.
     *
     * @return Writer
     */
    public function prepare() {}

    /**
     * Wrap up the writer after all items have been written
     *
     * This template method can be overridden in concrete writer
     * implementations.
     *
     * @return Writer
     */
    public function finish() {}
}
