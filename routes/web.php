<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\SiteController::class, 'index'])->name('index');

Route::get('/catalog', [App\Http\Controllers\SiteController::class, 'catalog'])->name('catalog');
Route::get('/contact', [App\Http\Controllers\SiteController::class, 'catalog'])->name('contact');
Route::get('/search', [App\Http\Controllers\SiteController::class, 'search'])->name('search');
Route::resource('/country', App\Http\Controllers\CountryController::class);
Route::resource('/region', App\Http\Controllers\RegionController::class);
Route::resource('/commune', App\Http\Controllers\CommuneController::class);

Route::get('/store/products/{product}/config', [\App\Http\Controllers\Store\ProductConfigController::class,'show'])
     ->name('store.product.config');
Route::get('/store/products/{product}/price', [\App\Http\Controllers\Store\ProductConfigController::class, 'price'])
    ->name('store.product.price');
// Endpoint para cantidades predefinidas de cada producto
Route::get('/store/product/{product}/quantities', [App\Http\Controllers\Store\ProductConfigController::class, 'quantities']);

Route::post('/store/orders/{order}/repeat', [App\Http\Controllers\Store\OrderController::class, 'repeat'])
    ->middleware('auth')
    ->name('store.orders.repeat');



Route::prefix('store/cart')->name('store.cart.')->group(function () {
    Route::post('add', [\App\Http\Controllers\Store\CartController::class, 'add'])->name('add');
    Route::delete('remove/{rowId}', [\App\Http\Controllers\Store\CartController::class, 'remove'])->name('remove');
    Route::post('clear', [\App\Http\Controllers\Store\CartController::class, 'clear'])->name('clear');   // <--
    Route::get('summary', [\App\Http\Controllers\Store\CartController::class, 'summary'])->name('summary');
    Route::post('update/{rowId}', [App\Http\Controllers\Store\CartController::class, 'update'])->name('update');

});

// === CHECKOUT ===
Route::prefix('store/checkout')->group(function () {
    Route::get('/', [App\Http\Controllers\Store\CheckoutController::class, 'index'])->name('store.checkout.index'); // paso 1
    Route::get('/step2', [App\Http\Controllers\Store\CheckoutController::class, 'step2'])->name('store.checkout.step2');
    Route::get('/payment', function () {return view('store.checkout.payment');})->name('store.checkout.payment');
    Route::post('/pay', function (\Illuminate\Http\Request $req) {
        return response()->json(['status'=>200, 'message'=>'OK']);
    })->name('store.checkout.pay');
    Route::post('/place', [\App\Http\Controllers\Store\CheckoutController::class, 'place'])->name('store.checkout.place');
    
    Route::get('/orders/{uid}',[\App\Http\Controllers\Store\CheckoutController::class, 'thankyou'])->name('store.orders.thankyou');

    // para invitados
    Route::get('/guest', [App\Http\Controllers\Store\CheckoutController::class, 'guest'])->name('store.checkout.guest');
    Route::post('/guest/save', [App\Http\Controllers\Store\CheckoutController::class, 'guestSave'])->name('store.checkout.guest.save');
});



Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('store.home');
})->name('dashboard');

// Admin (sólo admin)
Route::middleware(['auth', 'role:admin'])
->prefix('admin')
->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');
});

Route::get('/whatsapp/contact', function () {
    $num = env('WHATSAPP_NUMBER');
    $msg = urlencode('Hola! Me gustaría consultar sobre un trabajo personalizado.');
    return redirect("https://api.whatsapp.com/send?phone={$num}&text={$msg}");
})->name('whatsapp.contact');


Route::get('store/profile/check', [ProfileController::class, 'check'])->name('store.profile.check');

Route::prefix('store/profile')->middleware(['auth'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('store.profile.index');

    // Datos básicos
    Route::post('/update', [ProfileController::class, 'update'])->name('store.profile.update');
    Route::post('/avatar', [ProfileController::class, 'avatar'])->name('store.profile.avatar');
    Route::post('/password', [ProfileController::class, 'password'])->name('store.profile.password');
    
    // Comprobante (boleta/factura) por orden
    Route::get('/orders/{uid}/document', [ProfileController::class, 'document'])->name('store.profile.orders.document');


    // Direcciones
    Route::get('/addresses', [ProfileController::class, 'addresses'])->name('store.profile.addresses');
    Route::get('/addresses/{id}', [ProfileController::class, 'showAddress'])->name('store.profile.address.show');
    Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('store.profile.address.store');
    Route::delete('/addresses/{id}', [ProfileController::class, 'deleteAddress'])->name('store.profile.address.delete');

    // Teléfonos
    Route::get('/phones', [ProfileController::class, 'phones'])->name('store.profile.phones');
    Route::get('/phones/{id}', [ProfileController::class, 'showPhone'])->name('store.profile.phone.show');
    Route::post('/phones', [ProfileController::class, 'storePhone'])->name('store.profile.phone.store');
    Route::delete('/phones/{id}', [ProfileController::class, 'deletePhone'])->name('store.profile.phone.delete');

    Route::get('/orders',                    [\App\Http\Controllers\Store\ProfileOrdersController::class, 'index'])->name('store.profile.orders.index');
    Route::get('/orders/{order}',            [\App\Http\Controllers\Store\ProfileOrdersController::class, 'show'])->name('store.profile.orders.show');
});


Route::middleware(['auth','role:admin'])->group(function () {
    Route::resource('/user', App\Http\Controllers\UserController::class);
    Route::resource('/role', App\Http\Controllers\RoleController::class);

    Route::get('/sales', [App\Http\Controllers\SaleController::class, 'index'])->name('sale.index');
    Route::get('/sales/{id}', [App\Http\Controllers\SaleController::class, 'show'])->name('sale.show');
    Route::get('/sales/{id}/status',  [App\Http\Controllers\SaleController::class, 'getStatus'])->name('sale.status.show');
    Route::post('/sales/{id}/status', [App\Http\Controllers\SaleController::class, 'updateStatus'])->name('sale.status');

    Route::resource('/category', App\Http\Controllers\CategoryController::class);
    Route::resource('/product', App\Http\Controllers\ProductController::class);
    Route::resource('/attribute-type',  App\Http\Controllers\AttributeTypeController::class)->only(['index','store','update','destroy']);
    Route::resource('/attribute-value', App\Http\Controllers\AttributeValueController::class)->only(['index','store','update','destroy']);

    Route::get('/product-prices', [App\Http\Controllers\ProductPriceController::class, 'index'])->name('product-prices.index');
    Route::get('/product-prices/load/{product}', [App\Http\Controllers\ProductPriceController::class, 'loadCombinations']);
    Route::post('/product-prices', [App\Http\Controllers\ProductPriceController::class, 'store']);


    // Para Select2 de tipos (combo en Valores)
    Route::get('/attribute-type/options', [App\Http\Controllers\AttributeValueController::class,'typeOptions']);
    Route::get ('product/{product}/attributes', [\App\Http\Controllers\ProductAttributeController::class, 'show'])->name('product.attributes.show');
    Route::post('product/{product}/attributes', [\App\Http\Controllers\ProductAttributeController::class, 'update'])->name('product.attributes.update');
});

// Rutas de autenticación de Breeze
require __DIR__.'/auth.php';
