<?php

namespace Ddeboer\DataImportBundle\Reader;

use Ddeboer\DataImportBundle\ReaderInterface;

/**
 * Reads a CSV file, using as little memory as possible
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class CsvReader implements ReaderInterface, \SeekableIterator
{
    /**
     * The field delimiter (one character only)
     *
     * @var string
     */
    private $delimiter = ';';

    /**
     * The field enclosure character (one character only)
     *
     * @var string
     */
    private $enclosure = '"';

    /**
     * The field escape character (one character only)
     *
     * @var string
     */
    private $escape    = '\\';

    /**
     * Number of the row that contains the column names
     *
     * @var int
     */
    protected $headerRowNumber;

    /**
     * CSV file
     *
     * @var \SplFileObject
     */
    protected $file;

    /**
     * Column headers as read from the CSV file
     *
     * @var array
     */
    protected $columnHeaders;

      /**
     * Construct CSV reader
     *
     * @param \SplFileObject $file  The CSV file
     */
    public function __construct(\SplFileObject $file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $this->setFile($file, $delimiter, $enclosure, $escape);
    }

    /**
     * Set file and prepare it for CSV reading
     *
     * @param \SplFileObject $file
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return CsvReader
     */
    public function setFile(\SplFileObject $file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        $this->file = $file;
        $this->file->setFlags(\SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD);
        $this->file->setCsvControl(
            $delimiter,
            $enclosure,
            $escape
        );

        return $this;
    }

    /**
     * Return the current row as an array
     *
     * If a header row has been set, an associative array will be returned
     *
     * @return array
     */
    public function current()
    {
        $line = $this->file->current();

        // If the CSV has column headers, use them to construct an associative
        // array for the columns in this line
        if (!empty($this->columnHeaders)) {
            // Count the number of elements in both: they must be equal.
            // If not, ignore the row
            if (count($this->columnHeaders) == count($line)) {
                return array_combine(array_values($this->columnHeaders), $line);
            }


        } else {
            // Else just return the column values
            return $line;
        }
    }

    /**
     * Get column headers
     *
     * @return array
     */
    public function getColumnHeaders()
    {
        return $this->columnHeaders;
    }

    /**
     * Set column headers
     *
     * @param array $columnHeaders
     * @return CsvReader
     */
    public function setColumnHeaders(array $columnHeaders)
    {
        $this->columnHeaders = $columnHeaders;
        return $this;
    }

    /**
     * Rewind the file pointer
     *
     * If a header row has been set, the pointer is set just below the header
     * row. That way, when you iterate over the rows, that header row is
     * skipped.
     *
     */
    public function rewind()
    {
        $this->file->rewind();
        if (null !== $this->headerRowNumber) {
            $this->file->seek($this->headerRowNumber + 1);
        }
    }

    /**
     * Set header row number
     *
     * @param int $rowNumber Number of the row that contains column header names
     * @return CsvReader
     */
    public function setHeaderRowNumber($rowNumber)
    {
        $this->headerRowNumber = $rowNumber;
        $this->columnHeaders = $this->readHeaderRow($rowNumber);
        return $this;
    }

    /**
     * Count number of rows in CSV
     *
     * @return int
     */
    public function count()
    {
        return $this->countRows();
    }

    /**
     * Count number of rows in CSV
     *
     * @return int
     */
    public function countRows()
    {
        $rows = 0;
        foreach ($this as $row) {
            $rows++;
        }
        return $rows;
    }

    public function next()
    {
        return $this->file->next();
    }

    public function valid()
    {
        return $this->file->valid();
    }

    public function key()
    {
        return $this->file->key();
    }

    public function seek($pointer)
    {
        $this->file->seek($pointer);
    }

    public function getFields()
    {
        return $this->columnHeaders;
    }

    /**
     * Get a row
     *
     * @param int $number   Row number
     * @return array
     */
    public function getRow($number)
    {
        $this->seek($number);
        return $this->current();
    }

    /**
     * Read header row from CSV file
     *
     * @param \SplFileObject
     * @return array        Column headers
     */
    protected function readHeaderRow($rowNumber)
    {
        $this->file->seek($rowNumber);
        $headers = $this->file->current();
        return $headers;
    }
}