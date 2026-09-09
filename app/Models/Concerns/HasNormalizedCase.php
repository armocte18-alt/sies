<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Stores free-text fields in lowercase (the canonical form kept in the
 * database) and transparently title-cases them back when read, so views
 * never need to remember to format anything. Applies to every string
 * attribute on the model except the ones in EXCLUDED_KEYS/EXCLUDED_SUFFIXES
 * — ids, timestamps, emails, passwords, tokens, urls/links, and any
 * status-like flag, since those must keep their exact stored value.
 */
trait HasNormalizedCase
{
    /**
     * Attribute names never normalized, regardless of the model.
     */
    private static array $normalizedCaseExcludedKeys = [
        'password',
        'remember_token',
        'email',
        'avatar_path',
    ];

    /**
     * Substrings that exclude an attribute by name (case-insensitive).
     */
    private static array $normalizedCaseExcludedSubstrings = [
        '_id', 'id_', 'clave', 'token', 'url', 'link', 'estatus', 'activo', 'password',
    ];

    protected static function bootHasNormalizedCase(): void
    {
        static::saving(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if (is_string($value) && static::isNormalizedCaseField($key)) {
                    $model->attributes[$key] = mb_strtolower(trim($value), 'UTF-8');
                }
            }
        });
    }

    private static function isNormalizedCaseField(string $key): bool
    {
        if (in_array($key, self::$normalizedCaseExcludedKeys, true)) {
            return false;
        }

        foreach (self::$normalizedCaseExcludedSubstrings as $needle) {
            if (str_contains($key, $needle)) {
                return false;
            }
        }

        return true;
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($key !== $this->getKeyName() && is_string($value) && static::isNormalizedCaseField($key)) {
            return Str::title($value);
        }

        return $value;
    }
}
