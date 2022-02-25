<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\SurveyResults\Collections;

use Solspace\SurveysPolls\SurveyResults\DataObjects\AnswerBreakdown;

class AnswerBreakdownCollection extends AbstractCollection
{
    public function get(string $value): ?AnswerBreakdown
    {
        return $this->list[$value] ?? null;
    }

    public function add(AnswerBreakdown $item): self
    {
        $this->list[$item->getValue()] = $item;

        return $this;
    }

    public function remove(AnswerBreakdown $item)
    {
        if (\array_key_exists($item->getValue(), $this->list)) {
            unset($this->list[$item->getValue()]);
        }
    }

    public function cloneRanked($top): self
    {
        $ranked = $this->getRankedValues();
        if (false === $top || $top < 0) {
            $ranked = array_reverse($ranked, true);
        }

        if (is_numeric($top)) {
            $ranked = \array_slice($ranked, $bottom ?? 0, abs($top), true);
        }

        $collection = new self();
        foreach ($ranked as $value => $votes) {
            $collection->add($this->get($value));
        }

        return $collection;
    }

    public function rank()
    {
        $ranked = $this->getRankedValues();

        $rank = 1;
        foreach ($ranked as $value => $votes) {
            $breakdown = $this->list[$value];
            $breakdown->setRanking($rank++);
        }
    }

    private function getRankedValues(): array
    {
        $ranked = [];

        /** @var AnswerBreakdown $breakdown */
        foreach ($this->list as $breakdown) {
            $ranked[$breakdown->getValue()] = $breakdown->getVotes();
        }

        arsort($ranked, \SORT_NUMERIC);

        return $ranked;
    }
}
