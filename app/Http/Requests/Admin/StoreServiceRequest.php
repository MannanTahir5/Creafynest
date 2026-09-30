<?php

namespace App\Http\Requests\Admin;

class StoreServiceRequest extends AdminServiceFormRequest
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
        return ServiceFormRules::validationRules(null);
    }
}
