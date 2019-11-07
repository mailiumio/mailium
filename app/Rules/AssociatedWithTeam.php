<?php

namespace App\Rules;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Rule;

class AssociatedWithTeam implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $team = Team::find($value);

        if (!$team) {
            return false;
        }
        
        return Auth::user()->isAssociatedWith($team);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
