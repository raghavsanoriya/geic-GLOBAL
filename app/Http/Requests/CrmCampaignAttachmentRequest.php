<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;

class CrmCampaignAttachmentRequest extends CrmDocumentRequest
{
    public function authorize(): bool
    {
        return config('crm.enabled') && Gate::allows('crm.view') && Gate::allows('crm.marketing.manage');
    }

    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['category']);
        $rules['file'] = ['required', 'file', 'max:5120', 'mimetypes:application/pdf,image/jpeg,image/png', 'extensions:pdf,jpg,jpeg,png'];
        $rules['version'] = ['required', 'integer', 'min:1'];

        return $rules;
    }
}
