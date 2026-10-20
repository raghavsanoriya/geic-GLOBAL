<?php

namespace App\Http\Controllers;

use App\Models\CrmApplication;
use App\Models\CrmContact;
use App\Models\CrmFollowUp;
use App\Models\CrmInvoice;
use App\Models\CrmProgram;
use App\Models\CrmStudent;
use App\Models\CrmTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CrmSearchController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        abort_unless(config('crm.enabled'), 404);
        Gate::authorize('crm.view');
        $data = $request->validate(['q' => ['nullable', 'string', 'min:2', 'max:120']]);
        $term = trim($data['q'] ?? '');
        $groups = [];
        if (mb_strlen($term) >= 2) {
            $like = '%'.$term.'%';
            $user = $request->user();
            $groups['Students'] = CrmStudent::visibleTo($user)->where(fn ($q) => $q->where('full_name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like))
                ->orderByDesc('id')->limit(12)->get(['id', 'full_name'])->map(fn ($row) => ['label' => $row->full_name, 'reference' => 'CRM-'.$row->id, 'url' => route('admin.crm.students.show', $row->id)]);
            $groups['Follow-ups'] = CrmFollowUp::visibleTo($user)->where('subject', 'like', $like)->orderByDesc('id')->limit(12)->get(['id', 'subject'])
                ->map(fn ($row) => ['label' => $row->subject, 'reference' => 'Follow-up #'.$row->id, 'url' => route('admin.crm.follow-ups.show', $row->id)]);
            $groups['Tasks'] = CrmTask::visibleTo($user)->where('title', 'like', $like)->orderByDesc('id')->limit(12)->get(['id', 'title'])
                ->map(fn ($row) => ['label' => $row->title, 'reference' => 'Task #'.$row->id, 'url' => route('admin.crm.tasks.show', $row->id)]);
            $groups['Applications'] = CrmApplication::visibleTo($user)->where('course', 'like', $like)->orderByDesc('id')->limit(12)->get(['id', 'course'])
                ->map(fn ($row) => ['label' => $row->course, 'reference' => 'Application #'.$row->id, 'url' => route('admin.crm.applications.show', $row->id)]);
            $groups['Programs'] = CrmProgram::where('is_active', true)->where('name', 'like', $like)->orderBy('name')->limit(12)->get(['id', 'name'])
                ->map(fn ($row) => ['label' => $row->name, 'reference' => 'Program #'.$row->id, 'url' => route('admin.crm.programs.index', ['q' => $row->name])]);
            $groups['Contacts'] = CrmContact::visibleTo($user)->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('company', 'like', $like))->orderBy('name')->limit(12)->get(['id', 'name'])
                ->map(fn ($row) => ['label' => $row->name, 'reference' => 'Contact #'.$row->id, 'url' => route('admin.crm.contacts.show', $row->id)]);
            if (Gate::allows('crm.finance.view')) {
                $groups['Invoices'] = CrmInvoice::visibleTo($user)->where('number', 'like', $like)->orderByDesc('id')->limit(12)->get(['id', 'number'])
                    ->map(fn ($row) => ['label' => $row->number, 'reference' => 'Invoice #'.$row->id, 'url' => route('admin.crm.invoices.show', $row->id)]);
            }
        }

        return $request->expectsJson() ? response()->json(['query' => $term, 'results' => $groups], 200, ['Cache-Control' => 'private, no-store'])
            : response()->view('admin.crm.search', compact('groups', 'term'), 200, ['Cache-Control' => 'private, no-store']);
    }
}
