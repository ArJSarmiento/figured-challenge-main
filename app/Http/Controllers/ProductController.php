<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

            if($products->isEmpty()) {
                return response()->json([
                    'message' => 'There are no products available.'
                ], 400);
            }

            $appliedProducts = [];
            $totalPrice = 0;

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

                    return response()->view('applied', [
                        'applied_products' => $appliedProducts,
                        'total_price' => $totalPrice
                    ], 200);
                }
            }

            DB::rollBack();
            return response()->json([
                'message' => 'The quantity is greater than the total quantity of the products.'
            ], 400);
        } catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}