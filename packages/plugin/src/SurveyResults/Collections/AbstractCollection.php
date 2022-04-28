<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\SurveyResults\Collections;

use Solspace\SurveysPolls\Exceptions\SurveysPollsException;

abstract class AbstractCollection implements CollectionInterface, \IteratorAggregate, \Countable, \ArrayAccess, \JsonSerializable
{
    protected array $list = [];

    public function asArray(): array
    {
        return $this->list;
    }

    public function jsonSerialize()
    {
        return array_values($this->asArray());
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->list);
    }

    public function count(): int
    {
        return \count($this->list);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->list[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        throw new SurveysPollsException(
            'Cannot set properties directly on Collection. Use ::app() and ::remove() methods'
        );
    }

    public function offsetUnset(mixed $offset)
    {
        throw new SurveysPollsException('Cannot delete properties from Property Collection directly. Use ::remove() instead');
    }
}
