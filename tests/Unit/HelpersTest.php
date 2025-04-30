<?php

namespace Tests\Unit;

use App\Helpers\Helpers;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    /**
     * A get title for education form.
     *
     * @return void
     */
    public function test_title_form_education()
    {
        $title = Helpers::getTitleFormEducation(2023);
        $title2 = Helpers::getTitleFormEducation(2024);
        $title3 = Helpers::getTitleFormEducation(2025);

        $this->assertEquals('Фрма навчання', $title);
        $this->assertEquals('Форма здобуття освіти', $title2);
        $this->assertEquals('Форма здобуття освіти', $title3);
    }
}
