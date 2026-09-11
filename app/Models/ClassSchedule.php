<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSchedule extends Model
{
    protected $fillable = ['class_id', 'schedule_date', 'start_time', 'end_time', 'material_taught'];
    protected $casts = ['schedule_date' => 'date'];
    public function classRoom(): BelongsTo { return $this->belongsTo(ClassRoom::class, 'class_id'); }
}
