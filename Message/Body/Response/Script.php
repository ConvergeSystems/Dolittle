<?php
/**
 * Wrap the response for Script requests in an object
 */

namespace Converge\Dolittle\Message\Body\Response;

/**
 * A wrapper class for script response messages from RexPro
 *
 * @package Dolittle
 */
class Script extends \Converge\Dolittle\Message\Body\Response
{
    /**
     * @var array an array of results from the script
     */
    private $_results;
    
    /**
     * @var array an array of bound params for the script
     */
    private $_bindings;
    
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
     * Sets the results array from the server
     *
     * @param array $results the results from the server
     * @uses $_results to store the results.
     * @return $this
     */
    public function setResults(array $results)
    {
        $this->_results = $results;
        
        return $this;
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
     * Return an array of the results of the script
     *
     * @uses $_results to determine the results
     * @return array an array of results from the previous script request
     */
    public function getResults()
    {
        return $this->_results;
    }
    
    /**
     * Serialize the object into an array
     *
     * @uses getBindings() to hydrate the array
     * @uses getMeta() to hydrate the array
     * @uses getSession() to hydrate the array
     * @uses getRequest() to hydrate the array
     * @uses getResults() to hydrate the script array
     * @return array the message body converted into an array
     */
    public function toArray()
    {
        $message = array();
        $message['session']  = $this->getSession();
        $message['request']  = $this->getRequest();
        $message['meta']     = $this->getMeta();
        $message['results']  = $this->getResults();
        $message['bindings'] = $this->getBindings();
        
        return $message;
    }
    
    /**
     * Serialize the object into an array
     *
     * @uses setBindings() to hydrate the script response
     * @uses setMeta() to hydrate the script response
     * @uses setSession() to hydrate the script response
     * @uses setRequest() to hydrate the script response
     * @uses setResults() to hydrate the script response
     * @return array the message body converted into an array
     */
    protected function hydrate($raw)
    {
        $this->setSession($raw[0]);
        $this->setRequest($raw[1]);
        $this->setMeta($raw[2]);
        $this->setResults((is_array($raw[3]) ? $raw[3] : array()));
        $this->setBindings($raw[4]);
        
        return $this;
    }
    
    /**
     * returns an array of the valid meta attributes
     *
     * @return array an array of the valid meta attributes
     */
    protected function getMetaAttributes()
    {
        return array();
    }
}
