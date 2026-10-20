<?php

namespace App\Http\Controllers;

use App\Support\CrmReports;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmReportController extends Controller
{
    public function index(Request $request, CrmReports $reports): Response|JsonResponse|StreamedResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $current = (int) CarbonImmutable::now('Asia/Kolkata')->format('Y');
        $filters = $request->validate(['from_year' => ['nullable', 'integer', 'min:2000', 'max:'.$current],
            'to_year' => ['nullable', 'integer', 'min:2000', 'max:'.$current, 'gte:from_year'],
            'format' => ['nullable', Rule::in(['html', 'csv', 'svg'])],
            'section' => ['required_if:format,csv,svg', 'nullable', Rule::in(['years', 'conversions', 'months', 'applications_by_country', 'visas_by_country', 'counsellors'])]]);
        $from = (int) ($filters['from_year'] ?? max(2000, $current - 11));
        $to = (int) ($filters['to_year'] ?? $current);
        abort_if($from > $to || $to - $from > 30, 422, 'Choose a reporting range of up to 31 years.');
        $data = $reports->build($request->user(), $from, $to);
        if (in_array($filters['format'] ?? 'html', ['csv', 'svg'], true)) {
            Gate::authorize('crm.export');
        }
        if ($request->expectsJson()) {
            return response()->json($data, 200, ['Cache-Control' => 'private, no-store']);
        }
        if (($filters['format'] ?? 'html') === 'csv') {
            $rows = $data[$filters['section']];

            return response()->streamDownload(function () use ($rows): void {
                $stream = fopen('php://output', 'wb');
                if ($rows !== []) {
                    fputcsv($stream, array_keys($rows[0]), ',', '"', '');
                }
                foreach ($rows as $row) {
                    fputcsv($stream, array_map(fn ($value) => is_string($value) && preg_match('/^[\s]*[=+\-@]|^[\t\r\n]/u', $value) ? "'".$value : $value, $row), ',', '"', '');
                }
                fclose($stream);
            }, 'geic-report-'.$filters['section'].'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
        }
        if (($filters['format'] ?? 'html') === 'svg') {
            return response()->view('admin.crm.reports.chart', ['rows' => $data[$filters['section']], 'chartId' => $filters['section'], 'title' => ucwords(str_replace('_', ' ', $filters['section']))], 200,
                ['Content-Type' => 'image/svg+xml', 'Content-Disposition' => 'attachment; filename="geic-report-'.$filters['section'].'.svg"', 'Cache-Control' => 'private, no-store']);
        }

        return response()->view('admin.crm.reports.index', ['report' => $data, 'reports' => $reports], 200, ['Cache-Control' => 'private, no-store']);
    }
}
