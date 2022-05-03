<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\SurveyResults\DataObjects;

use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\SurveysPolls\SurveyResults\Collections\CollectionInterface;
use Solspace\SurveysPolls\SurveyResults\Collections\FieldTotalsCollection;

class FormTotals implements CollectionInterface, \IteratorAggregate, \Countable, \ArrayAccess, \JsonSerializable
{
    /** @var Form */
    private $form;

    /** @var FieldTotalsCollection */
    private $fieldTotals;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->fieldTotals = new FieldTotalsCollection();
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getVotes(): int
    {
        $votes = 0;

        /** @var FieldTotals $totals */
        foreach ($this->getFieldTotals() as $totals) {
            $votes += $totals->getVotes();
        }

        return $votes;
    }

    public function getFieldTotals(): FieldTotalsCollection
    {
        return $this->fieldTotals;
    }

    public function jsonSerialize()
    {
        $submissionService = Freeform::getInstance()->submissions;
        $formIds = [$this->form->getId()];

        $submissions = $submissionService->getSubmissionCount($formIds);
        $spam = $submissionService->getSubmissionCount($formIds, null, true);

        return [
            'form' => [
                'id' => $this->form->getId(),
                'handle' => $this->form->getHandle(),
                'name' => $this->form->getName(),
                'color' => $this->form->getColor(),
                'submissions' => $submissions,
                'spam' => $spam,
            ],
            'votes' => $this->getVotes(),
            'results' => $this->fieldTotals,
        ];
    }

    public function getIterator()
    {
        return $this->fieldTotals->getIterator();
    }

    public function offsetExists($offset)
    {
        return $this->fieldTotals->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->fieldTotals->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->fieldTotals->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->fieldTotals->offsetUnset($offset);
    }

    public function count()
    {
        return $this->fieldTotals->count();
    }
}
