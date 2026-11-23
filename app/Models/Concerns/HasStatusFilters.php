<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Status groups for request list pages (KPI tiles + ?status= filter).
 * The model defines STATUS_FILTERS: key => [label, [statuses…], tone, Font Awesome icon].
 */
trait HasStatusFilters
{
    public function scopeInStatusGroup(Builder $query, ?string $key): Builder
    {
        $group = static::STATUS_FILTERS[$key] ?? null;

        return $group ? $query->whereIn('status', $group[1]) : $query;
    }

    /** Totals per status group across all records (not just the current page), plus 'all'. */
    public static function statusGroupCounts(): array
    {
        $byStatus = static::query()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');

        return collect(static::STATUS_FILTERS)
            ->map(fn ($group) => (int) collect($group[1])->sum(fn ($status) => $byStatus[$status] ?? 0))
            ->put('all', (int) $byStatus->sum())
            ->all();
    }
}
