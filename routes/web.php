<?php

use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use App\Models\Bankrate;
use App\Models\Loanrequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LoanrequestController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('pages/loans/loanx','loanx');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();

 Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('dashboard');

 //my route am adding
   //handling members
Route::get('/home/member', function () {
    $members= Member::all();
    return view('admin/member/index',['members'=>$members]);
})->name('admindashboard');
Route::post('/registerMember',[MemberController::class, 'registerMember'] );
Route::get('/edit/{id}',[MemberController::class, 'showData'] );
//Route::post('updateMember',[MemberController::class, 'Update'] );
Route::post('/updateMember', [MemberController::class, 'update'])->name('updateMember');
Route::post('/uploadMember',[MemberController::class,'upload_member']);
//Route::get('/searchMember',[DepositController::class,'searchMember']);
Route::get('/search-member', [MemberController::class, 'searchMember'])->name('search.member');
Route::get('/active-members', [MemberController::class, 'getActiveMembers'])->name('active.members');
Route::get('/new-members', [MemberController::class, 'getNewMembers'])->name('new.members');

  //deposit
Route::get('/home/deposit', function () {
    $deposits= Deposit::all();
    return view('pages/deposits/deposit',['deposits'=>$deposits]);
})->name('depositdashboard');

Route::post('/upload',[DepositController::class,'upload']);
Route::get('/searchDeposit',[DepositController::class,'searchDeposit']);



// loanrequest
Route::get('/home/loanrequest',[LoanrequestController::class, 'getloanreq'])->name('loanreqdashboard');

//Route::get('pages/pendingloanrequest', [LoanrequestController::class, 'getPendingLoanRequests'])->name('pendingLoans');
Route::get('pages/pendingloanrequest', [LoanrequestController::class, 'getPendingLoanRequests'])->name('pendingloans');
// Route to display pending loan requests and perform the ranking
Route::get('/approvingloanrequests', [LoanrequestController::class, 'showPendingLoanRequests'])->name('rankloanrequests');

// bankrate
// Route::get('/constants/{rate}/edit', [LoanrequestController::class, 'edit'])->name('constants.edit');
Route::post('/updateRate', [LoanrequestController::class, 'updateRate']);
// loan approval
Route::post('/updateApprovals', [LoanRequestController::class, 'updateApprovals']);


// loans
Route::get('/home/loan', function () {
  $loans= Loan::all();
  return view('pages/loans/loan',['loans'=>$loans]);
})->name('loandashboard');






Route::get('pages/loans/completedloan', [LoanController::class, 'getCompletedLoans'])->name('completedLoans');
Route::get('pages/loans/undisbursedloan', [LoanController::class, 'getUndisbursedLoans'])->name('undisbursedLoans');
Route::get('pages/loans/delayedloan', [LoanController::class, 'getDelayedLoans'])->name('delayedLoans');
Route::get('pages/loans/activeloan', [LoanController::class, 'getActiveLoans'])->name('activeLoans');
Route::get('pages/pdf/loans', [LoanController::class, 'getPdf'])->name('loanspdf');


//pdf

// Route::get('pdf/loans', [PdfController::class, 'getLoans'])->name('pdfloans');







Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::patch('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::patch('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
});

Route::group(['middleware' => 'auth'], function () {
	Route::get('{page}', ['as' => 'page.index', 'uses' => 'App\Http\Controllers\PageController@index']);
});

