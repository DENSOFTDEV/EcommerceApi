<?php

namespace App\Http\Controllers\Seller;


use App\Http\Controllers\ApiController;
use App\Seller;


class SellerController extends ApiController
{

    public function index()
    {
        $sellers = Seller::has('products')->get();
        return $this->showAll($sellers);
    }


    public function show(Seller $seller)
    {
        return $this->showOne($seller);
    }


}
