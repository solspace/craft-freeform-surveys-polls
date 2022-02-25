<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\Resources\bundles;

use Solspace\Freeform\Resources\Bundles\AbstractFreeformAssetBundle;

abstract class AbstractSurveyAssetBundle extends AbstractFreeformAssetBundle
{
    /**
     * {@inheritDoc}
     */
    protected function getSourcePath(): string
    {
        return '@Solspace/SurveysPolls/Resources';
    }
}
