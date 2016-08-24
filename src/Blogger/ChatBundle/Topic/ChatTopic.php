<?php

namespace Blogger\ChatBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

class ChatTopic implements TopicInterface
{
    protected $em;
    protected $clientManipulator;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct($em, ClientManipulatorInterface $clientManipulator)
    {
        $this->em = $em;
        $this->clientManipulator = $clientManipulator;
    }
      
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        //$user = $this->clientStorage->getClient($connection->WAMP->clientStorageId);
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' =>  "User " . $user . " has joined to chat"]);
    }

    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => "User " . $user . " has left the chat"]);
    }

    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {     
        $user = $this->clientManipulator->getClient($connection);   
        $topic->broadcast([
            'msg' => $event,
            'user' => $user . ""
        ]);
    }

    public function getName()
    {
        return 'topic.chat';
    }

}