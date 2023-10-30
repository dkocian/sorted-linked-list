<?php

declare(strict_types=1);

namespace Dkocian;

use Countable;
use InvalidArgumentException;
use Iterator;

class SortedLinkedList implements Iterator, Countable
{
    /**
     * @var int[]|string[]
     */
    private array $items;
    private ?string $listType = null;
    private Direction $direction;

    public function __construct(Direction $direction = Direction::ASC)
    {
        $this->direction = $direction;
    }

    public function setDirection(Direction $direction): self
    {
        $this->direction = $direction;
        $this->orderItems($direction);
        return $this;
    }

    public function addValue(int|string $value): self
    {
        if ($this->listType === null) {
            $this->listType = gettype($value);
        }

        if ($this->listType !== gettype($value)) {
            throw new InvalidArgumentException('List can contain strings or integers, but not both.');
        }

        $this->items[] = $value;

        $this->orderItems($this->direction);

        return $this;
    }

    public function removeItem(int|string $value): self
    {
        $key = array_search($value, $this->items, true);
        if ($key !== false) {
            unset($this->items[$key]);
        }

        if ($this->count() === 0) {
            $this->listType = null; // if is list empty, reset listType
        }

        $this->items = array_values($this->items);
        return $this;
    }

    public function current(): int|string|false
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): int|string|null
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    private function orderItems(Direction $direction): void
    {
        $direction === Direction::ASC ? sort($this->items) : rsort($this->items);
    }

    /**
     * @return int[]|string[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function join(string $separator = ','): string
    {
        return implode($separator, $this->items);
    }
}
