<?php

namespace App\Http\Middleware;

use App\Models\CrmInboundForm;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class CrmInboundAudit
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('crm.enabled')) {
            return $next($request);
        }
        $form = CrmInboundForm::where('form_key', $request->route('formKey'))->first();
        if (! $form) {
            return $next($request);
        }
        try {
            $response = $next($request);
        } catch (ValidationException $exception) {
            $this->record($form->id, 422, array_keys($exception->errors()));
            throw $exception;
        } catch (Throwable $exception) {
            $this->record($form->id, $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500);
            throw $exception;
        }
        $errors = $response instanceof JsonResponse ? ($response->getData(true)['errors'] ?? []) : [];
        $this->record($form->id, $response->getStatusCode(), is_array($errors) ? array_keys($errors) : []);

        return $response;
    }

    private function record(int $formId, int $status, array $invalidFields = []): void
    {
        $outcome = match ($status) {
            201 => 'saved',
            202 => 'duplicate_review',
            401 => 'invalid_credentials',
            409 => 'idempotency_conflict',
            422 => 'validation_rejected',
            default => $status >= 500 ? 'server_error' : 'rejected',
        };
        $fields = array_values(array_intersect($invalidFields, [...array_keys(CrmInboundForm::FIELDS), 'idempotency_key']));
        try {
            DB::table('crm_inbound_attempts')->insert([
                'form_id' => $formId, 'response_status' => $status, 'outcome' => $outcome,
                'invalid_fields' => $fields === [] ? null : json_encode($fields, JSON_THROW_ON_ERROR), 'created_at' => now(),
            ]);
        } catch (Throwable) {
            report(new \RuntimeException('An inbound API attempt could not be audited.'));
        }
    }
}
