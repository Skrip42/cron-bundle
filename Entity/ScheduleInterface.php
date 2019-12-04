<?php

namespace Skrip42\Bundle\ChronBundle\Entity;

interface ScheduleInterface
{
    public function getId(): ?int;
    public function getCommand(): ?string;
    public function setCommand(string $command): self;
    public function setPattern(string $pattern): self;
    public function getPattern(): ?string;
    public function getLastRunning(): ?\DateTimeInterface;
    public function setLastRunning(?\DateTimeInterface $lastRunning): self;
    public function getRunningCounter(): ?int;
    public function setRunningCounter(int $runningCounter): self;
    public function incRunningCounter() : self;
    public function getActive(): ?bool;
    public function setActive(bool $active): self;
    public function toggleActive(): self;
}
