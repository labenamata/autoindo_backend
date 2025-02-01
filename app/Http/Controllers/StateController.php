<?php

namespace App\Http\Controllers;

use App\Models\Bstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StateController extends Controller
{
    // Create
    public function store(Request $request)
    {
        $request->validate([
            'state' => 'nullable|string|max:255'
        ]);

        $email = $request->user()->email;

        Bstate::create([
            'email' => $email,
            'state' => $request->input('state')
        ]);

        return response()->json(['message' => 'Record created successfully.'], 201);
    }

    // Read
    public function index()
    {
        $records = Bstate::where('email', Auth::user()->email)->first();
        return response()->json($records);
    }

    // Update
    public function update(Request $request)
    {
        $record = Bstate::where('email', Auth::user()->email)->first();

        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $request->validate([
            'state' => 'nullable|string|max:255'
        ]);

        $record->update($request->only('state'));

        return response()->json(['message' => 'Record updated successfully.']);
    }

    // Delete
    public function destroy(Request $request)
    {
        $record = Bstate::where('email', Auth::user()->email)->first();

        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $record->delete();

        return response()->json(['message' => 'Record deleted successfully.']);
    }
}
