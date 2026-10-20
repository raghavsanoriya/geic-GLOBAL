<?php

namespace App\Http\Middleware;

use App\Models\CrmInboundForm;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrmInboundCors
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! preg_match('~^api/v1/inbound-forms/([A-Za-z0-9]{24})/submissions$~D', $request->path(), $matches)) {
            return $next($request);
        }
        if (! config('crm.enabled')) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $origin = $request->header('Origin');
        if (! $origin) {
            return $next($request);
        }
        $form = CrmInboundForm::where('form_key', $matches[1])->where('is_active', true)->first();
        if (! $form || ! in_array($origin, $form->allowed_origins ?? [], true)) {
            return response()->json(['message' => 'Origin not allowed.'], 403);
        }
        $response = $request->isMethod('OPTIONS') ? response('', 204) : $next($request);
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-API-KEY, Idempotency-Key');
        $response->headers->remove('Access-Control-Allow-Credentials');
        $response->headers->set('Vary', 'Origin', false);
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }
}
