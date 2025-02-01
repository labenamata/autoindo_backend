<?php
namespace App\Http\Controllers;

use App\Models\Kredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KredentialController extends Controller
{
    /**
     * Create a new Kredential.
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function create(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'key'    => 'required|string|max:255',
            'secret' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create Kredential using Sanctum authenticated user's email
        $kredential         = new Kredential();
        $kredential->email  = Auth::user()->email;
        $kredential->key    = $request->key;
        $kredential->secret = $request->secret;
        $kredential->save();

        return response()->json(['message' => 'Kredential created successfully', 'data' => $kredential], 201);
    }

    /**
     * Retrieve all Kredentials.
     */
    public function index()
    {
        $kredentials = Kredential::where('email', Auth::user()->email)->first();

        return response()->json($kredentials);
    }

    /**
     * Update an existing Kredential.
     */
    public function update(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'key'    => 'required|string|max:255',
            'secret' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find the Kredential
        $kredential = Kredential::where('email', Auth::user()->email)->first();

        if (! $kredential) {
            return response()->json(['error' => 'Kredential not found'], 404);
        }

        // Update the Kredential
        $kredential->key    = $request->key;
        $kredential->secret = $request->secret;
        $kredential->save();

        return response()->json(['message' => 'Kredential updated successfully', 'data' => $kredential]);
    }

    /**
     * Delete a Kredential by ID.
     */
    public function destroy($id)
    {
        $kredential = Kredential::find($id);

        if (! $kredential) {
            return response()->json(['error' => 'Kredential not found'], 404);
        }

        $kredential->delete();

        return response()->json(['message' => 'Kredential deleted successfully']);
    }
}
