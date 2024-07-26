<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Word;
use App\Models\Pos;
use App\Models\Translation;
use Illuminate\Support\Facades\Validator;

class WordController extends Controller
{   
    /**
     * Backend - Admin related methods
     */

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

    // Update method
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'english_word' => 'required|string|max:255',
            'part_of_speech_id' => 'required|integer|exists:Pos,id',
            'translation' => 'required|string|max:255',
        ]);

        

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find the word to update
            $word = Word::findOrFail($id);

            // Update the word
            $word->update(['english_word' => $request->english_word]);

            // Handle translations
            $translation = $word->translations()
                ->where('part_of_speech_id', $request->part_of_speech_id)
                ->first();

        

            if ($translation) {
                // Update the existing translation
                $translation->update([
                    'translation' => $request->translation,
                ]);
            } else {
                // Create a new translation
                $word->translations()->create([
                    'part_of_speech_id' => $request->part_of_speech_id,
                    'translation' => $request->translation,
                ]);
            }

            return response()->json(['message' => 'Word updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating word: ' . $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $word = Word::findOrFail($id);
        $word->translations()->delete();
        $word->delete();

        return response()->json(['message' => 'Word deleted successfully']);
    }


    /* Frontend - Search related methods */

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
