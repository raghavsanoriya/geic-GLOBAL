<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmTableExport
{
    /** @param list<string> $headings */
    public function respond(Builder|QueryBuilder $query, string $format, string $title, string $filename, array $headings, Closure $row, ?string $keyColumn = null): Response|StreamedResponse
    {
        Gate::authorize('crm.export');
        if ($format === 'print') {
            abort_if((clone $query)->count() > 1000, 422, 'Narrow the filters to at most 1,000 records before printing.');

            return response()->view('admin.crm.print-table', [
                'title' => $title, 'headings' => $headings,
                'rows' => $query->get()->map($row),
            ], 200, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
        }

        $keyColumn ??= $query instanceof Builder ? $query->getModel()->getQualifiedKeyName() : 'id';

        return response()->streamDownload(function () use ($query, $headings, $row, $keyColumn): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, $headings, ',', '"', '');
            foreach ($query->reorder()->lazyById(200, $keyColumn, 'id') as $record) {
                fputcsv($stream, array_map(function ($value): string {
                    $value = (string) ($value ?? '');

                    return preg_match('/^[\s]*[=+\-@]|^[\t\r\n]/u', $value) ? "'".$value : $value;
                }, $row($record)), ',', '"', '');
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
