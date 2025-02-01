<?php
namespace App\Http\Controllers;

use App\Helpers\TradeHelper;
use App\Models\Bstate;
use App\Models\Kredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class ProfileController extends Controller
{
    //
    public function getProfile()
    {
        $record = Kredential::where('email', Auth::user()->email)->first();

        if (! $record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $data = [
            'method'     => 'getInfo',
            'timestamp'  => '1578304294000',
            'recvWindow' => '1578303937000',
        ];

        if (! empty($record->key) || ! empty($record->secret)) {
            $response = TradeHelper::sendServer($record->key, $record->secret, $data);
            return response()->json($response->json());
        } else {
            return response()->json([
                'succsess' => '0',
                'message'  => 'key atau secret salah']);
        }

    }

    public function getOrderHistori()
    {
        $record = Kredential::where('email', Auth::user()->email)->first();

        if (! $record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $data = [
            'method'     => 'orderHistory',
            'pair'       => 'pgala_idr',
            'timestamp'  => '1578304294000',
            'recvWindow' => '1578303937000',
        ];

        if (! empty($record->key) || ! empty($record->secret)) {
            $response = TradeHelper::sendServer($record->key, $record->secret, $data);
            return response()->json($response->json());
        } else {
            return response()->json([
                'succsess' => '0',
                'message'  => 'key atau secret salah']);
        }

    }
    public function getOrder(Request $request)
    {
        $record = Kredential::where('email', Auth::user()->email)->first();

        if (! $record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $data = [
            'method'     => 'getOrder',
            'pair'       => 'pgala_idr',
            'order_id'   => $request->id,
            'timestamp'  => '1578304294000',
            'recvWindow' => '1578303937000',
        ];

        if (! empty($record->key) || ! empty($record->secret)) {
            $response = TradeHelper::sendServer($record->key, $record->secret, $data);
            return response()->json($response->json());
        } else {
            return response()->json([
                'succsess' => '0',
                'message'  => 'key atau secret salah']);
        }

    }
    public function getStatus()
    {
        $record = Bstate::where('email', Auth::user()->email)->
            where('state', 'off')->
            with('kredentials.jarings.pairs')->get();
        if (!$record->isEmpty()) {
            return response()->json([
                'succsess' => '1',
                'message'  => $record]);
        }
        return response()->json([
            'succsess' => '0',
            'message'  => 'data tidak ada']);

    }

}
