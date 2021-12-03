<?php

namespace App;

/**
 * Eloquent class to describe the users table.
 *
 * automatically generated by ModelGenerator.php
 */
class Users extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'email_verified_at', 'password', 'two_factor_secret',
        'two_factor_recovery_codes', 'remember_token', 'current_team_id', 'profile_photo_path'];

    public function getDates()
    {
        return ['email_verified_at'];
    }
}