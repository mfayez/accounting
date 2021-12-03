<?php

namespace App;

/**
 * Eloquent class to describe the team_invitations table.
 *
 * automatically generated by ModelGenerator.php
 */
class TeamInvitations extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'team_invitations';

    protected $fillable = ['email', 'role'];

    public function teams()
    {
        return $this->belongsTo('App\Teams', 'team_id', 'id');
    }
}