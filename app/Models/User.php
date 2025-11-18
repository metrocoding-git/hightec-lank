<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role; // Import the Role model

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the branches associated with the user.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branches');
    }

    public function hasPermission($permissionName, $action = 'view')
    {
        if (!$this->role) {
            return false;
        }

        // Check if the permission exists for the role
        $permissionExists = $this->role->permissions()
                                       ->where('permission_name', $permissionName)
                                       ->exists();

        if (!$permissionExists) {
            return false;
        }

        // If a specific action is requested, check for it
        if ($action && $action !== 'view') { // 'view' is the default and often implied by permission existence
            return $this->role->permissions()
                              ->where('permission_name', $permissionName)
                              ->where(function ($query) use ($action) {
                                  $query->where('action_name', $action)
                                        ->orWhere('action_name', 'all'); // 'all' action grants all sub-permissions
                              })
                              ->exists();
        }

        return true; // If no specific action other than 'view' is requested, and permission exists
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'student_id',
        'status',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
}
