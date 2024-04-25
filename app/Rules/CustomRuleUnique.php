<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomRuleUnique implements ValidationRule
{
    protected $table;
    protected $column;
    protected $id;

    public function __construct($table, $column = null, $id = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @throws ValidationException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $record = DB::table($this->table)
            ->where($this->column ?: $attribute, $value)
            ->whereNull('deleted_at')
            ->first();
        if ($record && $record->id != $this->id) {
            throw ValidationException::withMessages([ucfirst($attribute . ' đã tồn tại.')]);
        }
    }
}
