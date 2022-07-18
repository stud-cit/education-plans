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
    const INDIVIDUAL_TASKS_SHORT = [
        self::INDIVIDUAL_TASKS['CONTROL_WORK'] => 'кр',
        self::INDIVIDUAL_TASKS['COURSE_WORK'] => 'КР',
        self::INDIVIDUAL_TASKS['NO_TASK'] => 'БЗ'
    ];

    const ASU_ERRORS = [
        'ERROR_API',
        'ERROR_CABINET'
    ];
}
