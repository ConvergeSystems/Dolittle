<?php
/**
 * The base of a request message body
 */

namespace Converge\Dolittle\Message\Body;

/**
 * The Response body abstract class
 *
 * @package Dolittle
 */
abstract class Request extends \Converge\Dolittle\Message\Body
{
    /**
     * @var string $_default_uuid a basic uuid for sessions and requests
     */
    protected $_default_uuid = '00000000-0000-0000-0000-000000000000';
    
    /**
     * Build the Request object
     *
     * @todo allow passing of $session so as to associate this with a session
     * @uses $_default_uuid to set the session id.
     * @uses setSession() to save the session id.
     * @uses setRequest() to save the request id.
     * @uses createUuid() generate a new uuid for the request
     * @return $this
     */
    public function __construct()
    {
        $this->setSession($this->_default_uuid);
        $this->setRequest($this->createUuid());
        
        return $this;
    }
    
    /**
     * Generates a unique id to associate with a request
     *
     * @return string unique uuid to set to the request id
     */
    private function createUuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
    
    /**
     * Serialize the object into an array
     *
     * @return array the message body converted into an array
     */
    abstract public function toArray();
}
