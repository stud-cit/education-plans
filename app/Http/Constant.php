<?php

namespace App\Http;

class Constant
{
    const PAGINATE = 15;

    const FORM_CONTROL = [
        'EXAM' => 1,
        'DIFFERENTIAL_TEST' => 2,
        'TEST' => 3,
        'PROTECTION' => 8,
        'NO_CERTIFICATIONS' => 10
    ];

    const NUMBER_HOURS_IN_CREDIT = 30;

    const INDIVIDUAL_TASKS = [
        'CONTROL_WORK' => 1,
        'COURSE_WORK' => 2,
        'NO_TASK' => 3,
    ];
}
