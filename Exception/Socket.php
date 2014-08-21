<?php
/**
 * Exceptions for Socket Connections
 */

namespace Converge\Dolittle\Exception;

/**
 * Class to wrap Socket exceptions for Dolittle
 *
 * @package Dolittle
 */
class Socket extends \Converge\Dolittle\Exception
{
    /**
     * Constructor for Socket connection
     *
     * @param string $message Description of what has gone wrong
     * @param string $code Error Code
     * @param \Exception $previous Previous Exception
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
