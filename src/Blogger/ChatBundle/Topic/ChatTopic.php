<?php

namespace Blogger\ChatBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Blogger\ChatBundle\Entity\ChatMessage;

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
        $topic->broadcast(['msg' =>  "User " . $user . " has connected to chat"]);
    }

    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => "User " . $user . " has disconnected the chat"]);
    }

    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {     
        $resource = $topic->getId();
        $pos = strrpos($resource, '/', -1);
        $id = substr($resource, $pos+1);

        $user = $this->clientManipulator->getClient($connection) . "";
        $obj_user = $this->em->getRepository('BloggerBlogBundle:User')->findOneBy(array("username" => $user));
        $chat = $this->em->getRepository('BloggerChatBundle:Chat')->findOneById($id);
        if(!$chat){
            $topic->broadcast([
            'msg' => "Chat not found",
        ]);
        } else {
            $message = new ChatMessage();
            $message->setContent($event);
            $message->setUser($obj_user);
            $message->setChat($chat);
            $message->setTime(new \DateTime());
            $this->em->persist($message);
            $this->em->flush();
      
            $topic->broadcast([
                'msg' => $event,
                'user' => $user . ""
            ]);
        }

    }

    public function getName()
    {
        return 'topic.chat';
    }

}