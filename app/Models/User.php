<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Issuer;
use App\Models\Receiver;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'current_team_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
		'is_admin' => 'boolean',
		'is_reviewer' => 'boolean',
		'is_data_entry' => 'boolean',
		'is_eta' => 'boolean',
		'is_viewer' => 'boolean'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'is_admin', 'is_reviewer', 'is_data_entry', 'is_eta', 'is_viewer'
    ];

	public function getIsAdminAttribute(){
		return $this->current_team_id == 1;
	}
	public function getIsReviewerAttribute(){
		return $this->current_team_id == 2;
	}
	public function getIsDataEntryAttribute(){
		return $this->current_team_id == 3;
	}
	public function getIsEtaAttribute(){
		return $this->current_team_id == 4;
	}
	public function getIsViewerAttribute(){
		return $this->current_team_id == 5;
	}
    
	public function uploads()
    {
        return $this->hasMany('App\Models\Upload', 'userId', 'Id');
    }

	public function issuers()
	{
		return $this->belongsToMany(Issuer::class, 'user_issuer');
	}

	// public function receivers()
	// {
	// 	return $this->belongsToMany(Receiver::class, 'user_receiver');
	// }
}
