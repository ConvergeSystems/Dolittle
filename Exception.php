<?php
/**
 * Base Exception for Rexster Protocol package
 */

namespace Converge\Dolittle;

/**
 * Base exception for Dolittle
 *
 * @package Dolittle
 */
class Exception extends \Exception
{
    /**
     * Constructor for base exception
     *
     * @param string $message description of what went wrong
     * @param string $code 
     * @param \Exception $previous 
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
