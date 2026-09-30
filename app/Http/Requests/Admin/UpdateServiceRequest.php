<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;

class UpdateServiceRequest extends AdminServiceFormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareBaseServicePayload();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Service $service */
        $service = $this->route('service');

        return ServiceFormRules::validationRules($service->getKey());
    }
}
