<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\Tests\SurveysPolls\Unit\SurveyResults\Collections;

use PHPUnit\Framework\TestCase;
use Solspace\Freeform\Fields\CheckboxGroupField;
use Solspace\SurveysPolls\SurveyResults\Collections\AnswerBreakdownCollection;
use Solspace\SurveysPolls\SurveyResults\DataObjects\AnswerBreakdown;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FieldTotals;

/**
 * @internal
 * @coversNothing
 */
class AnswerBreakdownCollectionTest extends TestCase
{
    private $fieldTotals;

    private $collection;

    protected function setUp(): void
    {
        $fieldMock = $this->createMock(CheckboxGroupField::class);
        $this->fieldTotals = new FieldTotals($fieldMock);
        $this->collection = new AnswerBreakdownCollection();
    }

    public function testRankMethod()
    {
        $breakdown1 = $this->createBreakdown(5);
        $breakdown2 = $this->createBreakdown(2);
        $breakdown3 = $this->createBreakdown(10);
        $breakdown4 = $this->createBreakdown(1);

        $this->collection->rank();

        $this->assertEquals(2, $breakdown1->getRanking());
        $this->assertEquals(3, $breakdown2->getRanking());
        $this->assertEquals(1, $breakdown3->getRanking());
        $this->assertEquals(4, $breakdown4->getRanking());
    }

    public function testGetRankedTopFour()
    {
        $breakdown1 = $this->createBreakdown(5);
        $breakdown2 = $this->createBreakdown(2);
        $breakdown3 = $this->createBreakdown(10);
        $breakdown4 = $this->createBreakdown(1);
        $breakdown5 = $this->createBreakdown(21);
        $breakdown6 = $this->createBreakdown(14);

        $rankedCollection = $this->collection->cloneRanked(4);
        $expectedCollection = new AnswerBreakdownCollection();
        $expectedCollection
            ->add($breakdown5)
            ->add($breakdown6)
            ->add($breakdown3)
            ->add($breakdown1)
        ;

        $this->assertEquals($expectedCollection, $rankedCollection);
    }

    public function testGetRankedTop()
    {
        $breakdown1 = $this->createBreakdown(5);
        $breakdown2 = $this->createBreakdown(2);
        $breakdown3 = $this->createBreakdown(10);
        $breakdown4 = $this->createBreakdown(1);
        $breakdown5 = $this->createBreakdown(21);
        $breakdown6 = $this->createBreakdown(14);

        $rankedCollection = $this->collection->cloneRanked(true);
        $expectedCollection = new AnswerBreakdownCollection();
        $expectedCollection
            ->add($breakdown5)
            ->add($breakdown6)
            ->add($breakdown3)
            ->add($breakdown1)
            ->add($breakdown2)
            ->add($breakdown4)
        ;

        $this->assertEquals($expectedCollection, $rankedCollection);
    }

    public function testGetRankedBottomFour()
    {
        $breakdown1 = $this->createBreakdown(5);
        $breakdown2 = $this->createBreakdown(2);
        $breakdown3 = $this->createBreakdown(10);
        $breakdown4 = $this->createBreakdown(1);
        $breakdown5 = $this->createBreakdown(21);
        $breakdown6 = $this->createBreakdown(14);

        $rankedCollection = $this->collection->cloneRanked(-4);
        $expectedCollection = new AnswerBreakdownCollection();
        $expectedCollection
            ->add($breakdown4)
            ->add($breakdown2)
            ->add($breakdown1)
            ->add($breakdown3)
        ;

        $this->assertEquals($expectedCollection, $rankedCollection);
    }

    public function testGetRankedWorst()
    {
        $breakdown1 = $this->createBreakdown(5);
        $breakdown2 = $this->createBreakdown(2);
        $breakdown3 = $this->createBreakdown(10);
        $breakdown4 = $this->createBreakdown(1);
        $breakdown5 = $this->createBreakdown(21);
        $breakdown6 = $this->createBreakdown(14);

        $rankedCollection = $this->collection->cloneRanked(false);
        $expectedCollection = new AnswerBreakdownCollection();
        $expectedCollection
            ->add($breakdown4)
            ->add($breakdown2)
            ->add($breakdown1)
            ->add($breakdown3)
            ->add($breakdown6)
            ->add($breakdown5)
        ;

        $this->assertEquals($expectedCollection, $rankedCollection);
    }

    private function createBreakdown(int $votes): AnswerBreakdown
    {
        $breakdown = new AnswerBreakdown($this->fieldTotals, (string) $votes, (string) $votes);
        $breakdown->incrementVotes($votes);

        $this->collection->add($breakdown);

        return $breakdown;
    }
}
