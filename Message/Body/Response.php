<?php
/**
 * The base of a response message body
 */

namespace Converge\Dolittle\Message\Body;

/**
 * The Response body abstract class
 *
 * @package Dolittle
 */
abstract class Response extends \Converge\Dolittle\Message\Body
{
    /**
     * constructor which takes raw data with which to hydrate a message body
     *
     * @param string $raw array from RexPro with which to hydrate the Response
     * @uses hydrate() to hydrate the message
     * @return $this
     */
    public function __construct($raw = false)
    {
        if($raw){
            $this->hydrate($raw);
        }
        
        return $this;
    }
    
    /**
     * Serialize the object into an array
     *
     * @return array the message body converted into an array
     */
    abstract public function toArray();
    
    /**
     * hydrate the object from the raw RexPro output
     *
     * @param array $raw 
     * @return $this
     */
    abstract protected function hydrate($raw);
}
