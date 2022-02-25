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
    protected $list = [];

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

    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->list[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws SurveysPollsException
     */
    public function offsetSet($offset, $value)
    {
        throw new SurveysPollsException(
            'Cannot set properties directly on Collection. Use ::app() and ::remove() methods'
        );
    }

    /**
     * @param mixed $offset
     *
     * @throws SurveysPollsException
     */
    public function offsetUnset($offset)
    {
        throw new SurveysPollsException('Cannot delete properties from Property Collection directly. Use ::remove() instead');
    }
}
