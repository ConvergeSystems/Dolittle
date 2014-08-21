<?php
/**
 * Error wrapper for RexPro messages
 */

namespace Converge\Dolittle\Message\Body\Response;

/**
 * Error class to wrap error message from RexPro
 *
 * @package Dolittle
 */
class Error extends \Converge\Dolittle\Message\Body\Response
{
    /**
     * @var string $_error_message The text of the error message
     */
    private $_error_message;
    
    /**
     * @var array $_meta_attributes the valid meta attributes for this message
     */
    private $_meta_attributes = array(
        'flag'
    );
    
    /**
     * set the error message to save
     *
     * @param string $message the error message from the server
     * @uses $_error_message to store the message
     * @return $this
     */
    public function setErrorMessage($message)
    {
        return $this->_error_message = $message;
    }
    
    /**
     * returns the error message from the server
     *
     * @uses $_error_message to retrieve the previous error message
     * @return string the error message associated with the previous request
     */
    public function getErrorMessage()
    {
        return $this->_error_message;
    }
    
    /**
     * converts the error response into an associative array
     *
     * @uses getSession() to hydrate the array
     * @uses getRequest() to hydrate the array
     * @uses getMeta() to hydrate the array
     * @uses getErrorMessage() to hydrate the array
     * @return array the error response in array format
     */
    public function toArray()
    {
        $message = array();
        $message['session']       = $this->getSession();
        $message['request']       = $this->getRequest();
        $message['meta']          = $this->getMeta();
        $message['error_message'] = $this->getErrorMessage();
        
        return $message;
    }
    
    /**
     * hydrate this message with raw RexPro output
     *
     * @uses setSession() to hydrate this message
     * @uses setRequest() to hydrate this message
     * @uses setMeta() to hydrate this message
     * @uses setErrorMessage() to hydrate this message
     * @return $this
     */
    protected function hydrate($raw)
    {
        $this->setSession($raw[0]);
        $this->setRequest($raw[1]);
        $this->setMeta($raw[2]);
        $this->setErrorMessage($raw[3]);
        
        return $this;
    }
    
    /**
     * returns the valid meta attributes for this message
     *
     * @return array the valid meta attributes for this message
     */
    protected function getMetaAttributes()
    {
        return $this->_meta_attributes;
    }
}