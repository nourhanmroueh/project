<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class Comments extends Model
{
    use HasFactory,SoftDeletes;
     protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'item_id', 'comment'];

    public function comments() {

        return $this->hasMany(Comment::class);
    }

}
