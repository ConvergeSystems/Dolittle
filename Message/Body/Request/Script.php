<?php
/**
 * A Script request body for RexPro
 */

namespace Converge\Dolittle\Message\Body\Request;

/**
 * A class to wrap the Script Request message type for sending to RexPro
 *
 * @package Dolittle
 */
class Script extends \Converge\Dolittle\Message\Body\Request
{
    /**
     * @var string $_language_name the name of the language this script is using
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#script
     */
    private $_language_name = 'groovy';
    
    /**
     * @var string $_script the query or script you want to run on Rexster
     */
    private $_script;
    
    /**
     * @var array $_bindings bound params to use with your query
     */
    private $_bindings;
    
    /**
     * @var array $_meta_attributes the valid attributes for this message type
     */
    private $_meta_attributes = array(
        'inSession',
        'isolate',
        'transaction',
        'graphName',
        'graphObjName',
        'console'
    );
    
    /**
     * set the language name you are using with this script
     *
     * @param string $language_name set the name of the language for this script
     * @uses $_language_name to save the name of the language for this script
     * @throws \InvalidArgumentException if the language_name isn't a string
     * @return $this
     */
    public function setLanguageName($language_name)
    {
        if (is_string($language_name)) {
            $this->_language_name = $language_name;
            
            return $this;
        }
        
        throw new \InvalidArgumentException(sprintf('LanguageName must be a string. %s given.', gettype($language_name)));
    }
    
    /**
     * Sets the script/query to run against the graph
     *
     * @param string $script the script/query to run
     * @uses $_script to store the script
     * @throws \InvalidArgumentException if the $script passed isn't a string
     * @return $this
     */
    public function setScript($script)
    {
        if (is_string($script)) {
            $this->_script = $script;
            
            return $this;
        }
        
        throw new \InvalidArgumentException(sprintf('Script must be a string. %s given.', gettype($script)));
    }
    
    /**
     * Sets the bound params to use for this script
     *
     * @param array $bindings
     * @uses $_bindings to store the bound params
     * @return $this
     */
    public function setBindings(array $bindings)
    {
        $this->_bindings = $bindings;
        
        return $this;
    }
    
    /**
     * returns the name of the language used for this script
     *
     * @uses $_language_name to determine the language name.
     * @return string the name of the language used for this script
     */
    public function getLanguageName()
    {
        return $this->_language_name;
    }
    
    /**
     * returns the script being run against the graph server
     *
     * @uses $_script to determine the script being run
     * @return string the script to run agaisnt the Rexster service
     */
    public function getScript()
    {
        return $this->_script;
    }
    
    /**
     * returns the bound params used in this script
     *
     * @uses $_bindings to determine the bound params
     * @return array the params bound to this script
     */
    public function getBindings()
    {
        return $this->_bindings;
    }
    
    /**
     * Serialize the object into an array
     *
     * @uses getBindings() to hydrate the array
     * @uses getMeta() to hydrate the array
     * @uses getSession() to hydrate the array
     * @uses getRequest() to hydrate the array
     * @uses getLanguageName() to hydrate the array
     * @uses getScript() to hydrate the array
     * @return array the message body converted into an array
     */
    public function toArray()
    {
        $bindings = $this->getBindings();
        $meta     = $this->getMeta();
        
        $message = array(
            $this->getSession(),
            $this->getRequest(),
            ((is_array($meta) && !empty($meta)) ? $meta : new \stdClass),
            $this->getLanguageName(),
            $this->getScript(),
            ((is_array($bindings) && !empty($bindings)) ? $bindings : new \stdClass)
        );
        
        return $message;
    }
    
    /**
     * returns an array of the valid meta attributes
     *
     * @uses $_meta_attributes to determine the attributes
     * @return array an array of the valid meta attributes
     */
    protected function getMetaAttributes()
    {
        return $this->_meta_attributes;
    }
}