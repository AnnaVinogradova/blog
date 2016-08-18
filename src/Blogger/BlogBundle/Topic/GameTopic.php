<?php

namespace Blogger\BlogBundle\Topic;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;

use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Blogger\BlogBundle\Entity\Post;

class GameTopic implements TopicInterface
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
        $posts = $this->em->getRepository('BloggerBlogBundle:Post')->findOneById(1);
        $name = $posts->getTitle();
        $id = $connection->resourceId;
        $topic_id = $topic->getId();

        //$user = $this->clientStorage->getClient($connection->WAMP->clientStorageId);
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' =>  "User " . $user . " has joined to game"]);
    }

    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $user = $this->clientManipulator->getClient($connection);
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast(['msg' => "User " . $user . " has left the game"]);
    }

    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $resource = $topic->getId();
        $pos = strrpos($resource, '/', -1);
        $id = substr($resource, $pos+1);

        $user = $this->clientManipulator->getClient($connection);
        $obj_user = $this->em->getRepository('BloggerBlogBundle:User')->findOneBy(array("username" => $user));
        $game = $this->em->getRepository('BloggerGameBundle:Game')->findOneById($id);

        //check user access
        if($game->getNextStep()->getUsername() == $user){
            if($game->getPlayer1()->getUsername() == $user){
                $number = $game->getNumber2();
                $game->setNextStep($game->getPlayer2());
            } else {
                $number = $game->getNumber1();
                $game->setNextStep($game->getPlayer1());
            }

            $this->em->persist($game);
            $this->em->flush();

            $called_number = $event + 0;
            if($called_number == $number){
                $message = $user . " win";
            } else {
                $message = "try later";
            }
        } else {
            $message = " but it's not his step";
        }

        $topic->broadcast([
            'msg' => "User ". $user . " call " . $event . $message,
        ]);
    }

    public function getName()
    {
        return 'topic.game';
    }
}