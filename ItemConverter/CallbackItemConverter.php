<?php

namespace Ddeboer\DataImportBundle\ItemConverter;

use Ddeboer\DataImportBundle\ItemConverterInterface;

/**
 * Converts items using a callback
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class CallbackItemConverter implements ItemConverterInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Constructor
     *
     * @param callable $callback
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new \RuntimeException("$callback must be callable");
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function convert(array $input)
    {
        return call_user_func($this->callback, $input);
    }
}
