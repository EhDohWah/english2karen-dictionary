<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartsOfSpeechSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //inserting parts of speech 
        $partsOfSpeech = [
            ['part_of_speech' => 'noun'],
            ['part_of_speech' => 'pronoun'],
            ['part_of_speech' => 'verb'],
            ['part_of_speech' => 'adjective'],
            ['part_of_speech' => 'adverb'],
            ['part_of_speech' => 'preposition'],
            ['part_of_speech' => 'conjunction'],
            ['part_of_speech' => 'interjection'],
        ];

        DB::table('pos')->insert($partsOfSpeech);
    }
}
