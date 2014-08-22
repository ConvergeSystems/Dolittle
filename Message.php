<?php
/**
 * Message Class for Dolittle package
 */

namespace Converge\Dolittle;

/**
 * Wrapper class for OOP interface to RexPro
 *
 * Wraps the RexPro Messages in a reusable PHP Object
 *
 * @todo implement MsgPack serializer type
 * @package Dolittle
 */
class Message
{
    /**
     * A class constant defining the JSON serializer identifier
     *
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#basic-message-structure
     */
    const SERIALIZER_TYPE_JSON = 1;
    
    /**
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#basic-message-structure RexPro Docs
     * @var int $_protocol_version The RexPro version to use.
     * @used-by setProtocolVersion()
     */
    private $_protocol_version = 1;
    
    /**
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#basic-message-structure
     * @var int $_serializer_type The type of serializer used for messages to the Rexster server
     */
    private $_serializer_type  = self::SERIALIZER_TYPE_JSON;
    
    /**
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#basic-message-structure
     * @var array $_reserved 4 empty bits, reserved for later versions of the
     *    protocol.
     */
    private $_reserved = array(0,0,0,0);
    
    /**
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#message-definitions
     * @var int $_message_type id of RexPro message type
     */
    private $_message_type;
    
    /**
     * @var int $_message_size length of the serialized message body
     */
    private $_message_size;
    
    /**
     * @var Message\Body $_message_body the main contents of the request
     */
    private $_message_body;
    
    /**
     * @var string $_message_body_serialized the serialized message body
     */
    private $_message_body_serialized;
    
    /**
     * @link https://github.com/tinkerpop/rexster/wiki/RexPro-Messages#message-definitions
     * @var array $_message_types a map of message type ids to namespaces
     */
    private $_message_types = array(
        1 => 'Converge\Dolittle\Message\Body\Request\Session',
        2 => 'Converge\Dolittle\Message\Body\Response\Session',
        3 => 'Converge\Dolittle\Message\Body\Request\Script',
        5 => 'Converge\Dolittle\Message\Body\Response\Script',
        0 => 'Converge\Dolittle\Message\Body\Response\Error'
    );
    
    /**
     * @var array $_serializer_types the possible serializer types
     */
    private $_serializer_types = array(
        self::SERIALIZER_TYPE_JSON
    );
    
    /**
     * Sets the version of the Rexster Protocol to use for this message.
     *
     * @uses $_protocol_version to set the version
     * @param int $version 
     * @throws Exception if wrong type is passed.
     * @return $this
     */
    public function setProtocolVersion($version)
    {
        if (is_int($version)) {
            $this->_protocol_version = $version;
            
            return $this;
        }
        
        throw new Exception(
            sprintf(
                'Dolittle::setProtocolVersion expects an integer. %s given',
                gettype($version)
            )
        );
    }
    
    /**
     * Returns the version of the Rexster protocol to use for this message
     *
     * @uses Message::$_protocol_version to return the version
     * @return int Rexster Protocol version
     */
    public function getProtocolVersion()
    {
        return $this->_protocol_version;
    }
    
    /**
     * Sets the serializer type for the message if the passed type is valid
     *
     * @uses Message::$_serializer_types to validate the type
     * @uses Message::$_serializer_type to store the serializer type
     * @param int $type 
     * @throws Exception if Type is invalid
     * @return $this
     */
    public function setSerializerType($type)
    {
        if (in_array($type, $this->_serializer_types)) {
            $this->_serializer_type = $type;
    
            return $this;
        }
        
        throw new Exception(sprintf('%s is not a valid type', $type));
    }
    
    /**
     * Returns the serializer type id used by Rexster Protocol
     *
     * @uses Message::_serializer_type
     * @return int Serializer Type id
     */
    public function getSerializerType()
    {
        return $this->_serializer_type;
    }
    
    /**
     * does nothing currently
     *
     * @param mixed $reserved does nothing
     * @return void
     */
    public function setReserved($reserved)
    {
        // This currently does nothing. These are reserved bits.
    }
    
    /**
     * does nothing currently
     *
     * @param mixed $reserved does nothing
     * @return void
     */
    public function getReserved()
    {
        // This currently does nothing. These are reserved bits.
    }
    
    /**
     * Set the length of the serialized message in bytes
     *
     * @param int $size size of serialized message body
     * @uses Message::$_message_size to store the given size
     * @throws Exception if the given size is not an integer
     * @return $this
     */
    public function setMessageSize($size)
    {
        if (is_int($size)) {
            $this->_message_size = $size;
            
            return $this;
        }
        
        throw new Exception('Message size must be an integer');
    }
    
