<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        $totalSum = $this->productService->getTotalSum();
        
        return view('products.index', compact('products', 'totalSum'));
    }

    public function show($id)
    {
        try {
            $product = $this->productService->getProduct($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product'
            ], 500);
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            $validated = $request->validated();
            $product = $this->productService->storeProduct($validated);
            
            return response()->json([
                'success' => true,
                'product' => $product,
                'totalSum' => $this->productService->getTotalSum()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving product'
            ], 500);
        }
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $product = $this->productService->updateProduct($id, $validated);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'product' => $product,
                'totalSum' => $this->productService->getTotalSum()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            
            return response()->json([
                'success' => true,
                'totalSum' => $this->productService->getTotalSum()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product'
            ], 500);
        }
    }
}