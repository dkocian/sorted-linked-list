<?php

declare(strict_types=1);

namespace Dkocian;

use Countable;
use InvalidArgumentException;
use Iterator;
use OutOfBoundsException;

class SortedLinkedList implements Iterator, Countable
{
    private ?Node $head = null;
    private int $position = 0;
    private ?Node $currentNode = null;
    private Direction $direction;

    public function __construct(Direction $direction = Direction::ASC)
    {
        $this->direction = $direction;
    }

    public function setDirection(Direction $direction): self
    {
        $this->direction = $direction;
        $this->orderItems();
        return $this;
    }

    public function addValue(int|string $value): self
    {
        if ($this->getType() !== null && $this->getType() !== gettype($value)) {
            throw new InvalidArgumentException('Linked list can contain strings or integers, but not both.');
        }

        $valueNode = new Node($value);

        if ($this->head === null) {
            $this->head = $valueNode;
            $this->currentNode = $valueNode;
        } elseif ($this->compare($value, $this->head->getValue())) {
            $next = $this->head;
            $this->head = $valueNode->setNext($next);
        } else {
            $node = $this->head;
            foreach ($this as $item) {
                if ($item->getNext() !== null && $this->compare($value, $item->getNext()->getValue())) {
                    break;
                }
                $node = &$item;
            }
            $next = $node->getNext();
            $valueNode->setNext($next);
            $node->setNext($valueNode);
        }

        return $this;
    }

    private function compare(int|string $a, int|string $b): bool
    {
        if ($this->getType() === 'string') {
            $compared = strcmp((string) $a, (string) $b);
            return $this->direction === Direction::ASC ? $compared <= 0 : $compared >= 0;
        }

        if ($this->direction === Direction::ASC) {
            return $a < $b;
        }
        return $a > $b;
    }

    public function removeItem(int|string $value): self
    {
        $currentNode = $this->head;
        $previousNode = null;
        while ($currentNode !== null) {
            if ($currentNode->getValue() === $value) {
                if ($currentNode === $this->head && $currentNode->getNext() === null) {
                    $this->head = null;
                    break;
                }
                if ($previousNode !== null) {
                    $previousNode->setNext($this->findNextValidNode($value, $currentNode->getNext()));
                }
            } else {
                if ($previousNode === null) {
                    $this->head = $currentNode;
                }
                $previousNode = $currentNode;
            }

            if ($this->compare($value, $currentNode->getValue())) {
                break;
            }
            $currentNode = $currentNode->getNext();
        }
        return $this;
    }

    public function findNextValidNode(int|string $value, ?Node $node = null): ?Node
    {
        $currentNode = $node;
        while ($currentNode !== null && $currentNode->getValue() !== $value) {
            $currentNode = $currentNode->getNext();
        }
        return $currentNode;
    }

    public function current(): Node
    {
        if ($this->currentNode !== null) {
            return $this->currentNode;
        }
        throw new OutOfBoundsException();
    }

    public function next(): void
    {
        if ($this->currentNode === null) {
            return;
        }

        $this->position++;
        $this->currentNode = $this->currentNode->getNext();
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return $this->currentNode !== null;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->currentNode = $this->head;
    }

    public function count(): int
    {
        $count = 0;
        $currentNode = $this->head;
        while ($currentNode !== null) {
            $count++;
            $currentNode = $currentNode->getNext();
        }
        return $count;
    }

    /**
     * @return int[]|string[]
     */
    public function getItems(): array
    {
        $items = [];
        foreach ($this as $item) {
            $items[] = $item->getValue();
        }
        return $items;
    }

    public function join(string $separator = ','): string
    {
        return implode($separator, $this->getItems());
    }

    private function orderItems(): void
    {
        $items = $this->getItems();
        usort($items, fn (int|string $a, int|string $b) => $this->compare($a, $b) ? -1 : 1);
        $this->createFromArray($items);
    }

    /**
     * @param int[]|string[] $items
     */
    private function createFromArray(array $items): void
    {
        $item = end($items);
        if ($item === false) {
            return;
        }

        $node = null;
        do {
            if ($node === null) {
                $node = new Node($item);
            } else {
                $node = new Node($item, $node);
            }
        } while ($item = prev($items));
        $this->head = $node;
    }

    private function getType(): ?string
    {
        return $this->head === null ? null : gettype($this->head->getValue());
    }
}
