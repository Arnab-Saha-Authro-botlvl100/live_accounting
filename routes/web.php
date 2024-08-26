<?php

use App\Http\Controllers\ADMController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AirlineController;
use App\Http\Controllers\DeporteeController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceivePaymentController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\ReissueController;
use App\Http\Controllers\MoneyTransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketRefundController;
use App\Http\Controllers\UmrahController;
use App\Http\Controllers\VoidController;
use App\Http\Controllers\AdminController;

use App\Models\Deportee;
use App\Models\Ticket;
use App\Models\Agent;
use App\Models\Supplier;
use App\Models\Receiver;
use App\Models\Payment;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Http\Controllers\SslCommerzPaymentController;


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

Route::get('/', function () {
    return view('welcome');
});


// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END

Route::get('/dashboard', function () {
   
        
    $current_date = new DateTime();
    $start_date = clone $current_date; // Today
    $end_date = $current_date->modify('+2 days'); // Next 4 days

    $closetickets = Ticket::where([
        ['user', Auth::id()],
        ['flight_date', '>=', $start_date->format('Y-m-d')],
        ['flight_date', '<=', $end_date->format('Y-m-d')]
    ])->get();

    foreach ($closetickets as $ticket){
        $ticket->agent = Agent::where('id', $ticket->agent)->value('name');
        $ticket->supplier = Supplier::where('id', $ticket->supplier)->value('name');
    }

    $current_date = Carbon::now()->toDateString();
    $total_receive = 0;
    $total_pay = 0;
    $total_amount = 0;

    $receives = Receiver::where([
        ['user', Auth::id()],
        ['date', '=', $current_date]
    ])
    ->orderBy('created_at', 'asc') // Change 'desc' to 'asc' if you need ascending order
    ->get();

    foreach ($receives as $receive){
        if($receive->receive_from == "agent"){
            $receive->name = Agent::where('id', $receive->agent_supplier_id)->value('name');
        }
        else{
            $receive->name = Supplier::where('id', $receive->agent_supplier_id)->value('name');
        }
        $receive->method = Transaction::where('id', $receive->method)->value('name');
        $total_receive += $receive->amount;
    }

    $payments = Payment::where([
        ['user', Auth::id()],
        ['date', '=', $current_date]
    ])
    ->orderBy('created_at', 'asc') // Change 'desc' to 'asc' if you need ascending order
    ->get();

    foreach ($payments as $payment){
        if($payment->receive_from == "agent"){
            $payment->name = Agent::where('id', $payment->agent_supplier_id)->value('name');
        }
        else{
            $payment->name = Supplier::where('id', $payment->agent_supplier_id)->value('name');
        }
        $payment->method = Transaction::where('id', $payment->method)->value('name');
        $total_pay += $payment->amount;
    }

    $current_date = Carbon::now()->toDateString();

    
    $transactions = Transaction::where('user', Auth::id())
        // ->whereDate('updated_at', '=', $current_date)
        ->orderBy('id', 'asc') // Change 'desc' to 'asc' if you need ascending order
        ->get();
    $total_amount = Transaction::where('user', Auth::id())
        // ->whereDate('updated_at', '=', $current_date)
        ->sum('amount');


    // dd($receives, $payments, $current_date, $total_amount);

    return view('dashboard', compact('closetickets', 'receives', 'payments', 'total_receive', 'total_pay', 'transactions', 'total_amount'));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/layout.app', function () {
    $user = Auth::user();
    return view('layout.app',compact('user'));
})->middleware(['auth', 'verified'])->name('layout.app');


Route::get('agent/view', function () {
    return app(AgentController::class)->index();
})->name('agent.view');
Route::post('/addagent', [AgentController::class, 'store'])->name('addagent.store');
Route::get('/agent/edit/{id}', [AgentController::class, 'edit'])->name('agent.edit');
Route::post('/agent/update/{id}', [AgentController::class, 'update'])->name('agent.update');
Route::get('/agent/delete/{id}', [AgentController::class, 'delete'])->name('agent.delete');

