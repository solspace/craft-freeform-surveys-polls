<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls\services;

use Carbon\Carbon;
use craft\db\Query;
use craft\db\Table;
use Solspace\Freeform\Elements\Submission;
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
use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\MultipleValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\OptionsInterface;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\SurveysPolls\SurveyResults\DataObjects\AnswerBreakdown;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FieldTotals;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FormTotals;
use yii\base\Component;

class SurveyService extends Component
{
    private const ALLOWED_FIELD_TYPES = [
        CheckboxGroupField::class,
        RadioGroupField::class,
        MultipleSelectField::class,
        OpinionScaleField::class,
        RatingField::class,
        SelectField::class,
        TextField::class,
        TextareaField::class,
        EmailField::class,
        NumberField::class,
        PhoneField::class,
        RegexField::class,
        WebsiteField::class,
    ];

    private array $formTotalsCache = [];

    public function getFormTotals(Form $form): FormTotals
    {
        if (!isset($this->formTotalsCache[$form->getId()])) {
            $contentTable = Submission::getContentTableName($form);

            $fields = $this->getProcessableFields($form);
            $searchableFields = array_map(
                fn (FieldInterface $field) => 'sc.[['.Submission::getFieldColumnName($field).']]',
                $fields
            );

            $query = (new Query())
                ->select(['s.id', ...$searchableFields])
                ->from(Submission::TABLE.' s')
                ->innerJoin("{$contentTable} sc", 'sc.[[id]] = s.[[id]]')
                ->where([
                    's.[[formId]]' => $form->getId(),
                    's.[[isSpam]]' => false,
                ])
            ;

            $formTotals = new FormTotals($form);
            $fieldTotalsCollection = $formTotals->getFieldTotals();

            foreach ($fields as $field) {
                $fieldTotalsCollection->add(new FieldTotals($field));
            }

            foreach ($query->batch() as $results) {
                foreach ($results as $row) {
                    foreach ($fields as $field) {
                        $totals = $fieldTotalsCollection->get($field);
                        if (!$totals) {
                            continue;
                        }

                        $column = Submission::getFieldColumnName($field);
                        $valueArray = $row[$column] ?? null;

                        if ($field instanceof MultipleValueInterface) {
                            $valueArray = json_decode($valueArray, true);
                        }

                        if (!\is_array($valueArray)) {
                            $valueArray = [$valueArray];
                        }

                        $hasOptions = false;
                        if ($field instanceof OptionsInterface) {
                            $hasOptions = true;
                            foreach ($field->getOptionsAsKeyValuePairs() as $value => $label) {
                                if ('' === $value || null === $value) {
                                    continue;
                                }

                                $breakdown = $totals->getBreakdown()->get($value);
                                if (null === $breakdown) {
                                    $totals->getBreakdown()->add(new AnswerBreakdown($totals, $label, $value));
                                }
                            }
                        }

                        if ($field instanceof RatingField) {
                            $hasOptions = true;
                            for ($value = 1; $value <= $field->getMaxValue(); ++$value) {
                                $label = $value;

                                if ('' == $value || null == $value) {
                                    continue;
                                }

                                $breakdown = $totals->getBreakdown()->get($value);
                                if (null === $breakdown) {
                                    $totals->getBreakdown()->add(new AnswerBreakdown($totals, $label, $value));
                                }
                            }
                        }

                        if ($field instanceof OpinionScaleField) {
                            $hasOptions = true;
                            foreach ($field->getScales() as $scale) {
                                $value = $scale['value'];
                                $label = $scale['label'];

                                if ('' == $value || null == $value) {
                                    continue;
                                }

                                $breakdown = $totals->getBreakdown()->get($value);
                                if (null === $breakdown) {
                                    $totals->getBreakdown()->add(new AnswerBreakdown($totals, $label, $value));
                                }
                            }
                        }

                        if (empty($valueArray)) {
                            $totals->incrementSkipped();

                            continue;
                        }

                        foreach ($valueArray as $value) {
                            if ('' === $value || null === $value) {
                                $totals->incrementSkipped();

                                continue 2;
                            }

                            $breakdown = $totals->getBreakdown()->get($value);
                            if (null === $breakdown) {
                                if ($hasOptions) {
                                    continue 2;
                                }

                                $breakdown = new AnswerBreakdown($totals, $value, $value);
                                $totals->getBreakdown()->add($breakdown);
                            }

                            $breakdown->incrementVotes();
                        }
                    }
                }
            }

            $this->formTotalsCache[$form->getId()] = $formTotals;
        }

        return $this->formTotalsCache[$form->getId()];
    }

    public function getChartData(Form $form): array
    {
        $submissions = Submission::TABLE;
        $elements = Table::ELEMENTS;

        $query = (new Query())
            ->select([
                "COUNT({$submissions}.[[id]]) as count",
                "DATE({$submissions}.[[dateCreated]]) as dt",
            ])
            ->from(Submission::TABLE)
            ->groupBy('dt')
            ->where(["{$submissions}.[[formId]]" => $form->getId()])
            ->innerJoin(
                $elements,
                "{$elements}.[[id]] = {$submissions}.[[id]] AND {$elements}.[[dateDeleted]] IS NULL"
            )
            ->orderBy(['dt' => \SORT_ASC])
            ->indexBy('dt')
        ;

        $result = $query->column();

        $rangeStart = new Carbon('-60 days');
        $rangeEnd = new Carbon('now');

        $labels = $data = [];
        $dateContext = $rangeStart->copy();
        while ($dateContext->lte($rangeEnd)) {
            $count = (int) ($result[$dateContext->toDateString()] ?? 0);
            $labels[] = $dateContext->format('M j');
            $data[] = ['x' => $dateContext->toDateString(), 'y' => $count];
            $dateContext->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * @return FieldInterface[]
     */
    private function getProcessableFields(Form $form): array
    {
        $fieldList = [];
        foreach ($form->getLayout()->getStorableFields() as $field) {
            if (\in_array(\get_class($field), self::ALLOWED_FIELD_TYPES, true)) {
                $fieldList[] = $field;
            }
        }

        return $fieldList;
    }
}
