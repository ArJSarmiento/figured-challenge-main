<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyProductsRequest;
use App\Models\Product;
use Exception;
use DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function applyProducts(ApplyProductsRequest $request)
    {
        $validated = $request->validated();
        $applyQuantity = $validated['quantity'];

        DB::beginTransaction();
        try {
            $products = Product::where('quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $appliedProducts = [];
            $totalPrice = 0;

            if($products->isEmpty()) {
                return Inertia::render('Applied', [
                    'applied_products' => $appliedProducts,
                    'total_price' => $totalPrice,
                    'errorModal' => true,
                    'errorMessage' => "There are no products available."
                ]);
            }

            foreach ($products as $product) {
                if ($applyQuantity >= $product->quantity) {
                    $appliedProducts[] = [
                        'quantity' => $product->quantity,
                        'price' => $product->unit_price,
                    ];
                    $totalPrice += $product->quantity * $product->unit_price;

                    $applyQuantity -= $product->quantity;
                    $product->update(['quantity' => 0]);
                } else {
                    $appliedProducts[] = [
                        'quantity' => $applyQuantity,
                        'price' => $product->unit_price,
                    ];
                    $totalPrice += $applyQuantity * $product->unit_price;

                    $product->update(['quantity' => $product->quantity - $applyQuantity]);
                    $applyQuantity = 0;
                }

                if ($applyQuantity == 0) {
                    DB::commit();

                    return Inertia::render('Applied', [
                        'applied_products' => $appliedProducts,
                        'total_price' => $totalPrice,
                        'errorModal' => false,
                        'errorMessage' => ""
                    ]);
                }
            }

            DB::rollBack();
            return Inertia::render('Applied', [
                'applied_products' => $appliedProducts,
                'total_price' => $totalPrice,
                'errorModal' => true,
                'errorMessage' => "Desired quantity is not available."
            ]);
        } catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}
