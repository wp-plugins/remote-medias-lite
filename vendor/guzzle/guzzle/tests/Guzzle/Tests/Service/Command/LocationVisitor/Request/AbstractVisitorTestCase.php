<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command\LocationVisitor\Request;

use WPRemoteMediaExt\Guzzle\Http\Message\EntityEnclosingRequest;
use WPRemoteMediaExt\Guzzle\Service\Description\Operation;
use WPRemoteMediaExt\Guzzle\Service\Description\Parameter;
use WPRemoteMediaExt\Guzzle\Service\Description\SchemaValidator;
use WPRemoteMediaExt\Guzzle\Service\Command\OperationCommand;
use WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand;
use WPRemoteMediaExt\Guzzle\Tests\Service\Mock\MockClient;

abstract class AbstractVisitorTestCase extends \Guzzle\Tests\GuzzleTestCase
{
    protected $command;
    protected $request;
    protected $param;
    protected $validator;

    public function setUp()
    {
        $this->command = new MockCommand();
        $this->request = new EntityEnclosingRequest('POST', 'http://www.test.com/some/path.php');
        $this->validator = new SchemaValidator();
    }

    protected function getCommand($location)
    {
        $command = new OperationCommand(array(), $this->getNestedCommand($location));
        $command->setClient(new MockClient());

        return $command;
    }

    protected function getNestedCommand($location)
    {
        return new Operation(array(
            'httpMethod' => 'POST',
            'parameters' => array(
                'foo' => new Parameter(array(
                    'type'         => 'object',
                    'location'     => $location,
                    'sentAs'       => 'Foo',
                    'required'     => true,
                    'properties'   => array(
                        'test' => array(
                            'type'      => 'object',
                            'required'  => true,
                            'properties' => array(
                                'baz' => array(
                                    'type'    => 'boolean',
                                    'default' => true
                                ),
                                'jenga' => array(
                                    'type'    => 'string',
                                    'default' => 'hello',
                                    'sentAs'  => 'Jenga_Yall!',
                                    'filters' => array('strtoupper')
                                )
                            )
                        ),
                        'bar' => array('default' => 123)
                    ),
                    'additionalProperties' => array(
                        'type' => 'string',
                        'filters' => array('strtoupper'),
                        'location' => $location
                    )
                )),
                'arr' => new Parameter(array(
                    'type'         => 'array',
                    'location'     => $location,
                    'items' => array(
                        'type' => 'string',
                        'filters' => array('strtoupper')
                     )
                )),
            )
        ));
    }

    protected function getCommandWithArrayParamAndFilters()
    {
        $operation = new Operation(array(
            'httpMethod' => 'POST',
            'parameters' => array(
                'foo' => new Parameter(array(
                    'type' => 'string',
                    'location' => 'query',
                    'sentAs' => 'Foo',
                    'required' => true,
                    'default' => 'bar',
                    'filters' => array('strtoupper')
                )),
                'arr' => new Parameter(array(
                    'type' => 'array',
                    'location' => 'query',
                    'sentAs' => 'Arr',
                    'required' => true,
                    'default' => array(123, 456, 789),
                    'filters' => array(array('method' => 'implode', 'args' => array(',', '@value')))
                ))
            )
        ));
        $command = new OperationCommand(array(), $operation);
        $command->setClient(new MockClient());

        return $command;
    }
}
