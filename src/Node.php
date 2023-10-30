<?php

declare(strict_types=1);

namespace Dkocian;

class Node
{
    private ?Node $next;
    public function __construct(readonly private int|string $value, Node $next = null)
    {
        $this->next = $next;
    }

    public function getValue(): int|string
    {
        return $this->value;
    }

    public function getNext(): ?Node
    {
        return $this->next;
    }

    public function setNext(?Node $next): self
    {
        $this->next = $next;
        return $this;
    }
}
