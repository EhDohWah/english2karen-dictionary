<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Word;
use App\Models\Pos;
use App\Models\Translation;

class WordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_word_with_valid_part_of_speech(): void
    {
        $pos = Pos::create(['part_of_speech' => 'noun']);
        $word = Word::create(['english_word' => 'apple']);
        $translation = Translation::create([
            'word_id' => $word->id,
            'part_of_speech_id' => $pos->id,
            'translation' => 'old',
        ]);

        $response = $this->putJson("/api/words/{$word->id}", [
            'english_word' => 'apple',
            'part_of_speech_id' => $pos->id,
            'translation' => 'new',
        ]);

        $response->assertStatus(200);
        $this->assertSame('new', $translation->fresh()->translation);
    }
}
