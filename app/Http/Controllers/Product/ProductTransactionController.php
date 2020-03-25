<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;

class ProductTransactionController extends ApiController
{

    public function index(Product $product)
    {
        $transactions = $product->transactions;

        return $this->showAll($transactions);
    }


}
