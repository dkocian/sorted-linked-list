# SortedLinkedList

Linked list that keeps values sorted. Can contain strings or integers value, but not both.

## Installation

```shell
composer require dkocian/sorted-linked-list
```

## Usage examples

```php
$list = new Dkocian/SortedLinkedList();
$list->addValue(2); // 2
$list->addValue(3); // 2, 3
$list->addValue(1); // 1, 2, 3

echo $list->join(); // 1,2,3
```

Remove item from list:
```php
$list->removeItem(1);
```

List is iterable and countable:
```php
$list = new Dkocian/SortedLinkedList();
$list->addValue(3); // 3
$list->addValue(1); // 1, 3

echo $list->count(); // 2

foreach ($list as $item) {
    echo $item;
}
```

## Tests

Run all test:
```shell
composer check
```

Run phpunit:
```shell
composer phpunit
```

Run phpstan:
```shell
composer phpstan
```

Run coding standards:
```shell
composer cs
```

## License
This project is licensed under the terms of the MIT license.