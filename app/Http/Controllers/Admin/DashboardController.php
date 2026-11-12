<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'total' => Product::count(),
            'discounted' => Product::whereNotNull('discount_price')->count(),
            'recent' => Product::latest('id')->take(5)->get(),
        ]);
    }
}
