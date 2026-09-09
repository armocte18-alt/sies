<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Stores text in lowercase (the canonical form kept in the database) while
 * letting views render it in a human-friendly case. A model using this trait
 * must define a $normalizedCase array of attribute names to lowercase on
 * save.
 */
trait HasNormalizedCase
{
    protected static function bootHasNormalizedCase(): void
    {
        static::saving(function ($model) {
            foreach ($model->normalizedCaseFields() as $field) {
                if (is_string($model->{$field})) {
                    $model->{$field} = mb_strtolower(trim($model->{$field}));
                }
            }
        });
    }

    protected function normalizedCaseFields(): array
    {
        return $this->normalizedCase ?? [];
    }

    /**
     * Title-cased version of a normalized field, for display only.
     */
    public function display(string $field): string
    {
        return Str::title((string) $this->{$field});
    }
}
