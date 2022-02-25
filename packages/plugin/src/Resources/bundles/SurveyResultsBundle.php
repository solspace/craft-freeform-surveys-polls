<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\Resources\bundles;

class SurveyResultsBundle extends AbstractSurveyAssetBundle
{
    public function getScripts(): array
    {
        return [
            'js/app/vendor.js',
            'js/app/survey-results.js',
        ];
    }
}
