<?php

declare(strict_types=1);

namespace Dkocian\Tests\Unit;

use Dkocian\Direction;
use Dkocian\SortedLinkedList;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SortedLinkedListTest extends TestCase
{
    public function testSortInt(): void
    {
        $list = new SortedLinkedList();
        $list
            ->addValue(1)
            ->addValue(1)
            ->addValue(3)
            ->addValue(2);

        self::assertSame([1, 1, 2, 3], $list->getItems());

        $list->setDirection(Direction::DESC);
        $list->addValue(5);
        self::assertSame([5, 3, 2, 1, 1], $list->getItems());

        $list->setDirection(Direction::ASC);
        self::assertSame([1, 1, 2, 3, 5], $list->getItems());
    }

    public function testSortString(): void
    {
        $list = new SortedLinkedList(Direction::ASC);
        $list
            ->addValue('car')
            ->addValue('abstract')
            ->addValue('foo');

        self::assertSame(['abstract', 'car', 'foo'], $list->getItems());
    }

    public function testNavigation(): void
    {
        $list = new SortedLinkedList(Direction::ASC);
        $list
            ->addValue(1)
            ->addValue(3)
            ->addValue(2);

        self::assertSame(1, $list->current()->getValue());

        $list->next();
        self::assertSame(2, $list->current()->getValue());
        self::assertSame(1, $list->key());

        $list->next();
        self::assertTrue($list->valid());
        self::assertSame(3, $list->current()->getValue());

        $list->next();
        self::assertFalse($list->valid());

        $list->rewind();
        self::assertSame(1, $list->current()->getValue());
        self::assertCount(3, $list);
    }

    public function testCombineTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Linked list can contain strings or integers, but not both.');

        $list = new SortedLinkedList();
        $list->addValue(1);
        $list->addValue('foo');
    }
    public function testRemoveEmptyList(): void
    {
        $list = new SortedLinkedList();
        $list->removeItem(1);
        self::assertCount(0, $list);
    }

    public function testRemoveItem(): void
    {
        $list = new SortedLinkedList();
        $list
            ->addValue(1)
            ->addValue(1)
            ->addValue(3)
            ->addValue(3)
            ->addValue(2);

        $list->removeItem(3);
        self::assertSame([1, 1, 2], $list->getItems());

        $list->removeItem(1);
        self::assertSame([2], $list->getItems());

        $list->removeItem(2);
        self::assertCount(0, $list);

        $list->addValue('foo');
        self::assertSame(['foo'], $list->getItems());
    }

    public function testRemoveNonexistentItem(): void
    {
        $list = new SortedLinkedList();
        $list
            ->addValue(1)
            ->addValue(3)
            ->addValue(3);

        $list->removeItem('foo');
        self::assertSame([1, 3, 3], $list->getItems());
    }

    public function testIterations(): void
    {
        $list = new SortedLinkedList();
        $list
            ->addValue(1)
            ->addValue(3)
            ->addValue(2);

        $actual = [];
        foreach ($list as $index => $value) {
            $actual[$index] = $value->getValue();
        }
        self::assertSame([1, 2, 3], $actual);
    }

    public function testJoin(): void
    {
        $list = new SortedLinkedList();
        $list
            ->addValue(1)
            ->addValue(3);

        self::assertSame('1,3', $list->join());
        self::assertSame('1;3', $list->join(';'));
    }
}
