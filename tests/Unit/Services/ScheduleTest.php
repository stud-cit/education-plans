<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Support\Collection;
use App\ExternalServices\Asu\Schedule;

class ScheduleTest extends TestCase
{
    public function testListFiltersCorrectly()
    {
        $mockData = collect([
            ['education_level_id' => 1, 'study_term_id' => 1, 'name' => 'Math'],
            ['education_level_id' => 1, 'study_term_id' => 2, 'name' => 'Science'],
            ['education_level_id' => 2, 'study_term_id' => 1, 'name' => 'History'],
        ]);

        $scheduleMock = $this->getMockBuilder(Schedule::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $scheduleMock->method('getData')->willReturn($mockData);

        $result = $scheduleMock->list(1, 1);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('Math', $result->first()['name']);
    }

    public function testListReturnsEmptyCollectionWhenNoMatches()
    {
        $mockData = collect([
            ['education_level_id' => 1, 'study_term_id' => 1, 'name' => 'Math'],
        ]);

        $scheduleMock = $this->getMockBuilder(Schedule::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $scheduleMock->method('getData')->willReturn($mockData);

        $result = $scheduleMock->list(2, 2);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
