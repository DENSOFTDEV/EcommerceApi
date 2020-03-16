<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;


class BuyerController extends ApiController
{

    public function index()
    {
        $buyers = Buyer::has('transactions')->get();
        return $this->showAll($buyers);
    }


    public function show(Buyer $buyer)
    {

        return $this->showOne($buyer);
    }

}
