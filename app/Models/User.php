<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Partner;
use Coderflex\LaravelTicket\Concerns\HasTickets;
use Coderflex\LaravelTicket\Contracts\CanUseTickets;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as IsAuditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Auditable, CanUseTickets
{
	use HasFactory, Notifiable, IsAuditable, HasRoles, HasTickets, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'has_2fa_enabled',
		'google2fa_secret',
		'google2fa_url',
		'partner_id',
		'role_id',
		'is_admin',
		'password_changed_at'
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	public static function boot()
	{
		parent::boot();
		// self::addGlobalScope(new PartnerScope); // Don't ever enable this. Left as an example for you not to do it.
		// static::creating(function ($user) {
		// 	$user->google2fa_secret = Crypt::encrypt($user->google2fa_secret);
		// });
	}

	public function partner()
	{
		return $this->belongsTo(Partner::class);
	}

	public function getRedirectRoute()
	{
		if ($this->role && $this->role->name == "Finance Dashboard") {
			return route('dashboard.finance-dashboard');
		}
		return route('dashboard.index');
	}

	public function role()
	{
		return $this->belongsTo(Role::class, 'role_id');
	}

	public function hasPermission($permission)
	{
		return $this->hasPermissionTo($permission);
	}
}
