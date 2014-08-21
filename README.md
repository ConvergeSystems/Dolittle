Dolittle
========
A PHP RexPro Driver Implementation
----------------------------------

With Dolittle, it is possible to communicate with Rexster via RexPro.

###Basic Usage

The most basic usage of Dolittle is to execute  a script (query) against a Rexster server using Gremlin. This is done by instantiating a Dolittle client, and executing the ```exectueScript``` method. This will return an instance of ```\Converge\Dolittle\Message```.

The Message returned will have basic meta-data from the Rexster server, as well as an embeded Message Body. The Message Body will be an instance of ```\Converge\Dolittle\Message\Body\Response```. It will either be a ```\Converge\Dolittle\Message\Body\Response\Script``` or a ```\Converge\Dolittle\Message\Body\Response\Error```.

####Usage Example

```php
$client  = new \Converge\Dolittle\Client('localhost:8184');
$results = $client->executeScript('g.V()', 'my_graph_name', array('BoundParam1' => 'boundValue1'))->getMessageBody();

get_class($results); // \Converge\Dolittle\Message\Body\Response\Script
echo($results->getBindings()); // array('BoundParam1' => 'boundValue1')
echo($results->getResults()); // array('0' => array('type' => 'vertex', '_id' => '12345'))
echo($results->toArray()); // See Below

/*
 array(
    'session'  => '00000000-0000-0000-0000-000000000000',
    'request'  => '00000000-0000-0000-0000-000000000000',
    'meta'     => array(),
    'results'  => array(
        '0' => array(
            'type' => 'vertex',
            '_id' => '12345'
        )
     ),
    'bindings' => array('BoundParam1' => 'boundValue1'),
)
*/    
````

###Advanced Usage

Some use cases may require more advanced usage. In this case, you will need to instantiate the message and message body directly, then use the client to send the message and get the response manually. See the example below.

####Usage Example

```php
$client  = new \Converge\Dolittle\Client('localhost:8184');

$script  = new \Converge\Dolittle\Message\Body\Request\Script('g.V()');
$script->setBindings(array('BoundParam1' => 'boundValue1'));
$script->setMeta(array('graphName' => 'my_graph_name'));

$message = new \Converge\Dolittle\Message();
$message->setMessageBody($script);

$client->send($message);
$results = $client->getResponse()->getMessageBody();

get_class($results); // \Converge\Dolittle\Message\Body\Response\Script
echo($results->getBindings()); // array('BoundParam1' => 'boundValue1')
echo($results->getResults()); // array('0' => array('type' => 'vertex', '_id' => '12345'))
echo($results->toArray()); // See Below

/*
 array(
    'session'  => '00000000-0000-0000-0000-000000000000',
    'request'  => '00000000-0000-0000-0000-000000000000',
    'meta'     => array(),
    'results'  => array(
        '0' => array(
            'type' => 'vertex',
            '_id' => '12345'
        )
     ),
    'bindings' => array('BoundParam1' => 'boundValue1'),
)
*/
````

###Future

- [ ] Add Session Support
- [ ] Add MsgPack Support
- [ ] Finish Documentation
- [ ] Build Unit Tests
- [ ] Add to Composer