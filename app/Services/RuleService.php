<?php

namespace App\Services;

use App\Models\Rule;

class RuleService
{
	/**
	 * Get a rule by key and date.
	 *
	 * @param string $key
	 * @param string $date
	 * @return string
	 */
	public static function getRule(string $key, $date): string
	{
		$rules = Rule::select('key', 'label', 'date')->where('key', $key)->whereYear('date', $date)->first();

		if ($rules) {
			return $rules->label;
		}

		return Rule::select('key', 'label', 'date')->where('key', $key)->first()->label ?? '';
	}

	/**
	 * Get all rules.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getAllRules()
	{
		return \App\Models\Rule::all();
	}
}
