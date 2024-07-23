<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Word;
use App\Models\Pos;
use App\Models\Translation;

class WordController extends Controller
{   

    // show the input word index page
    public function createIndex()
    {
        $partsOfSpeech = Pos::all();
        return view('create_word', compact('partsOfSpeech'));
    }
    
    // Fetch words with pagination
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of items per page
        $words = Word::with('translations.partOfSpeech')->paginate($perPage);
        return response()->json($words);
    }

    // Store a new word
    public function store(Request $request)
    {
        $request->validate([
            'english_word' => 'required|string|max:255|unique:words,english_word',
            'part_of_speech_id' => 'required|exists:pos,id',
            'translation' => 'required|string'
        ]);

        $word = Word::create([
            'english_word' => $request->english_word
        ]);

        Translation::create([
            'word_id' => $word->id,
            'part_of_speech_id' => $request->part_of_speech_id,
            'translation' => $request->translation
        ]);

        return response()->json(['message' => 'Word added successfully!'], 201);
    }

    // search index
    public function searchIndex()
    {
        return view('search');
    }

    // method for searching word
    public function search(Request $request)
    {

        // Handle autocomplete
        if ($request->input('autocomplete')) {
            $request->validate(['q' => 'required|string|max:255']);
            $searchTerm = $request->input('q');
            $words = Word::where('english_word', 'LIKE', "%{$searchTerm}%")
                         ->pluck('english_word')
                         ->toArray();

            return response()->json(['suggestions' => $words]);
        }

        // Handle full search
        $request->validate(['q' => 'required|string|max:255']);
        $searchTerm = $request->input('q');
        $word = Word::where('english_word', 'LIKE', "%{$searchTerm}%")
                    ->with(['translations.partOfSpeech'])
                    ->first();
        
        if (!$word) {
            return response()->json(['error' => 'Word not found'], 404);
        }

        return response()->json($word);
    }
}
