<?php

namespace App\Http\Requests\Admin;

use App\Support\ServiceContentNormalizer;
use Illuminate\Foundation\Http\FormRequest;

abstract class AdminServiceFormRequest extends FormRequest
{
    protected function prepareBaseServicePayload(): void
    {
        if ($this->has('delivery_category_id') && $this->input('delivery_category_id') === '') {
            $this->merge(['delivery_category_id' => null]);
        }

        foreach (['canonical_url', 'schema_type'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }

        $content = $this->input('content');
        if (is_array($content)) {
            $this->merge(['content' => ServiceContentNormalizer::normalizeForPersistence($content)]);
        }
    }
}
