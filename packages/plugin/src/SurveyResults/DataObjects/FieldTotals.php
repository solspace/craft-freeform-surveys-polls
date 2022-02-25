<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\SurveyResults\DataObjects;

use Solspace\Freeform\Fields\TextareaField;
use Solspace\Freeform\Fields\TextField;
use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\SurveysPolls\SurveyResults\Collections\AnswerBreakdownCollection;

class FieldTotals implements \JsonSerializable, \IteratorAggregate, \Countable, \ArrayAccess
{
    private const RANK_BY_TOP_FIELDS = [TextField::class, TextareaField::class];

    /** @var FieldInterface */
    private $field;

    /** @var AnswerBreakdown[]|AnswerBreakdownCollection */
    private $breakdown;

    /** @var int */
    private $skipped;

    public function __construct(FieldInterface $field)
    {
        $this->field = $field;
        $this->skipped = 0;
        $this->breakdown = new AnswerBreakdownCollection();
    }

    public function getField(): FieldInterface
    {
        return $this->field;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function incrementSkipped(int $count = 1)
    {
        $this->skipped += $count;
    }

    public function getVotes(): int
    {
        $votes = 0;
        foreach ($this->breakdown as $breakdown) {
            $votes += $breakdown->getVotes();
        }

        return $votes;
    }

    public function getAverage(): ?float
    {
        if (0 === \count($this->breakdown)) {
            return null;
        }

        $sum = 0;
        $count = 0;
        foreach ($this->breakdown as $breakdown) {
            if (!is_numeric($breakdown->getValue())) {
                return null;
            }

            $sum += (float) $breakdown->getValue() * $breakdown->getVotes();
            $count += $breakdown->getVotes();
        }

        $count = max(1, $count);

        return (float) number_format($sum / $count, 2, '.', '');
    }

    public function getMax(): ?float
    {
        $max = 0;
        foreach ($this->breakdown as $breakdown) {
            if (!is_numeric($breakdown->getValue())) {
                return null;
            }

            $max = max($breakdown->getValue(), $max);
        }

        return $max;
    }

    public function getBreakdown($top = null): AnswerBreakdownCollection
    {
        if (null !== $top) {
            return $this->breakdown->cloneRanked($top);
        }

        return $this->breakdown;
    }

    public function jsonSerialize(): array
    {
        $sortByTop = \in_array(\get_class($this->field), self::RANK_BY_TOP_FIELDS, true) ? true : null;

        return [
            'field' => [
                'id' => $this->field->getId(),
                'handle' => $this->field->getHandle(),
                'label' => $this->field->getLabel(),
                'type' => $this->field->getType(),
                'multiChoice' => $this->field instanceof MultipleValueInterface,
            ],
            'average' => $this->getAverage(),
            'max' => $this->getMax(),
            'votes' => $this->getVotes(),
            'skipped' => $this->skipped,
            'breakdown' => $this->getBreakdown($sortByTop),
        ];
    }

    public function getIterator()
    {
        return $this->breakdown->getIterator();
    }

    public function offsetExists($offset)
    {
        return $this->breakdown->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->breakdown->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->breakdown->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->breakdown->offsetUnset($offset);
    }

    public function count()
    {
        return $this->breakdown->count();
    }
}
