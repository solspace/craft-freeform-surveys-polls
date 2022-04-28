<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2021 Solspace
 */

namespace Solspace\SurveysPolls\FormTypes;

use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FieldTotals;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FormTotals;
use Solspace\SurveysPolls\SurveysPolls;

class Survey extends Form
{
    public static function getTypeName(): string
    {
        return 'Surveys & Polls';
    }

    public static function getPropertyManifest(): array
    {
        return [];
    }

    public function getSurveyResults(FieldInterface $field = null): FormTotals|FieldTotals|null
    {
        $formTotals = SurveysPolls::$plugin->surveys->getFormTotals($this);
        if (null === $field) {
            return $formTotals;
        }

        return $formTotals->getFieldTotals()->get($field);
    }
}
