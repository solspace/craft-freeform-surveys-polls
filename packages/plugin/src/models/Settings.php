<?php

namespace Solspace\SurveysPolls\models;

use craft\base\Model;
use Solspace\Freeform\Fields\CheckboxGroupField;
use Solspace\Freeform\Fields\EmailField;
use Solspace\Freeform\Fields\MultipleSelectField;
use Solspace\Freeform\Fields\NumberField;
use Solspace\Freeform\Fields\Pro\OpinionScaleField;
use Solspace\Freeform\Fields\Pro\PhoneField;
use Solspace\Freeform\Fields\Pro\RatingField;
use Solspace\Freeform\Fields\Pro\RegexField;
use Solspace\Freeform\Fields\Pro\WebsiteField;
use Solspace\Freeform\Fields\RadioGroupField;
use Solspace\Freeform\Fields\SelectField;
use Solspace\Freeform\Fields\TextareaField;
use Solspace\Freeform\Fields\TextField;

class Settings extends Model
{
    public const CHART_HORIZONTAL = 'Horizontal';
    public const CHART_VERTICAL = 'Vertical';
    public const CHART_PIE = 'Pie';
    public const CHART_DONUT = 'Donut';
    public const CHART_HIDDEN = 'Hidden';
    public const CHART_TEXT = 'Text';

    /** @var string[] */
    public $chartDefaults;

    /** @var bool */
    public $highlightHighest;

    public function __construct($config = [])
    {
        $this->highlightHighest = true;
        $this->chartDefaults = [
            CheckboxGroupField::getFieldType() => self::CHART_HORIZONTAL,
            RadioGroupField::getFieldType() => self::CHART_HORIZONTAL,
            SelectField::getFieldType() => self::CHART_HORIZONTAL,
            MultipleSelectField::getFieldType() => self::CHART_HORIZONTAL,
            OpinionScaleField::getFieldType() => self::CHART_VERTICAL,
            RatingField::getFieldType() => self::CHART_VERTICAL,
            TextField::getFieldType() => self::CHART_TEXT,
            TextareaField::getFieldType() => self::CHART_TEXT,
            EmailField::getFieldType() => self::CHART_TEXT,
            NumberField::getFieldType() => self::CHART_TEXT,
            PhoneField::getFieldType() => self::CHART_TEXT,
            RegexField::getFieldType() => self::CHART_TEXT,
            WebsiteField::getFieldType() => self::CHART_TEXT,
        ];

        parent::__construct($config);
    }

    public function getLabels(): array
    {
        return [
            CheckboxGroupField::getFieldType() => CheckboxGroupField::getFieldTypeName(),
            RadioGroupField::getFieldType() => RadioGroupField::getFieldTypeName(),
            SelectField::getFieldType() => SelectField::getFieldTypeName(),
            MultipleSelectField::getFieldType() => MultipleSelectField::getFieldTypeName(),
            OpinionScaleField::getFieldType() => OpinionScaleField::getFieldTypeName(),
            RatingField::getFieldType() => RatingField::getFieldTypeName(),
            TextField::getFieldType() => TextField::getFieldTypeName(),
            TextareaField::getFieldType() => TextareaField::getFieldTypeName(),
            EmailField::getFieldType() => EmailField::getFieldTypeName(),
            NumberField::getFieldType() => NumberField::getFieldTypeName(),
            PhoneField::getFieldType() => PhoneField::getFieldTypeName(),
            RegexField::getFieldType() => RegexField::getFieldTypeName(),
            WebsiteField::getFieldType() => WebsiteField::getFieldTypeName(),
        ];
    }

    public function getCharts(): array
    {
        $genericCharts = [
            self::CHART_HORIZONTAL => self::CHART_HORIZONTAL,
            self::CHART_VERTICAL => self::CHART_VERTICAL,
            self::CHART_PIE => self::CHART_PIE,
            self::CHART_DONUT => self::CHART_DONUT,
            self::CHART_HIDDEN => self::CHART_HIDDEN,
        ];

        $textCharts = [
            self::CHART_TEXT => self::CHART_TEXT,
            self::CHART_HIDDEN => self::CHART_HIDDEN,
        ];

        return [
            CheckboxGroupField::getFieldType() => $genericCharts,
            RadioGroupField::getFieldType() => $genericCharts,
            SelectField::getFieldType() => $genericCharts,
            MultipleSelectField::getFieldType() => $genericCharts,
            OpinionScaleField::getFieldType() => $genericCharts,
            RatingField::getFieldType() => $genericCharts,
            TextField::getFieldType() => $textCharts,
            TextareaField::getFieldType() => $textCharts,
            EmailField::getFieldType() => $textCharts,
            NumberField::getFieldType() => $textCharts,
            PhoneField::getFieldType() => $textCharts,
            RegexField::getFieldType() => $textCharts,
            WebsiteField::getFieldType() => $textCharts,
        ];
    }
}
