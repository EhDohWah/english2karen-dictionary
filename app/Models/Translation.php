<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;
    protected $fillable = ['word_id', 'part_of_speech_id', 'translation'];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }

    public function partOfSpeech()
    {
        return $this->belongsTo(Pos::class, 'part_of_speech_id');
    }
}
