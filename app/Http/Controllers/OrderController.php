<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Ingredient;
use App\Models\Order;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    /**
     * Store a new order.
     *
     * @param \App\Http\Requests\OrderRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {

        $order = Order::create();
        $order->products()->attach($request->products);
        $order->updateStock();
        $lowStockIngredients = Ingredient::lowStock();
        foreach ($lowStockIngredients as $ingredient) {
            if(!$ingredient->order_confirmation_email_sent) {
                Notification::route('mail', config('app.merchant_email'))->notify(new LowStockNotification($ingredient));
                $ingredient->order_confirmation_email_sent = true;
                $ingredient->save();
                \Log::info($ingredient);
            }
        }
        return response()->json(['message' => 'Order stored and ingredients updated successfully.'], 201);
    }
}
