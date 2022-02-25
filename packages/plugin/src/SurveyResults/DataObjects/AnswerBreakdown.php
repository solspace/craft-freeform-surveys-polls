<?php

namespace Solspace\SurveysPolls\SurveyResults\DataObjects;

class AnswerBreakdown implements \JsonSerializable
{
    private $fieldTotals;

    private $label;

    private $value;

    private $votes;

    private $ranking;

    public function __construct(FieldTotals $fieldTotals, string $label, string $value)
    {
        $this->fieldTotals = $fieldTotals;
        $this->label = $label;
        $this->value = $value;
        $this->votes = 0;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(int $rank)
    {
        $this->ranking = $rank;
    }

    public function incrementVotes(int $count = 1)
    {
        $this->votes += $count;
        $this->fieldTotals->getBreakdown()->rank();
    }

    public function getPercentage(): float
    {
        $totalVotes = $this->fieldTotals->getVotes();

        if (!$totalVotes) {
            $percentage = 0;
        } else {
            $percentage = ($this->votes / $totalVotes) * 100;
        }

        return (float) number_format($percentage, 2, '.', '');
    }

    public function jsonSerialize()
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'votes' => $this->getVotes(),
            'ranking' => $this->getRanking(),
            'percentage' => $this->getPercentage(),
        ];
    }
}
