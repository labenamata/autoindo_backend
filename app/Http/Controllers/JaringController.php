<?php
namespace App\Http\Controllers;

use App\Models\Jaring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JaringController extends Controller
{
    // Middleware to ensure authenticated users only
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Create a new record
    public function create(Request $request)
    {
        $user = Auth::user();

        // Validation
        $validator = Validator::make($request->all(), [
            'koin_id'  => 'required|string',
            'modal'    => 'required|string',
            'buy'      => 'required|string',
            'sell'     => 'required|string',
            'profit'   => 'nullable|string',
            'status'   => 'nullable|string',
            'order_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create a new Jaring record
        $jaring = Jaring::create([
            'email'   => $user->email, // Use authenticated user's email
            'koin_id' => $request->koin_id,
            'modal'   => $request->modal,
            'buy'     => $request->buy,
            'sell'    => $request->sell,
            'status'  => 'pending',
        ]);

        return response()->json(['success' => 'Record created successfully', 'data' => $jaring], 201);
    }

    public function createBatch(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'jarings'           => 'required|array',
            'jarings.*.koin_id' => 'required|string',
            'jarings.*.modal'   => 'required|string',
            'jarings.*.buy'     => 'required|string',
            'jarings.*.sell'    => 'required|string',
        ]);

        $postsData = collect($validated['jarings'])->map(function ($data) {
            return [
                'email'   => Auth::user()->email, // Use authenticated user's email
                'koin_id' => $data['koin_id'],
                'modal'   => $data['modal'],
                'buy'     => $data['buy'],
                'sell'    => $data['sell'],
                'status'  => 'pending',

            ];
        })->toArray();

        Jaring::insert($postsData);

        return response()->json(['success' => true, 'message' => 'Posts created successfully'], 201);
    }

    // Read (Retrieve) all records
    public function index()
    {

        $jaring = Jaring::where('email', Auth::user()->email)->with('pairs')->get();
        return response()->json(['data' => $jaring]);
    }

    public function search($id)
    {

        $jaring = Jaring::where('id', $id)->with('pairs')->first();
        return response()->json(['data' => $jaring]);

    }

    // Update a record by email
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'id'       => 'required|string|max:255',
            'koin_id'  => 'required|string',
            'modal'    => 'required|string',
            'buy'      => 'required|string',
            'sell'     => 'required|string',
            'profit'   => 'nullable|string',
            'status'   => 'nullable|string',
            'order_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find the record by email
        $jaring = Jaring::where('id', $request->id)->first();

        if (! $jaring) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Update the record
        $jaring->update($request->all());

        return response()->json(['success' => 'Record updated successfully', 'data' => $jaring]);
    }

    // Delete a record by email
    public function destroy(Request $request)
    {
        // Find the record by email
        $jaring = Jaring::find($request->id);

        if (! $jaring) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Delete the record
        $jaring->delete();

        return response()->json(['success' => 'Record deleted successfully']);
    }
}
