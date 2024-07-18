<?php

namespace Fleetbase\RegistryBridge\Http\Requests;

use Fleetbase\Http\Requests\FleetbaseRequest;
use Fleetbase\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Request class to handle the addition of new registry users.
 *
 * Validates the request data for adding a new user to the registry.
 * Ensures that the company ID, user email, and password are provided and valid.
 * The company and user must exist and not be deleted.
 */
class RegistryAuthRequest extends FleetbaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return session('is_admin') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('valid_identity', function ($attribute, $value, $parameters, $validator) {
            return User::where('email', $value)
                       ->orWhere('username', $value)
                       ->whereNull('deleted_at')
                       ->exists();
        });

        return [
            'identity' => [Rule::requiredIf($this->isNotFilled('package')), 'valid_identity'],
            'package'  => ['nullable'],
        ];
    }
}
