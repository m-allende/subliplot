<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Client;
use App\Models\Presentation;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $brands = Brand::count();
        $categories = Category::count();
        $clients = Client::count();
        $presentations = Presentation::count();
        $products = Product::count();
        $suppliers = Supplier::count();

        $purchases = Purchase::count();
        $sales = Sale::count();

        $dataPurchase = DB::table('purchases')
                    ->selectRaw('count(id) as contador,sum(total) as total, month(date) as mes')
                    ->groupBy('mes')
                    ->get();

        $arrPurchases = "";
        $arrPurchasesTotal = "";
        for ($i=1; $i <= 12; $i++) {
            $count = 0;
            $total = 0;
            foreach ($dataPurchase as $key => $value) {
                if($i == $value->mes){
                    $count = $value->contador;
                    $total = $value->total;
                }
            }
            $arrPurchases = $arrPurchases . ($arrPurchases==""?"":",").$count;
            $arrPurchasesTotal = $arrPurchasesTotal . ($arrPurchasesTotal==""?"":",").$total;
        }

        $dataSales = DB::table('sales')
                    ->selectRaw('count(id) as contador,sum(total) as total, month(date) as mes')
                    ->groupBy('mes')
                    ->get();

        $arrSales = "";
        $arrSalesTotal = "";
        for ($i=1; $i <= 12; $i++) {
            $count = 0;
            $total = 0;
            foreach ($dataSales as $key => $value) {
                if($i == $value->mes){
                    $count = $value->contador;
                    $total = $value->total;
                }
            }
            $arrSales = $arrSales . ($arrSales == ""?"":",").$count;
            $arrSalesTotal = $arrSalesTotal . ($arrSalesTotal==""?"":",").$total;
        }


        return view('home', ["brands" => $brands, "categories" => $categories, "clients" => $clients, "presentations" => $presentations,
                                "products" => $products, "purchases" => $purchases, "sales" => $sales, "suppliers" => $suppliers, "arrPurchases" => $arrPurchases, "arrPurchasesTotal" => $arrPurchasesTotal, "arrSales" => $arrSales, "arrSalesTotal" => $arrSalesTotal]);
    }
}
