<?php

namespace Blogger\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Blogger\GameBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="number1", type="integer")
     * @Assert\GreaterThan(999)
     * @Assert\LessThan(10000)
     */
    private $number1;

    /**
     * @var int
     * @Assert\GreaterThan(999)
     * @Assert\LessThan(10000)
     * @ORM\Column(name="number2", type="integer", nullable=true)
     */
    private $number2;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="player1_games", cascade={"persist"})
     * @ORM\JoinColumn(name="player1_id", referencedColumnName="id", onDelete="cascade")
     * @var Blogger\BlogBundle\Entity\User
     */
    protected $player1;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="player2_games", cascade={"persist"})
     * @ORM\JoinColumn(name="player2_id", referencedColumnName="id", onDelete="cascade")
     * @var Blogger\BlogBundle\Entity\User
     * @Assert\NotNull()
     */
    protected $player2;

    /**
     * @ORM\ManyToOne(targetEntity="Blogger\BlogBundle\Entity\User", inversedBy="step_games", cascade={"persist"})
     * @ORM\JoinColumn(name="next_id", referencedColumnName="id", onDelete="cascade")
     * @var Blogger\BlogBundle\Entity\User
     */
    protected $next_step;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number1
     *
     * @param integer $number1
     *
     * @return Game
     */
    public function setNumber1($number1)
    {
        $this->number1 = $number1;

        return $this;
    }

    /**
     * Get number1
     *
     * @return int
     */
    public function getNumber1()
    {
        return $this->number1;
    }

    /**
     * Set number2
     *
     * @param integer $number2
     *
     * @return Game
     */
    public function setNumber2($number2)
    {
        $this->number2 = $number2;

        return $this;
    }

    /**
     * Get number2
     *
     * @return int
     */
    public function getNumber2()
    {
        return $this->number2;
    }

    public function setPlayer1($player)
    {
        $this->player1 = $player;

        return $this;
    }

    public function getPlayer1()
    {
        return $this->player1;
    }

    public function setPlayer2($player)
    {
        $this->player2 = $player;

        return $this;
    }

    public function getPlayer2()
    {
        return $this->player2;
    }

    public function getNextStep()
    {
        return $this->next_step;
    }

    public function setNextStep($user)
    {
        $this->next_step = $user;

        return $this;
    }
}

