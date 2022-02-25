<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\records;

use craft\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $userId
 * @property int    $formId
 * @property int    $fieldId
 * @property string $chartType
 */
class SurveyViewSettings extends ActiveRecord
{
    public const TABLE = '{{%freeform_surveys_view_settings}}';
    public const TABLE_STD = 'freeform_surveys_view_settings';

    public static function tableName(): string
    {
        return self::TABLE;
    }
}
