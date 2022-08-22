<?php

namespace Fleetbase\Http\Requests;

use Fleetbase\Support\Http;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class FleetbaseRequest extends FormRequest
{
    /**
     * Form validation to throw JSON formatted errors if validation fails
     *
     * @param Illuminate\Contracts\Validation\Validator $validator;
     * @return Illuminate\Http\Exceptions\HttpResponseException;
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        if (Http::isInternalRequest()) {
            $response = [
                'errors' => [$errors->first()],
            ];

            // if more than one error display the others
            if ($errors->count() > 1) {
                $response['errors'] = collect($errors->all())
                    ->values()
                    ->toArray();
            }
        } else {
            $response = ['error' => $errors->first()];
            if ($errors->count() > 1) {
                $response['errors'] = collect($errors->all())
                    ->values()
                    ->toArray();
            }
        }

        return response()->json($response, 422);
    }

    public function responseWithErrors(Validator $validator)
    {
        return $this->failedValidation($validator);
    }
}
