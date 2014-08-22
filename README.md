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
<?php
    
require_once('vendor/autoload.php');

$graph_name = 'converge';
$params = array('MY_BOUND_PARAM' => 'converge engineering');

$client  = new \Converge\Dolittle\Client('tcp://localhost:8184');
$client->executeScript('g.addVertex(name:MY_BOUND_PARAM)', $graph_name, $params);

$results = $client->executeScript('g.V("name", MY_BOUND_PARAM)', $graph_name, $params)->getMessageBody();

echo(get_class($results)); // Converge\Dolittle\Message\Body\Response\Script
var_dump($results->getResults()); // See Below
/*
array(1) {
  [0] =>
  array(3) {
    '_type' =>
    string(6) "vertex"
    '_id' =>
    string(6) "440068"
    '_properties' =>
    array(1) {
      'name' =>
      string(20) "converge engineering"
    }
  }
}
*/

var_dump($results->toArray()); // See Below
/*
array(5) {
  'session' =>
  string(36) "00000000-0000-0000-0000-000000000000"
  'request' =>
  string(36) "a2bedfa8-46d8-4df7-937c-81c8a3e319d3"
  'meta' =>
  array(0) {
  }
  'results' =>
  array(1) {
    [0] =>
    array(3) {
      '_type' =>
      string(6) "vertex"
      '_id' =>
      string(6) "440068"
      '_properties' =>
      array(1) {
        'name' =>
        string(20) "converge engineering"
      }
    }
  }
  'bindings' =>
  array(0) {
  }
}
*/       
````

###Advanced Usage

Some use cases may require more advanced usage. In this case, you will need to instantiate the message and message body directly, then use the client to send the message and get the response manually. See the example below.

####Usage Example

```php
<?php
require_once('vendor/autoload.php');

$graph_name = 'converge';
$params  = array('MY_BOUND_PARAM' => 'converge engineering');
$client  = new \Converge\Dolittle\Client('tcp://localhost:8184');

$script  = new \Converge\Dolittle\Message\Body\Request\Script();
$script->setScript('g.addVertex(name:MY_BOUND_PARAM)');
$script->setBindings($params);
$script->setMeta(array('graphName' => $graph_name));

$message = new \Converge\Dolittle\Message();
$message->setMessageBody($script);
$client->send($message);

$script  = new \Converge\Dolittle\Message\Body\Request\Script();
$script->setScript('g.V("name", MY_BOUND_PARAM)');
$script->setBindings($params);
$script->setMeta(array('graphName' => $graph_name));

$message = new \Converge\Dolittle\Message();
$message->setMessageBody($script);

$message->setMessageBody($script);
$client->send($message);

$response = $client->getResponse();
$results = $response->getMessageBody();

echo(get_class($results)); // Converge\Dolittle\Message\Body\Response\Script
var_dump($results->getResults()); // See Below
/*
array(1) {
  [0] =>
  array(3) {
    '_type' =>
    string(6) "vertex"
    '_id' =>
    string(6) "440676"
    '_properties' =>
    array(1) {
      'name' =>
      string(20) "converge engineering"
    }
  }
}
*/

var_dump($results->toArray()); // See Below
/*
array(5) {
  'session' =>
  string(36) "00000000-0000-0000-0000-000000000000"
  'request' =>
  string(36) "e66b643a-5b90-4ca6-b7bf-d5ee52ddeb62"
  'meta' =>
  array(0) {
  }
  'results' =>
  array(1) {
    [0] =>
    array(3) {
      '_type' =>
      string(6) "vertex"
      '_id' =>
      string(6) "440676"
      '_properties' =>
      array(1) {
        'name' =>
        string(20) "converge engineering"
      }
    }
  }
  'bindings' =>
  array(0) {
  }
}
*/
````

###Future

- [ ] Add Session Support
- [ ] Add MsgPack Support
- [ ] Finish Documentation
- [ ] Build Unit Tests