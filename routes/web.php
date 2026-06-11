<?php
use Braintree\Gateway;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController; // <-- Zadna el Controller taba3 el Services hon
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
Route::get('/', function () {
    return view('welcome');
});

// Ghayyerna hal route kermel yb3at el balance automatic
Route::get('/dashboard', function () {
    $user = auth()->user();
    $balance = $user->balance ?? 0; 

    // Mnerba7 l-Braintree Client Token kermel l-Drop-in UI bl-frontend
    $gateway = new Gateway([
    'environment' => env('BRAINTREE_ENV', 'sandbox'),
    'merchantId'  => env('BRAINTREE_MERCHANT_ID'),  // I uppercase!
    'publicKey'   => env('BRAINTREE_PUBLIC_KEY'),   // K uppercase!
    'privateKey'  => env('BRAINTREE_PRIVATE_KEY')   // K uppercase!
]);
    
    $clientToken = $gateway->clientToken()->generate();

    // Return view wa7de kfye m3 compact lal balance wal token
    return view('dashboard', compact('balance', 'clientToken'));
    
})->middleware(['auth', 'verified'])->name('dashboard');

// --- ROUTES L-SERVICES (Zidnahon hon) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/transactions/history', [TransactionController::class, 'showTransactionHistory'])->name('transaction.history');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
    // Show all mother services (where parent_id is null)
    Route::get('/services/mother', [ServiceController::class, 'indexMother']);
    
    // Show all services (mother + children)
    Route::get('/services', [ServiceController::class, 'index']);
    
    // Show specific service details along with its children (used by Alpine.js fetch)
    Route::get('/services/{id}', [ServiceController::class, 'showService']);

    Route::post('/services/purchase', [PurchasesController::class, 'store'])->name('services.purchase');
});
// ----------------------------------------

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';