<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinCompanionshipRequest extends FormRequest
{
    /**
     * Authorization is handled by CompanionshipPolicy@join in the controller.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * No additional fields needed — the meetup ID comes from the route.
     * This class exists to enforce auth and keep the controller clean.
     */
    public function rules(): array
    {
        return [];
    }
}