Route::get('/supplier/view', function () {
    return app(SupplierController::class)->index();
})->name('supplier.view');
Route::post('/addsupplier', [SupplierController::class, 'store'])->name('addsupplier.store');
Route::get('/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::post('/supplier/update/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::get('/supplier/delete/{id}', [SupplierController::class, 'delete'])->name('supplier.delete');

// Route::get('type/view', function () {
//     return app(TypeController::class)->index();
// })->name('type.view');


Route::get('/types', [TypeController::class, 'index'])->name('type.index');
Route::post('/addtype', [TypeController::class, 'store'])->name('addtype.store');
Route::get('/type/edit/{id}', [TypeController::class, 'edit'])->name('type.edit');
Route::post('/type/update/{id}', [TypeController::class, 'update'])->name('type.update');
Route::get('/type/delete/{id}', [TypeController::class, 'delete'])->name('type.delete');

Route::get('/order/view', function () {
    return app(OrderController::class)->index();
})->name('order.view');
Route::post('/addorder', [OrderController::class, 'store'])->name('addorder.store');
Route::get('/order/edit/{id}', [OrderController::class, 'edit'])->name('order.edit');
Route::get('/order/view/{id}', [OrderController::class, 'view'])->name('order.viewInv');
Route::post('/order/update/{id}', [OrderController::class, 'update'])->name('order.update');
Route::get('/order/delete/{id}', [OrderController::class, 'delete'])->name('order.delete');

Route::get('/ticket/view', function () {
    return app(TicketController::class)->index();
})->name('ticket.view');
Route::post('/addticket', [TicketController::class, 'store'])->name('addticket.store');
Route::post('/addsingleticket', [TicketController::class, 'store_single'])->name('addsingleticket.store');
Route::get('/ticket/edit/{id}', [TicketController::class, 'edit'])->name('ticket_edit');
Route::post('/ticket/update', [TicketController::class, 'update'])->name('ticket.update');
Route::get('/ticket/delete/{id}', [TicketController::class, 'delete'])->name('ticket.delete');
Route::get('/ticket/print/{id}', [TicketController::class, 'print'])->name('ticket_print');
Route::get('/ticket/view/{id}', [TicketController::class, 'view'])->name('ticket_view');
Route::post('/search_airline', [TicketController::class, 'searchAirline'])->name('search_airline');

Route::any('/refund_ticket', [TicketController::class, 'refundindex'])->name('refund_ticket');
Route::post('/refund_ticket_entry', [RefundController::class, 'entry'])->name('refund_ticket_entry');


Route::post('/search_ticket', [TicketController::class, 'searchTicket'])->name('search_ticket');
Route::get('/get_agent_supplier', [TicketController::class, 'getAgentSupplier'])->name('get_agent_supplier');
Route::get('/get-last-id', [TicketController::class, 'getlastid'])->name('get-last-id');


Route::post('/receive_only', [TicketController::class, 'receiveAmount'])->name('receive_only');
Route::get('/deportee/index', function (Request $request) {
    return app(DeporteeController::class)->view($request);
})->name('deportee.index');
Route::post('/deportee_ticket_entry', [DeporteeController::class, 'deportee_ticket_entry'])->name('deportee_ticket_entry');
Route::get('/get-last-id-deportee', [DeporteeController::class, 'getlastiddeportee'])->name('get-last-id-deportee');

Route::get('/segment/view', function () {
    return app(ReportController::class)->segment_view();
})->name('segment.view');
Route::post('/segment_report', [ReportController::class, 'segment_report'])->name('segment_report');

Route::get('/sector_city/view', function () {
    return app(ReportController::class)->sector_city_view();
})->name('sector_city.view');
Route::post('/sector_city_report', [ReportController::class, 'sector_city_report'])->name('sector_city_report');


Route::get('/void/view', function (Request $request) {
    return app(VoidController::class)->view($request);
})->name('void.view');
Route::post('/ticket_void', [VoidController::class, 'void_entry'])->name('ticket_void');

Route::get('/void_ticket', [ReportController::class, 'void_ticket'])->name('void_ticket');
Route::post('/void_ticket_report', [ReportController::class, 'void_ticket_report'])->name('void_ticket_report');

Route::get('/reissue_ticket', [ReportController::class, 'reissue_ticket'])->name('reissue_ticket');
Route::post('/reissue_ticket_report', [ReportController::class, 'reissue_ticket_report'])->name('reissue_ticket_report');

Route::get('/adm/view', function (Request $request) {
    return app(ADMController::class)->view($request);
})->name('adm.view');
Route::post('/adm_entry', [ADMController::class, 'adm_entry'])->name('adm_entry');
Route::get('/get-last-id-adm', [ADMController::class, 'getlastidadm'])->name('get-last-id-adm');

Route::any('/reissue/view', function (Request $request) {
    return app(ReissueController::class)->view($request);
})->name('reissue.view');

Route::any('/reissue/view', function (Request $request) {
    return app(ReissueController::class)->view($request);
})->name('reissue.view');




Route::post('/ticket_reissue', [ReissueController::class, 'reissue_entry'])->name('ticket_reissue');
// 

// Inside your routes/web.php
Route::post('/submit-payment', [ReceivePaymentController::class, 'payment'])->name('submit.payment');
Route::post('/submit-receive', [ReceivePaymentController::class, 'receive'])->name('submit.receive');
Route::get('/receive_payment', [ReceivePaymentController::class, 'index'])->name('receive_payment');
Route::get('/receive_payment', [ReceivePaymentController::class, 'index'])->name('receive_payment.index');
Route::get('/payment_form', [ReceivePaymentController::class, 'payment_index'])->name('payment.form');
Route::get('/receive_form', [ReceivePaymentController::class, 'receive_index'])->name('receive.index');

Route::get('/report/view', function () {
    return app(ReportController::class)->index();
})->name('report.view');

Route::get('/ticket_seles_report', [ReportController::class, 'ticket_seles_report'])->name('ticket_seles_report');
// Route::get('/general-ledger', [GeneralLedgerController::class, 'general_ledger'])->name('general_ledger');
// Route::post('/generate-report', [ReportController::class, 'generate'])->name('generate.report');
// Route::post('/general-ledger-report', [GeneralLedgerController::class, 'general_ledger_report'])->name('general_ledger_report');
Route::get('/general-ledger', [GeneralLedgerController::class, 'general_ledger'])->name('general_ledger');
Route::post('/general-ledger-report', [GeneralLedgerController::class, 'general_ledger_report'])->name('general_ledger_report');

Route::get('/receive_report_index', [ReportController::class, 'receive_report_index'])->name('receive_report_index');
Route::post('/receive_report_info', [ReportController::class, 'receive_report_info'])->name('receive_report_info');
Route::get('/receive_voucher/{id}', [ReportController::class, 'receive_voucher'])->name('receive_voucher');
Route::get('/delete_receive/{id}', [ReceivePaymentController::class, 'delete_receive'])->name('delete_receive');
Route::get('/delete_payment/{id}', [ReceivePaymentController::class, 'delete_payment'])->name('delete_payment');

Route::get('/payment_report_index', [ReportController::class, 'payment_report_index'])->name('payment_report_index');
Route::post('/payment_report_info', [ReportController::class, 'payment_report_info'])->name('payment_report_info');
Route::get('/payment_voucher/{id}', [ReportController::class, 'payment_voucher'])->name('payment_voucher');

Route::get('/profit_loss/view', function () {
    return app(ReportController::class)->profit_loss_view();
})->name('profit_loss.view');
Route::post('/profit_loss_report', [ReportController::class, 'profit_loss_report'])->name('profit_loss_report');

Route::get('/income_statement/view', function () {
    return app(ReportController::class)->income_statement_view();
})->name('income_statement.index');
Route::post('/income_statement_report', [ReportController::class, 'income_statement_report'])->name('income_statement_report');

Route::get('/ait_report_index', [ReportController::class, 'ait_report_index'])->name('ait_report_index');
Route::post('/ait_report_info', [ReportController::class, 'ait_report_info'])->name('ait_report_info');

Route::get('/due_reminder', [ReportController::class, 'due_reminder'])->name('due_reminder');
Route::get('/due_reminder_specific', [ReportController::class, 'due_reminder_specific'])->name('due_reminder_specific');

Route::get('/trialbalance', [ReportController::class, 'trialbalance'])->name('trialbalance.view');
Route::post('/trialbalance_report', [ReportController::class, 'trialbalance_report'])->name('trialbalance_report');

Route::get('/sales_ticket', [ReportController::class, 'sales_ticket'])->name('sales_ticket');
Route::post('/sales_report_ticket', [ReportController::class, 'sales_report_ticket'])->name('seles_report_ticket');

Route::get('/sales_visa', [ReportController::class, 'sales_visa'])->name('sales_visa');
Route::post('/sales_report_visa', [ReportController::class, 'sales_report_visa'])->name('sales_report_visa');

Route::get('/sales_analysis', [ReportController::class, 'sales_analysis'])->name('sales_analysis');
Route::post('/sales_analysis_report', [ReportController::class, 'sales_analysis_report'])->name('sales_analysis_report');

Route::get('/sales_exicutive_stuff', [ReportController::class, 'sales_exicutive_stuff'])->name('sales_exicutive_stuff');
Route::post('/seles_executive_report_stuff', [ReportController::class, 'seles_executive_report_stuff'])->name('seles_executive_report_stuff');

Route::get('/income_statement/view', function () {
    return app(ReportController::class)->income_statement_view();
})->name('income_statement.index');
Route::post('/income_statement_report', [ReportController::class, 'income_statement_report'])->name('income_statement_report');

Route::get('stuff_details/view', function () {
    return app(HrController::class)->index();
})->name('stuff_details.view');
Route::post('/addstuff', [HrController::class, 'store'])->name('addstuff.store');
Route::get('/stuff/edit/{id}', [HrController::class, 'edit'])->name('stuff.edit');
Route::post('/stuff/update/', [HrController::class, 'update'])->name('stuff.update');
Route::get('/stuff/delete/{id}', [HrController::class, 'delete'])->name('stuff.delete');
Route::any('/stuff/report/{id}', [HrController::class, 'report'])->name('stuff.report');
// Route::post('/stuff/report/{id}', [HrController::class, 'report'])->name('stuff.report');
Route::post('/get-staff-details', [HrController::class, 'getStaffDetails']);

Route::get('pay_salary/view', function () {
    return app(HrController::class)->salary_index();
})->name('pay_salary.view');
Route::get('/payslip/{id}', [HrController::class, 'payslip'])->name('payslip.view');
Route::post('/addsalary', [HrController::class, 'salary_store'])->name('salary.store');
Route::get('/salary/edit/{id}', [HrController::class, 'salary_edit'])->name('salary.edit');
Route::post('/salary/update/', [HrController::class, 'salary_update'])->name('salary.update');
Route::get('/salary/delete/{id}', [HrController::class, 'salary_delete'])->name('salary.delete');

Route::post('/addorder_multiple', [OrderController::class, 'store_multiple'])->name('addorder.multiple');


Route::get('moneytransfer/view', function () {
    return app(MoneyTransferController::class)->index();
})->name('moneytransfer.view');
Route::post('/moneytransfer/add', [MoneyTransferController::class, 'store'])->name('moneytransfer.add');
Route::get('/moneytransfers/{id}', [MoneyTransferController::class, 'destroy'])->name('moneytransfer.delete');


Route::get('transaction/view', function () {
    return app(TransactionController::class)->index();
})->name('transaction.view');
Route::post('/transaction_add', [TransactionController::class, 'store'])->name('transaction.store');
Route::get('/transaction/edit/{id}', [TransactionController::class, 'edit'])->name('transaction.edit');
Route::post('/transaction/update/{id}', [TransactionController::class, 'update'])->name('transaction.update');
Route::get('/transaction/delete/{id}', [TransactionController::class, 'delete'])->name('transaction.delete');

Route::get('/get-last-id-order', [OrderController::class, 'getlastid'])->name('get-last-id-order');
Route::get('/get-last-id-payment', [ReceivePaymentController::class, 'getlastid_payment'])->name('get-last-id-payment');
Route::get('/get-last-id-receive', [ReceivePaymentController::class, 'getlastid_receive'])->name('get-last-id-receive');


Route::post('/change_password', function () {
    return app(SettingsController::class)->change_password();
})->name('change_password');
Route::get('changePass', function () {
    return app(SettingsController::class)->index();
})->name('changePass');
Route::get('company_profile/view', function () {
    return app(SettingsController::class)->edit_company_profile();
})->name('company_profile.view');

Route::get('/bank_book', [ReportController::class, 'bank_book'])->name('bank_book.view');
Route::get('/cash_book', [ReportController::class, 'cash_book'])->name('cash_book.view');
Route::post('/bank_book_report', [ReportController::class, 'bank_book_report'])->name('bank_book_report');
Route::post('/cash_book_report', [ReportController::class, 'cash_book_report'])->name('cash_book_report');

Route::get('/dailystate', [ReportController::class, 'dailystate'])->name('dailystate.view');
Route::post('/dailystate_report', [ReportController::class, 'dailystate_report'])->name('dailystate_report');

Route::get('/profitreport_view', [ReportController::class, 'profitreport_view'])->name('profitreport.view');
Route::post('/profitreport', [ReportController::class, 'profitreport'])->name('profitreport.info');

Route::get('airline/view', function () {
    return app(AirlineController::class)->index();
})->name('airline.view');
Route::post('/addairline', [AirlineController::class, 'store'])->name('airline.store');
Route::get('/airline/edit/{id}', [AirlineController::class, 'edit'])->name('airline.edit');
Route::post('/airline/update/{id}', [AirlineController::class, 'update'])->name('airline.update');
Route::get('/airline/delete/{id}', [AirlineController::class, 'delete'])->name('airline.delete');
Route::get('/findairlinefree', [AirlineController::class, 'findAirlineFree'])->name('findairlinefree');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/pending-accounts', [AdminController::class, 'showPendingAccounts'])->name('admin.pending_accounts');
//     Route::post('/admin/approve-account/{user}', [AdminController::class, 'approveAccount'])->name('admin.approve_account');
//     Route::post('/admin/deny-account/{user}', [AdminController::class, 'denyAccount'])->name('admin.deny_account');
// });

Route::get('/admin/login', [AdminController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'adminLogin']);

// Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/pending-users', [App\Http\Controllers\AdminController::class, 'showPendingUsers'])->name('admin.pending_users');
    Route::post('/admin/approve-user/{id}', [App\Http\Controllers\AdminController::class, 'approveUser'])->name('admin.approve_user');
// });
require __DIR__.'/auth.php';

Route::get('expanditure/view', function () {
    return app(MoneyTransferController::class)->expanditure_index();
})->name('expanditure.view');
Route::post('/add-expenditure-towards', [MoneyTransferController::class, 'add_expenditure_towards'])
    ->name('add_expenditure_towards');
Route::post('/add_expenditure_main', [MoneyTransferController::class, 'addExpenditureMain'])->name('add_expenditure_main');


use App\Http\Controllers\Admin\UserApprovalController;

Route::prefix('admin')->group(function () {
    Route::get('user/approve/{id}', [UserApprovalController::class, 'approve'])->name('admin.user.approve');
    Route::get('user/deny/{id}', [UserApprovalController::class, 'deny'])->name('admin.user.deny');
});



use App\Http\Controllers\BkashTokenizePaymentController;
Route::any('/payment/index', [BkashTokenizePaymentController::class, 'payment_index'])->name('payment.index');

// Route::group(['middleware' => ['web']], function () {
    // Payment Routes for bKash
    Route::get('/bkash/payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'index']);
    Route::get('/bkash/create-payment', [App\Http\Controllers\BkashTokenizePaymentController::class,'createPayment'])->name('bkash-create-payment');
    Route::get('/bkash/callback', [App\Http\Controllers\BkashTokenizePaymentController::class,'callBack'])->name('bkash-callBack');

    //search payment
    Route::get('/bkash/search/{trxID}', [App\Http\Controllers\BkashTokenizePaymentController::class,'searchTnx'])->name('bkash-serach');

    //refund payment routes
    Route::get('/bkash/refund', [App\Http\Controllers\BkashTokenizePaymentController::class,'refund'])->name('bkash-refund');
    Route::get('/bkash/refund/status', [App\Http\Controllers\BkashTokenizePaymentController::class,'refundStatus'])->name('bkash-refund-status');

// });