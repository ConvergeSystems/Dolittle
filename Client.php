<?php
/**
 * Client class for Dolittle package.
 */

namespace Converge\Dolittle;

/**
 * Class used to connect to Rexster server and parse messages.
 *
 * This class is used to make a connection to the Rexster server, send messages,
 * and parse responses. Reponses are converted to objects.
 *
 * @package Dolittle
 */
class Client
{
    /**
     * Socket connection to Rexster.
     *
     * @var resource $_socket stream resource for rexster connection
     */
    private $_socket;
    
    /**
     * The URI of the Rexster Server
     *
     * @var string $_host_uri URI of the rexster server
     */
    private $_host_uri;
    
    /**
     * Constructor for \Converge\Dolittle\Client
     *
     * @param string $host_uri Fully qualified URI for Rexster server
     */
    public function __construct($host_uri)
    {
        $this->_host_uri = $host_uri;
    }
    
    /**
     * Wrapper function for sending a query to the Rexster server.
     *
     * This will create a \Converge\Dolittle\Message\Body\Request\Script object
     * with the passed params, wrap that in a Rexster protocol message, send it
     * to the server, then return the response.
     *
     * @param string $query script to be run by Rexster (a query)
     * @param string $graph_name name of the graph against which to run the 
     *    script
     * @param array $bindings if the script uses bound parameters, those
     *    bindings should be set here.
     * @uses Message to wrap the mssage
     * @uses Message::setMessageBody() to hold the contents of the request
     * @return \Converge\Dolittle\Message The response message from the Rexster
     *    protocol. This will be an Error, Script, or Session.
     */
    public function executeScript($query, $graph_name, array $bindings = array())
    {
        $script = new Message\Body\Request\Script;
        $script->setScript($query);
        $script->setBindings($bindings);
        $script->setMeta(array('graphName' => $graph_name));
        
        $message = new Message;
        $message->setMessageBody($script);
        
        $this->send($message);
        
        return $this->getResponse();
    }
    
    /**
     * Return the response of the most recent message sent to Rexster
     *
     * @return \Converge\Dolittle\Message The response message from the Rexster
     *    protocol. This will be an Error, Script, or Session.
     */
    public function getResponse()
    {
        $message = new Message;
        $stream = stream_get_contents($this->_socket, 11);
        $message->setProtocolVersion((int)hexdec(bin2hex(substr($stream, 0, 1))));
        $message->setSerializerType((int)hexdec(bin2hex(substr($stream, 1, 1))));
        $message->setReserved((int)hexdec(bin2hex(substr($stream, 2, 4))));
        $message->setMessageType((int)hexdec(bin2hex(substr($stream, 6, 1))));
        $message->setMessageSize((int)hexdec(bin2hex(substr($stream, 7, 4))));
        $message->setMessageBodySerialized(stream_get_contents($this->_socket, $message->getMessageSize(), 11));
        $message->unpack();
        
        $this->destroySocket();
        
        return $message;
    }
    
    /**
     * Sends a message to Rexter via the socket connection.
     *
     * @param Message $message A \Converge\Dolittle\Message containing the request
     *    to be sent to the server.
     * @throws Exception\Socket there was a problem sending the request.
     * @return boolean Whether the message was successfully sent. Does not imply
     *    a successful query, only reciept by the server.
     */
    public function send(Message $message)
    {
        $this->connectSocket();
        $packed = $message->pack();

        $write = @fwrite($this->_socket, $packed);

        if($write === false)
        {
            throw new Exception\Socket('Dolittle was not able to send your request to the server.');
        }
        
        return true;
    }
    
    /**
     * Opens socket connection to the Rexster server
     * 
     * @param string $host_uri URI to the server to which we are connecting
     * @throws Exception\Socket could not connect the socket.
     * @return bool true on success false on error
     */
    private function connectSocket()
    {
        $this->_socket = stream_socket_client(
            $this->_host_uri,
            $errno, 
            $errorMessage,
            ini_get("default_socket_timeout")
        );
        
        if(!$this->_socket)
        {
            throw new Exception\Socket($errorMessage, $errno);
        }
        
        return true;
    }
    
    /**
     * Make sure the session is closed on destruction of the object
     * 
     * @return boolean were we successfully disconnected?
     */
    public function __destruct()
    {
        $this->destroySocket();
        
        return true;
    }
    
    /**
     * Destroy the socket connection if it exists
     *
     * @return bool
     */
    private function destroySocket()
    {
        if($this->_socket !== null)
        {
            stream_socket_shutdown($this->_socket, STREAM_SHUT_RDWR);
            $this->_socket = null;
        }
        
        return true;
    }
}
