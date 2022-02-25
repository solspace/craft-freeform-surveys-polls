<?php
/*
 * Freeform Surveys & Polls plugin for Craft CMS 3.x
 *
 * @link      https://solspace.com
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\Tests\SurveysPolls\Unit\SurveyResults\DataObjects;

use PHPUnit\Framework\TestCase;
use Solspace\SurveysPolls\SurveyResults\DataObjects\AnswerBreakdown;
use Solspace\SurveysPolls\SurveyResults\DataObjects\FieldTotals;

/**
 * @internal
 * @coversNothing
 */
class AnswerBreakdownTest extends TestCase
{
    private $mock;

    protected function setUp(): void
    {
        $this->mock = $this->createMock(FieldTotals::class);
    }

    public function testIncrementsVotes()
    {
        $breakdown = new AnswerBreakdown($this->mock, 'label', 'value');

        $this->assertEquals(0, $breakdown->getVotes());

        $breakdown->incrementVotes();

        $this->assertEquals(1, $breakdown->getVotes());

        $breakdown->incrementVotes(5);

        $this->assertEquals(6, $breakdown->getVotes());
    }

    public function testPercentage()
    {
        $this->mock->expects($this->exactly(2))
            ->method('getVotes')
            ->willReturn(70)
        ;

        $breakdown1 = new AnswerBreakdown($this->mock, 'label', 'value');
        $breakdown1->incrementVotes(25);

        $breakdown2 = new AnswerBreakdown($this->mock, 'label', 'value');
        $breakdown2->incrementVotes(45);

        $percentage1 = $breakdown1->getPercentage();
        $percentage2 = $breakdown2->getPercentage();

        $this->assertEquals(35.71, $percentage1);
        $this->assertEquals(64.29, $percentage2);
        $this->assertEquals(100, $percentage1 + $percentage2);
    }
}
