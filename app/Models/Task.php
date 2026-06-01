<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'user_id', 'title', 'description', 'status', 'priority'];
    public function project(): BelongsTo{
        return $this->belongsTo(Project::class);
    }
    public function assignee(): BelongsTo{
        return $this->belongsTo(User::class,'assignee_id');
    }

}
