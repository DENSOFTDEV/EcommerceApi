<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransfomer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;


class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransfomer::class)->only(['store', 'update']);
    }

    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }


    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);
    }


    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'qunatity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image' => 'image',
        ];

        $this->validate($request, $rules);

        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));

        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        if ($request->hasFile('image')) {
            Storage::delete($product->image);

            $product->image = $request->image->store('');
        }

        if ($product->isClean()) {
            return $this->errorResponse('you need to specify a different value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw  new HttpException(422, "The specified seller is not the actual seller of the product");
        }
    }

    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }
}