    /**
     * Get the length of the serialized message in bytes
     *
     * @uses Message::$_message_size to store the given size
     * @uses Message::$_message_body_serialized to calculate the length
     * @throws Exception if the given size is not an integer
     * @return int length of the serialized message in bytes
     */
    public function getMessageSize()
    {
        if ($this->_message_size) {
            return $this->_message_size;
        }

        if ($this->_message_body_serialized) {
            $this->_message_size = mb_strlen($this->_message_body_serialized, 'ISO-8859-1');
        } elseif ($this->_message_body) {
            $this->serializeBody();
            
            $this->_message_size = mb_strlen($this->_message_body_serialized, 'ISO-8859-1');
        }
        
        return $this->_message_size;
    }
    
    /**
     * Sets the message type by ID
     *
     * @uses Message::$_message_types to validate the type
     * @uses Message::$_message_type to store the type
     * @param int $type 
     * @throws Exception if the provided type is invalid
     * @return $this
     */
    public function setMessageType($type)
    {
        if (array_key_exists($type, $this->_message_types)) {
            $this->_message_type = $type;
            
            return $this;
        }
        
        throw new Exception(sprintf('%s is not a valid type', $type));
    }
    
    /**
     * Returns the message type by id
     *
     * @uses Message::$_message_type to return the message type.
     * @uses Message::$_message_body to determine the message type if not set.
     * @return int|null message type
     */
    public function getMessageType()
    {
        if (is_int($this->_message_type)) {
            return $this->_message_type;
        }
        
        if ($this->_message_body) {
            $this->_message_type = $this->getMessageKeyFromType($this->_message_body);
            
            return $this->_message_type;
        }
        
        return null;
    }
    
    /**
     * Sets the message body object for this message
     *
     * @param Body $body 
     * @uses Message::$_message_body to store the body.
     * @return $this
     */
    public function setMessageBody(Message\Body $body)
    {
        $this->_message_body = $body;
        
        return $this;
    }
    
    /**
     * Returns the message body object for this message
     *
     * @uses Message::$_message_body to return the body
     * @uses Message::$_message_body_serialized to create the body if not set.
     * @uses Message::unpack() to transform serialized body into object form
     * @return Body|null
     */
    public function getMessageBody()
    {
        if ($this->_message_body) {
            return $this->_message_body;
        }
        
        if ($this->_message_body_serialized) {
            $this->_message_body = $this->unpack();
            
            return $this->_message_body;
        }
        
        return null;
    }
    
    /**
     * Sets the serialized format of the message body
     *
     * @param string $serialized 
     * @uses Message::$_message_body_serialized to store the serialized format
     * @return $this
     */
    public function setMessageBodySerialized($serialized)
    {
        // I don't know how to validate this.
        $this->_message_body_serialized = $serialized;
        
        return $this;
    }
    
    /**
     * Returns the serialized format of the message body
     *
     * @uses Message::$_message_body_serialized to return the serialized format
     * @uses Message::$_message_body to transform into serialized format if not set
     * @return string|null serialized message body
     */
    public function getMessageBodySerialized()
    {
        if ($this->_message_body_serialized) {
            return $this->_message_body_serialized;
        }
        
        if ($this->_message_body) {
            $this->serializeBody();
            
            return $this->_message_body_serialized;
        }
        
        return null;
    }
    
    /**
     * Packs the message into a binary format for transmission to Rexster server
     *
     * @uses Message::$_message_body to validate the message
     * @uses Message::getProtocolVersion() to send to Rexster
     * @uses Message::getSerializerType() to send to Rexster
     * @uses Message::getMessageType() to send to Rexster
     * @uses Message::$_reserved for the reserved bytes
     * @uses Message::getMessageSize() to calculate the message size
     * @uses Message::getMessageBodySerialized() to attach to the message
     * @uses Message::convertIntTo32BitBinaryString() to convert size to binary
     * @throws Exception if the message body isn't valid
     * @return string
     */
    public function pack()
    {
        if (!$this->_message_body InstanceOf Message\Body) {
            throw new Exception('Message::$_message_body is invalid type.');
        }

        $this->_message_size = null;
        $this->_message_body_serialized = null;
        $this->_message_type = null;
        
        $protocol_version = $this->getProtocolVersion();
        $serializer_type  = $this->getSerializerType();
        $message_type     = $this->getMessageType();
        
        $pack = pack(
            'C*',
            $protocol_version,
            $serializer_type,
            $this->_reserved[0],
            $this->_reserved[1],
            $this->_reserved[2],
            $this->_reserved[3],
            $message_type
        );
        
        $message_size = $this->getMessageSize();
        $message_body_serialized = $this->getMessageBodySerialized();
        
        $pack .= $this->convertIntTo32BitBinaryString($message_size).$message_body_serialized;;
        
        return $pack;
    }
    
