<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\SurveyResults\Collections;

use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FieldTotals;

class FieldTotalsCollection extends AbstractCollection
{
    public function get(FieldInterface $field): ?FieldTotals
    {
        return $this->list[$field->getId()] ?? null;
    }

    public function add(FieldTotals $item)
    {
        $this->list[$item->getField()->getId()] = $item;
    }

    public function remove(FieldTotals $item)
    {
        if (\array_key_exists($item->getField()->getId(), $this->list)) {
            unset($this->list[$item->getField()->getId()]);
        }
    }
}
