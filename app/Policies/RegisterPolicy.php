<?php

namespace App\Policies;

use App\Models\Register;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegisterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Register $register)
    {
        return $user->id === $register->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Register $register)
    {
        return $user->id === $register->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Register $register)
    {
        return $user->id === $register->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Register $register)
    {
        return $user->id === $register->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Register $register)
    {
        return $user->id === $register->user_id;
    }
}
