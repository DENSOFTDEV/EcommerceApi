<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;


class SellerBuyerController extends ApiController
{

    public function index(Seller $seller)
    {
        $buyers = $seller->products()
            ->whereHas('transactions')
            ->with('transactions.buyer')
            ->get()
            ->pluck('transactions')
            ->collapse()
            ->pluck('buyer')
            ->unique('id')
            ->values();

        return $this->showAll($buyers);
    }

}
