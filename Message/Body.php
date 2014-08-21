<?php
/**
 * Abstract class for Dolittle Message Body
 */

namespace Converge\Dolittle\Message;

/**
 * Abstract class for Dolittle Message Bodies
 *
 * This class contains the properties and method common to all request and 
 * responses for RexPro.
 *
 * @package Dolittle
 */
abstract class Body
{
    /**
     * @var string $_session the session_id for RexPro
     */
    protected $_session;
    
    /**
     * @var string $_request the request_id for this RexPro message
     */
    protected $_request;
    
    /**
     * @var array $_meta an array of the metadata associated with this message
     */
    protected $_meta;
    
    /**
     * Set the session id for this message.
     *
     * @param string $session the id of the users current session
     * @uses $_session to store the session id
     * @return $this
     */
    public function setSession($session)
    {
        $this->_session = $session;
        
        return $this;
    }
    
    /**
     * Set the request id for this message.
     *
     * @param string $request the id of the users current session
     * @uses $_request to store the request id
     * @return $this
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        
        return $this;
    }
    
    /**
     * Set the meta data for the message
     *
     * Checks the meta data keys against the messages allowed keys, then if they
     * are valid, sets them.
     *
     * @param array $meta
     * @uses getMetaAttributes() to determine valid meta data.
     * @uses $_meta to store the meta data
     * @throws \InvalidArgumentException
     * @return $this
     * @todo Make meta actual classes so I can validate types.
     */
    public function setMeta(array $meta)
    {
        $invalid_keys = array();
        
        foreach ($meta as $attribute => $value) {
            if (!in_array($attribute, $this->getMetaAttributes())) {
                $invalid_keys[] = $attribute;
            }
        }
        
        if (count($invalid_keys)) {
            throw new \InvalidArgumentException(
                sprintf('%s doesn\'t accept the meta data %s',
                get_called_class(),
                implode(', ', $invalid_keys)
            ));
        }
        
        $this->_meta = $meta;
        
        return $this;
    }
    
    /**
     * Get the session id associated with this message
     *
     * @uses $_session to determine the session id
     * @return string the session_id for this message
     */
    public function getSession()
    {
        return $this->_session;
    }
    
    /**
     * Get the request id associated with this message
     *
     * @uses $_request to determine the request id 
     * @return string the request_id for this message
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Get the meta data associated with this message
     *
     * @uses $_meta to determine the meta data for this message
     * @return array the metadata for this array
     */
    public function getMeta()
    {
        return $this->_meta;
    }
    
    /**
     * require concrete classes to implement a method returning valid meta
     *
     * @return array the valid meta attributes for this message
     */
    abstract protected function getMetaAttributes();
}
