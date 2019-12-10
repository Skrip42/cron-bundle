<?php

namespace Skrip42\Bundle\ChronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Skrip42\Bundle\ChronBundle\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $command;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastRunning;

    /**
     * @ORM\Column(type="integer")
     */
    private $runningCounter = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $pattern;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Set field from pattern
     *
     * @param string $pattern
     *
     * @return self
     */
    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * compile pattern
     *
     * @return string
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     *
     * @return \DateTimeInterface|null
     */
    public function getLastRunning(): ?\DateTimeInterface
    {
        return $this->lastRunning;
    }

    /**
     * @param \DateTimeInterface|null $lastRunning
     *
     * @return self
     */
    public function setLastRunning(?\DateTimeInterface $lastRunning): self
    {
        $this->lastRunning = $lastRunning;

        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getRunningCounter(): ?int
    {
        return $this->runningCounter;
    }

    /**
     * @param int $runningCounter
     *
     * @return self
     */
    public function setRunningCounter(int $runningCounter): self
    {
        $this->runningCounter = $runningCounter;

        return $this;
    }

    /**
     *
     * @return self
     */
    public function incRunningCounter() : self
    {
        $this->runningCounter++;

        return $this;
    }

    /**
     *
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return self
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Toggle active
     *
     * @return self
     */
    public function toggleActive(): self
    {
        $this->setActive(!$this->active);

        return $this;
    }
}
