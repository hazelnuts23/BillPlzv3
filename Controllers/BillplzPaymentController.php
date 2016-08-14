<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use hazelnuts23\BillPlzv3\Billplzv3;

class BillPlzPaymentController extends Controller
{
    public function pay(Request $request)
    {
        $title = $request->input('title');
        $price = str_replace(".", "", $request->input('price'));
        $fullname = $request->input('fullname');
        $nric = $request->input('nric');
        $email = $request->input('email');
        $telno = $request->input('telno');
        $description = $request->input('description');

        $bplz = new Billplzv3([
            'api_key' => config('billplz.'.env('APP_ENV').'.api_key'),
            'host' => config('billplz.'.env('APP_ENV').'.api_endpoint')
        ]);
        $bplz->set_data('title', $title);
        $result = $bplz->create_collection();
        $result = json_decode($result);
        $id = $result->id;

        $callback_url = url('/payment/processing');
        $redirect_url = url('/payment/complete/');

        $secret_key = str_random(42);

        $bplz->set_data(array(
            'collection_id' => $id,
            'description' => $description,
            'amount' => $price,
            'name' => $fullname,
            'email' => $email,
            'mobile' => $telno,
            'callback_url' => $callback_url,
            'redirect_url' => $redirect_url,
            'metadata[token]' => $secret_key,
            'metadata[nric]' => $nric
        ));

        $result = json_decode($bplz->create_bill());
        return response()->json($result);
    }
}
