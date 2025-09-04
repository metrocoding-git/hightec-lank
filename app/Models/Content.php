<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    //
    protected $table = 'contents';
    protected $fillable = ['file_name', 'content_type_id', 'youtube_link', 'upload_file', 'file_size'];

    public function content_type()
    {
        return $this->belongsTo(Content_Type::class);
    }
}
