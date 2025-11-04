<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Email extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ["email", "id", "parent_type", "created_at"];

    public function parent()
    {
        return $this->morphTo();
    }
}
