<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransfomer;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . CategoryTransfomer::class)->only(['store', 'update']);
    }

    public function index()
    {
        $categories = Category::all();

        return $this->showAll($categories);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required'],
        ];

        $this->validate($request, $rules);

        $newCategory = Category::create($request->all());

        return $this->showOne($newCategory, 201);
    }

    public function show(Category $category)
    {
        return $this->showOne($category);
    }


    public function update(Request $request, Category $category)
    {
        $category->fill($request->only([
            'name',
            'description',
        ]));

        if ($category->isClean()) {
            return $this->errorResponse('you need to specify any different value to update', 422);
        }

        $category->save();

        return $this->showOne($category);
    }


    public function destroy(Category $category)
    {
        $category->delete();

        return $this->showOne($category);
    }
}
