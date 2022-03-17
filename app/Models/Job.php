<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'created_by_user_id', 'updated_by_user_id', 'title', 'description', 'location'];
    protected $guarded = ['id'];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
