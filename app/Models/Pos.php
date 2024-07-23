<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pos extends Model
{
    use HasFactory;

    protected $table = 'pos';
    protected $fillable = ['part_of_speech'];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'part_of_speech_id');
    }
}
