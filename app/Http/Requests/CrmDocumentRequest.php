<?php

namespace App\Http\Requests;

use App\Models\CrmApplication;
use App\Models\CrmStudent;
use App\Models\CrmVisa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CrmDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! config('crm.enabled') || ! Gate::allows('crm.view') || ! Gate::allows('crm.documents.manage')) {
            return false;
        }
        CrmStudent::visibleTo($this->user())->findOrFail($this->route('student'));

        return true;
    }

    public function rules(): array
    {
        return ['creation_key' => ['required', 'uuid'], 'label' => ['required', 'string', 'max:160'],
            'application_id' => ['nullable', 'integer', 'min:1', Rule::exists('crm_applications', 'id')->where('student_id', $this->route('student'))],
            'visa_id' => ['nullable', 'integer', 'min:1', Rule::exists('crm_visas', 'id')->where('student_id', $this->route('student'))],
            'category' => ['required', Rule::in(['education', 'employment', 'english_test', 'application', 'visa', 'passport', 'other'])],
            'file' => ['required', 'file', 'max:10240', 'mimetypes:application/pdf,image/jpeg,image/png', 'extensions:pdf,jpg,jpeg,png']];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $validator->errors()->hasAny(['application_id', 'visa_id'])) {
                $application = $this->filled('application_id') ? CrmApplication::find($this->integer('application_id')) : null;
                $visa = $this->filled('visa_id') ? CrmVisa::find($this->integer('visa_id')) : null;
                if ($application && $visa && $visa->application_id !== $application->id) {
                    $validator->errors()->add('visa_id', 'Choose a visa belonging to the selected application, or leave the application blank.');
                }
            }
            if ($validator->errors()->has('file') || ! $this->hasFile('file')) {
                return;
            }
            $path = $this->file('file')->getRealPath();
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
            $valid = false;
            if ($mime === 'application/pdf') {
                $handle = fopen($path, 'rb');
                $valid = $handle !== false && fread($handle, 5) === '%PDF-';
                if ($handle !== false) {
                    fclose($handle);
                }
            } elseif (in_array($mime, ['image/png', 'image/jpeg'], true)) {
                $size = @getimagesize($path);
                $valid = $size !== false && $size[0] > 0 && $size[1] > 0 && $size[0] * $size[1] <= 60000000 && ($size['mime'] ?? null) === $mime;
            }
            if (! $valid) {
                $validator->errors()->add('file', 'Upload a genuine PDF, JPEG or PNG document (images up to 60 megapixels).');
            }
        });
    }
}
