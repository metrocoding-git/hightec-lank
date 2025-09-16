<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareContentList extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'send_to',
        'title',
        'description',
        'save_date',
        'valid_date'
    ];

    // Belongs to role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Many-to-many with users
    public function users()
    {
        return $this->belongsToMany(User::class, 'share_content_list_user');
    }

    // Many-to-many with contents
    public function contents()
    {
        return $this->belongsToMany(Content::class, 'share_content_list_content');
    }
}