    /**
     * Unpacks a message from Rexster into a hydrated Message
     *
     * @uses Message::$_message_body_serialized to validate Message
     * @uses Message::$_message_body to store the hydrate message body
     * @uses Message::getMessageTypeFromKey() to instantiate a new Message\Body
     * @uses Message::deserializeBody() to hydrate the Message\Body
     * @throws Exception if there is no serialized message body
     * @return $this
     */
    public function unpack()
    {
        if (!$this->_message_body_serialized) {
            throw new Exception('There is currently no serialized message body. Nothing to unpack.');
        }
        
        $this->_message_body = $this->getMessageTypeFromKey($this->deserializeBody());
        $this->_message_body_serialized = null;
        
        return $this;
    }
    
    /**
     * Get's a Message\Body class for 
     *
     * @param string $raw 
     * @return void
     * @author Travis Black
     */
    private function getMessageTypeFromKey($raw = false)
    {
        if ((int)$this->getMessageType() !== false) {
            return new $this->_message_types[$this->getMessageType()]($raw);
        }
        
        throw new Exception('The given MessageType id doesn\'t map to a message type class.');
    }
    
    /**
     * Gets the id of the message type for the given body
     * 
     * @param Body $body a Message Body object.
     * @uses Message::$_message_types to map the class of the given body object
     *    to the id expected by Rexster
     * @return int $key Message type integer for Rexster Protocol
     */
    private function getMessageKeyFromType(Message\Body $body)
    {
        $key = array_search(get_class($body), $this->_message_types);
        
        if ($key) {
            return $key;
        }
        
        throw new Exception('Message type not found for this instance of Message\Body');
    }
    
    /**
     * Deserializes a serialized message body.
     *
     * @uses Message::$_message_body to serialize it into the expected format
     * @uses Message::$_serializer_type to know into which format to convert the body
     * @return string Message::$_message_body in a serialized format.
     * @throws Exception if the Message::$_serializer_type or Message::$_message_body
     *    isn't set, or is unrecognized type.
     */
    private function serializeBody()
    {
        if (!$this->_message_body InstanceOf Message\Body) {
            throw new Exception(sprintf("Message::_message_body must be an instance of Message\Body. %s given.", gettype($this->_message_body)));
        }
        
        if($this->_serializer_type == self::SERIALIZER_TYPE_JSON) {
            $this->_message_body_serialized = json_encode($this->_message_body->toArray(), JSON_UNESCAPED_UNICODE);
            
            return $this;
        }
        
        throw new Exception("Message::serializeBody() doesn't recognize the given serializer type.");
    }
    
    /**
     * Deserializes a serialized message body.
     *
     * @uses Message::$_message_body_serialized to serialize the message body
     * @uses Message::$_serializer_type to know how to deserialize the message body
     * @return array Message::$_message_body_serialized in array format.
     * @throws Exception if the Message::$_serializer_type or Message::$_message_body_serialized
     *    isn't set, or is unrecognized.
     */
    private function deserializeBody()
    {
        if (!$this->_message_body_serialized) {
            throw new Exception('Message::deserializeBody requires Message::$_message_body_serialized to be set.');
        }
        
        if($this->_serializer_type == self::SERIALIZER_TYPE_JSON) {
            return json_decode($this->_message_body_serialized, true, JSON_UNESCAPED_UNICODE);
        }
        
        throw new Exception("Message::deserializeBody doesn't recognize the given serializer type.");
    }
    
    /**
     * Converts a given integer to a 32bit binary string.
     *
     * @param int $int integer needing converted to binary string
     * @throws Exception if $int is wrong type
     * @return string Integer convergted to 32bit binary string.
     */
    private function convertIntTo32BitBinaryString($int)
    {
        if (!is_int($int)) {
            throw new Exception(sprintf("Message::convertIntTo32BitBinaryString requires an int. %s given.", gettype($int)));
        }
        
        $result = array();
        for($i=0;$i<4;$i++) {
            array_unshift($result, pack('C*', $int & 0xff));
            $int >>= 8;

        }
        
        return implode('', $result);
    }
}