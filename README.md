# BillPlzv3

Before we get started, check out https://www.billplz.com/api

Require this package with composer using the following command:

    composer require "hazelnuts23\BillPlzv3:1.0.*"

After updating composer, add the `BillPlzv3ServiceProvider` to the `providers` array in `config/app.php`

    hazelnuts23\BillPlzv3\BillPlzv3ServiceProvider::class,

And `BillPlz` alias to the `aliases` array in `config/app.php`

    'BillPlz' => hazelnuts23\BillPlzv3\Billplzv3::class,

Run `php artisan vendor:publish` and `config/billplz.php` will be created for you to configure BillPlz API.
  
Sample Usage

``` php
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


```


- I would like to thank and give credit to Kidino for his BillPlz script and I made some improvement and support for BillPlz API v3 -
