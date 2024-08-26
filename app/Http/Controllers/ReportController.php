<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Supplier;
use App\Models\Agent;
use App\Models\AIT;
use App\Models\Type;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Receiver;
use App\Models\Refund;
use App\Models\ReissueTicket;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\VoidTicket;
use Illuminate\Support\Facades\Auth; // Add this line
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View as ViewFacade;

class ReportController extends Controller
{
    public function index()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $types = Type::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $orders = Order::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            // dd($suppliers);
            return view('report.index', compact('suppliers', 'agents', 'types'));
        }
        else{
            return view('welcome');
        }
       
    }
    public function generate(Request $request)
    {
        if(Auth::user()){
            $type = $request->input('type') ?? null;
            $agent = $request->input('agent') ?? null;
            $supplier = $request->input('supplier') ?? null;
            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;
    
            $show_profit = $request->input('show_profit') ?? null;
            $show_supplier = $request->input('show_supplier') ?? null;
    
            // dd($type, $agent, $supplier, $start_date, $end_date, $show_profit, $show_supplier);
    
            if ($start_date) {
                $start_date = (new DateTime($start_date))->format('Y-m-d');
            }
            if ($end_date) {
                $end_date = (new DateTime($end_date))->format('Y-m-d');
            }
            $user = Auth::id();
    
            $query = DB::table('order')
                ->where([
                    ['is_active', 1],
                    ['is_delete', 0],
                    ['user', $user],
                ]);
            if ($type !== null) {
                $query->where('type', $type);
            }
    
            if ($agent !== null) {
                $query->where('agent', $agent);
            }
    
            if ($supplier !== null) {
                $query->where('supplier', $supplier);
            }
    
            if ($start_date !== null && $end_date !== null) {
                $query->whereBetween('date', [$start_date, $end_date]);
            }
            $alldata = $query->get();
    
            // dd($alldata, $supplier, $agent);
            $htmlTable = '';
            if ($show_profit != null || $show_supplier != null) {
    
    
                if ($show_profit != null && $show_supplier == null) {
                    $htmlTable = '<table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Agent</th>
                            <th>Date</th>
                            <th>Seller</th>
                            <th>Passport</th>
                            <th>Country</th>
                            <th>Remark</th>
                            <th>Profit</th>
                            <!-- Add more columns as needed -->
                        </tr>
                    </thead>
                    <tbody>';
    
                    // Loop through each record in $alldata and add a row to the table
                    foreach ($alldata as $data) {
                        $htmlTable .= '<tr>
                            <td>' . $data->invoice . '</td>
                            <td>' . Type::where('id', $data->type)->value('name') . '</td>
                            <td>' . Agent::where('id', $data->agent)->value('name') . '</td>
                            <td>' . (new DateTime($data->date))->format('d-m-Y') . '</td>
                            <td>' . $data->seller . '</td>
                            <td>' . $data->passport_no . '</td>
                            <td>' . $data->country . '</td>
                            <td>' . $data->remark . '</td>
                            <td>' . $data->profit . '</td>
                            <!-- Add more cells as needed -->
                        </tr>';
                    }
    
                    // Close the HTML table
                    $htmlTable .= '</tbody></table>';
                } elseif ($show_supplier != null && $show_profit == null) {
                    $htmlTable = '<table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Agent</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Seller</th>
                            <th>Passport</th>
                            <th>Country</th>
                            <th>Remark</th>
                            <!-- Add more columns as needed -->
                        </tr>
                    </thead>
                    <tbody>';
    
                    // Loop through each record in $alldata and add a row to the table
                    foreach ($alldata as $data) {
                        $htmlTable .= '<tr>
                            <td>' . $data->invoice . '</td>
                            <td>' . Type::where('id', $data->type)->value('name') . '</td>
                            <td>' . Agent::where('id', $data->agent)->value('name') . '</td>
                            <td>' . Supplier::where('id', $data->supplier)->value('name') . '</td>
                            <td>' . (new DateTime($data->date))->format('d-m-Y') . '</td>
                            <td>' . $data->seller . '</td>
                            <td>' . $data->passport_no . '</td>
                            <td>' . $data->country . '</td>
                            <td>' . $data->remark . '</td>
                            <!-- Add more cells as needed -->
                        </tr>';
                    }
    
                    // Close the HTML table
                    $htmlTable .= '</tbody></table>';
                } elseif ($show_supplier != null && $show_profit != null) {
                    $htmlTable = '<table border="1">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>Agent</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Seller</th>
                            <th>Passport</th>
                            <th>Country</th>
                            <th>Remark</th>
                            <th>Profit</th>
                            <!-- Add more columns as needed -->
                        </tr>
                    </thead>
                    <tbody>';
    
                    // Loop through each record in $alldata and add a row to the table
                    foreach ($alldata as $data) {
                        $htmlTable .= '<tr>
                            <td>' . $data->invoice . '</td>
                            <td>' . Type::where('id', $data->type)->value('name') . '</td>
                            <td>' . Agent::where('id', $data->agent)->value('name') . '</td>
                            <td>' . Supplier::where('id', $data->supplier)->value('name') . '</td>
                            <td>' . (new DateTime($data->date))->format('d-m-Y') . '</td>
                            <td>' . $data->seller . '</td>
                            <td>' . $data->passport_no . '</td>
                            <td>' . $data->country . '</td>
                            <td>' . $data->remark . '</td>
                            <td>' . $data->profit . '</td>
                            <!-- Add more cells as needed -->
                        </tr>';
                    }
    
                    // Close the HTML table
                    $htmlTable .= '</tbody></table>';
                }
            } else {
                $htmlTable = '<table border="1">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Type</th>
                        <th>Agent</th>
                        <th>Date</th>
                        <th>Seller</th>
                        <th>Passport</th>
                        <th>Country</th>
                        <th>Remark</th>
                        <!-- Add more columns as needed -->
                    </tr>
                </thead>
                <tbody>';
    
                // Loop through each record in $alldata and add a row to the table
                foreach ($alldata as $data) {
                    $htmlTable .= '<tr>
                        <td>' . $data->invoice . '</td>
                        <td>' . Type::where('id', $data->type)->value('name') . '</td>
                        <td>' . Agent::where('id', $data->agent)->value('name') . '</td>
                        <td>' . (new DateTime($data->date))->format('d-m-Y') . '</td>
                        <td>' . $data->seller . '</td>
                        <td>' . $data->passport_no . '</td>
                        <td>' . $data->country . '</td>
                        <td>' . $data->remark . '</td>
                        <!-- Add more cells as needed -->
                    </tr>';
                }
    
                // Close the HTML table
                $htmlTable .= '</tbody></table>';
            }
    
            return $htmlTable;
       
        }
        else{
            return view('welcome');
        }
        // dd($request->all());
    }

    public function general_ledger()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            return view('report.general_ledger.index', compact('agents', 'suppliers'));
            
        }
        else{
            return view('welcome');
        }
    }
    public function general_ledger_report(Request $request){
        if(Auth::user()){
            $who = $request->agent_supplier;
            $html = '';
    
            if ($who == 'agent') {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $id = $request->agent_supplier_id;
    
                $receive = Ticket::where([['agent', $id],  ['is_active', 1]]);

                $receive = $receive->where('user', Auth::id());
                $refund = Refund::where('user', Auth::id());
                // $order = Order::where('user', Auth::id());
    
                if (!is_null($start_date) || !is_null($end_date)) {
                    $start_date = (new DateTime($start_date))->format('Y-m-d');
                    $end_date = (new DateTime($end_date))->format('Y-m-d');
    
                    $receive->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('invoice_date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('invoice_date', '<=', $end_date);
                        }
                    });
                }
                
                $receive = $receive->get();
    
                $receiver = Receiver::where([
                    ['receive_from', '=', 'agent'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
    
                $refund = $refund->where([
                    ['agent', $id],
                ]);
    
                $paymenter = Payment::where([
                    ['receive_from', '=', 'agent'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);

                
    
                // $order = $order->where(['agent', $id]);
    
                $void_ticket = VoidTicket::where([['user', Auth::id()], ['agent', $id]]);
                $reissue = ReissueTicket::where([['agent', $id], ['user', Auth::id()]]);

                $opening_balance_debit = $opening_balance_credit = $opening_balance = 0;

                if (!is_null($start_date) || !is_null($end_date)) {
                    $start_date = (new DateTime($start_date))->format('Y-m-d');
                    $end_date = (new DateTime($end_date))->format('Y-m-d');
    
                    $receiver->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $refund->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $paymenter->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $void_ticket->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                   
                    $reissue->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                }
                // $until_start_date_collections = null;
                if (!is_null($start_date)) {
                    // dd("here");
                    $until_start_date_receive = Ticket::where([['agent', $id], ['is_active', 1]])
                        ->where('user', Auth::id())
                        ->where('invoice_date', '<', $start_date)
                        ->get();
                
                    $until_start_date_receiver = Receiver::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_refund = Refund::where([
                        ['agent', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_paymenter = Payment::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_void_ticket = VoidTicket::where([
                        ['user', Auth::id()],
                        ['agent', $id],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_reissue = ReissueTicket::where([
                        ['agent', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_order = Order::where('user', Auth::id())
                        ->where('agent', $id)
                        ->where('date', '<', $start_date)
                        ->get();
                
                    $until_start_date_collections = $until_start_date_receive
                        ->merge($until_start_date_receiver)
                        ->merge($until_start_date_refund)
                        ->merge($until_start_date_paymenter)
                        ->merge($until_start_date_void_ticket)
                        ->merge($until_start_date_reissue)
                        ->merge($until_start_date_order);

                    // dd($until_start_date_collections, $until_start_date_receiver, $until_start_date_paymenter, $until_start_date_refund);
                        foreach ($until_start_date_collections as $collection){
                            // dd($collection);
                            if ($collection->getTable() == 'order'){
                                $opening_balance_debit += $collection->contact_amount;
                            }
                            if ($collection->getTable() == 'tickets'){
                                $opening_balance_debit += $collection->agent_price;
                            }
                            if ($collection->getTable() == 'payment'){
                                $opening_balance_debit += $collection->amount;
                            }
                            if ($collection->getTable() == 'receive'){
                                $opening_balance_credit += $collection->amount;
                            }
                            if ($collection->getTable() == 'reissue'){
                                $opening_balance_debit += $collection->now_agent_debit;
                            }
                            if ($collection->getTable() == 'refund'){
                                $opening_balance_credit += $collection->now_agent_fere;
                            }
                            if ($collection->getTable() == 'voidTicket'){
                                $opening_balance_debit += $collection->now_agent_fere;
                            }
                            
                        }
                }
                
    
                // dd($until_start_date_collections);
              
                // dd($opening_balance_debit);
                $receiver = $receiver->get();
                $paymenter = $paymenter->get();
                $void_ticket = $void_ticket->get();
                $reissue = $reissue->get();
                $refund = $refund->get();
    
                $order = Order::where('user', Auth::id())
                    ->where('agent', $id);
                $order = $order->where(function ($query) use ($start_date, $end_date) {
                    if (!is_null($start_date)) {
                        $query->where('date', '>=', $start_date);
                    }
    
                    if (!is_null($end_date)) {
                        $query->where('date', '<=', $end_date);
                    }
                });
                $order = $order->get();
    
                // dd($receive);
                // $order = $order->get();
                // dd($order, $void_ticket);
                $mergedCollection = $receive->concat($receiver)->concat($paymenter)->concat($void_ticket)->concat($reissue)->concat($refund)->concat($order);
                $sortedCollection = $mergedCollection->sortBy('date');
                // dd($mergedCollection);
                $acountname = Agent::where('id', $id)->value('name');
    
                $balance =  $opening_balance_debit - $opening_balance_credit;
                $debit = 0;
                $credit = 0;

                foreach ($sortedCollection as $index => $item) {
                    // dd($item->getTable());
                    if ($item->getTable() == "tickets") {
                        // Handle logic specific to Ticket model
                        $balance += $item->agent_price;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $debit += $item->agent_price;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        if($item->reissued_new_ticket == 1){
                            $html .= <<<HTML
                                <tr>
                                    <td class="w-[10%]"> $item->invoice_date </td>
                                    <td class="w-[11%]"> $item->invoice </td>
                                    <td class="w-[15%]"> {$ticket->ticket_code}/{$item->ticket_no} </td>
                                    <td class="w-[28%] pr-3">
                                        PAX NAME: <span class="font-semibold"> $item->passenger </span><br>
                                        PNR:  $item->pnr ,  $item->sector <br>
                                        FLIGHT DATE:  $item->flight_date <br>
                                        $item->airline_code -  $item->airline_name <br>
                                        Remarks:  $item->remark 
                                    </td>
                                    
                                    <td class="w-[12%] totaldebit"> $item->agent_price </td>
                                    <td class="w-[12%] totalcredit"></td>
                                    <!-- <td class="w-[12%] text-center"> $item->previous_amount  Dr</td> -->
                                    <td class="w-[12%] totaltotal"> $currentAmount </td>
                                </tr>
                            HTML;
                        }
                        else{

                        
                        $html .= <<<HTML
                                                    <tr>
                                                        <td class="w-[10%]"> $item->invoice_date </td>
                                                        <td class="w-[11%]"> $item->invoice </td>
                                                        <td class="w-[15%]"> {$item->ticket_no} </td>
                                                        <td class="w-[28%] pr-3">
                                                            PAX NAME: <span class="font-semibold"> $item->passenger </span><br>
                                                            PNR:  $item->pnr ,  $item->sector <br>
                                                            FLIGHT DATE:  $item->flight_date <br>
                                                            $item->airline_code -  $item->airline_name <br>
                                                            Remarks:  Reissue
                                                        </td>
                                                        
                                                        <td class="w-[12%] totaldebit"> $item->agent_price </td>
                                                        <td class="w-[12%] totalcredit"></td>
                                                        <!-- <td class="w-[12%] text-center"> $item->previous_amount  Dr</td> -->
                                                        <td class="w-[12%] totaltotal"> $currentAmount </td>
                                                    </tr>
                            HTML;
                        }
                    } elseif ($item->getTable() == "receive") {
                        // $currentAmount = $item->current_amount >= 0 ? $item->current_amount . ' DR' : $item->current_amount . ' CR';
                        $balance -= $item->amount;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $credit += $item->amount;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->ticket_code}/{$item->ticket_no}</td>
                                                    <td class="w-[28%]">
                                                        Remarks:  {$item->remark} <br>
                                                        <b>Receive</b>
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->amount}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "payment") {
                        // $currentAmount = $item->current_amount >= 0 ? $item->current_amount . ' DR' : $item->current_amount . ' CR';
    
                        $balance += $item->amount;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $debit += $item->amount;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        Remarks:  {$item->remark} <br>
                                                        <b>Payment<b>
                                                    </td>
                                                    <td class="w-[12%] totaldebit">{$item->amount}</td>
                                                    <td class="w-[12%] totalcredit"></td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "reissue") {
                        // $currentAmount = $item->now_agent_amount;
                        // $currentAmount = $currentAmount >= 0 ? $currentAmount . ' DR' : $currentAmount . ' CR';
    
                        $balance += $item->now_agent_fere;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $debit += $item->now_agent_debit;
    
                        $agentname = Agent::where('id', $id)->value('name');
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        // dd($ticket);
                      
                        $html .= <<<HTML
                        <tr>
                            <td class="w-[10%]"> {$item->date} </td>
                            <td class="w-[11%]"> {$item->invoice} </td>
                            <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                        HTML;
                        
                        if ($ticket) {
                            $html .= <<<HTML
                            <td class="w-[28%]">
                                Remarks: {$item->remark} 
                                <b>Reissue</b> to Customer: $agentname,  
                                {$item->invoice}<br> Ticket No: {$item->airline_code}/{$item->ticket_no}, <br>
                                Sector: {$ticket->sector},<br> on {$item->date} <b> PAX Name: {$ticket->passenger}</b>
                            </td>
                        HTML;
                        } else {
                            $html .= '<td class="w-[28%]"></td>';
                        }
                        
                        $html .= <<<HTML
                            <td class="w-[12%] totaldebit">{$item->now_agent_fere}</td>
                            <td class="w-[12%] totalcredit"></td>
                            <td class="w-[12%] totaltotal">{$currentAmount}</td>
                        </tr>
                        HTML;
                        
                    } elseif ($item->getTable() == "refund") {
                        // dd($item);
                        $balance -= $item->now_agent_fere;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $credit += $item->now_agent_fere;
    
                        $agentname = Agent::where('id', $id)->value('name');
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr >
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        <!-- Remarks:  Refund
                                                        Agent New Amount: {$item->now_agent_fere}
                                                        Agent Previous Amount: {$item->prev_agent_amount} -->
                                                        <b>Refund</b> to Customer : $agentname ,  
                                                        {$item->invoice}<br> Ticket No : {$item->airline_code}/{$item->ticket_no}, <br>
                                                        Sector :{$ticket->sector} ,<br> on {$item->date} <b> PAX Name : {$ticket->passenger}</b>
                                                        Remark: {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->now_agent_fere}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "order") {
                        // $currentAmount = $item->agent_new_amount;
                        // $currentAmount = $currentAmount >= 0 ? $currentAmount . ' DR' : $currentAmount . ' CR';
                        $balance += $item->contact_amount;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $debit += $item->contact_amount;
    
                        $typeneme = Type::where('id', $item->type)->value('name');
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$typeneme} </td>
                                                    <td class="w-[28%]">
                                                        Passenger: {$item->name} <br>
                                                        Passport: {$item->passport_no}<br>
                                                        Remarks:  {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit">{$item->contact_amount}</td>
                                                    <td class="w-[12%] totalcredit"></td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "voidTicket") {
                        // $currentAmount = $item->now_agent_amount;
                        // $currentAmount = $currentAmount >= 0 ? $currentAmount . ' DR' : $currentAmount . ' CR';
    
                        $balance += $item->now_agent_fere;
                        $currentAmount = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                        $debit += $item->now_agent_fere;
                        // dd($item->date, $currentAmount);
    
                        $agentname = Agent::where('id', $id)->value('name');
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr >
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->ticket_code}-{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        <b>Void</b> to Customer : $agentname ,  
                                                        {$item->invoice}<br> Ticket No : {$item->airline_code}/{$item->ticket_no}, <br>
                                                        Sector :{$ticket->sector} ,<br> on {$item->date} <b> PAX Name : {$ticket->passenger}</b>
                                                        <b>Remarks</b>:  {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit">{$item->now_agent_fere}</td>
                                                    <td class="w-[12%] totalcredit"></td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    }
    
                    // if($index%2 == 0){
    
                    // }
                }
                $balance = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                $agentName = Agent::where('id', $id)->value('name');

                $htmlpart = ViewFacade::make('report.general_ledger.GeneralLadger', [
              
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'html'   => $html,
                    'balance' => $balance,
                    'debit' => $debit,
                    'credit' => $credit,
                    'holdername' => $agentName,
                    'opening_balance_debit' => $opening_balance_debit,
                    'opening_balance_credit' => $opening_balance_credit,
                    'opening_balance' => $opening_balance,
                    // $opening_balance_debit = $opening_balance_credit = $opening_balance = 0;

                   
                ])->render();

                return response()->json(['html' => $htmlpart]);
            }

            elseif ($who == 'supplier') {
                // dd($who);
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $id = $request->agent_supplier_id;
    
                $receive = Ticket::where([['supplier', $id], ['reissued_new_ticket', 1]]);
                // dd($receive);
                $refund = Refund::where('user', Auth::id());
    
                $receiver = Receiver::where([
                    ['receive_from', '=', 'supplier'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
    
                $paymenter = Payment::where([
                    ['receive_from', '=', 'supplier'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
    
                $refund = $refund->where([
                    ['supplier', $id],
                ]);
    
                $void_ticket = VoidTicket::where([['user', Auth::id()], ['supplier', $id]]);
                $reissue = ReissueTicket::where([['supplier', $id], ['user', Auth::id()]]);
                // dd($receive);
    
    
                if (!is_null($start_date) || !is_null($end_date)) {
                    $start_date = (new DateTime($start_date))->format('Y-m-d');
                    $end_date = (new DateTime($end_date))->format('Y-m-d');
    
                    $receive->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('invoice_date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('invoice_date', '<=', $end_date);
                        }
                    });
                }
    
                if (!is_null($start_date) || !is_null($end_date)) {
                    $start_date = (new DateTime($start_date))->format('Y-m-d');
                    $end_date = (new DateTime($end_date))->format('Y-m-d');
    
                    $receiver->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $paymenter->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $void_ticket->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $reissue->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                    $refund->where(function ($query) use ($start_date, $end_date) {
                        if (!is_null($start_date)) {
                            $query->where('date', '>=', $start_date);
                        }
    
                        if (!is_null($end_date)) {
                            $query->where('date', '<=', $end_date);
                        }
                    });
                }

                $opening_balance_debit = $opening_balance_credit = $opening_balance = 0;

                if (!is_null($start_date)) {
                    // Operations for Agent
                
                    $until_start_date_receive = Ticket::where([['agent', $id], ['is_active', 1]])
                        ->where('user', Auth::id())
                        ->where('invoice_date', '<', $start_date)
                        ->get();
                
                    $until_start_date_receiver = Receiver::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_refund = Refund::where([
                        ['agent', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_paymenter = Payment::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_void_ticket = VoidTicket::where([
                        ['user', Auth::id()],
                        ['agent', $id],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_reissue = ReissueTicket::where([
                        ['agent', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_order = Order::where('user', Auth::id())
                        ->where('agent', $id)
                        ->where('date', '<', $start_date)
                        ->get();
                
                    $until_start_date_collections = $until_start_date_receive
                        ->merge($until_start_date_receiver)
                        ->merge($until_start_date_refund)
                        ->merge($until_start_date_paymenter)
                        ->merge($until_start_date_void_ticket)
                        ->merge($until_start_date_reissue)
                        ->merge($until_start_date_order);
                
                    foreach ($until_start_date_collections as $collection) {
                        if ($collection->getTable() == 'order') {
                            $opening_balance_debit += $collection->contact_amount;
                        }
                        if ($collection->getTable() == 'tickets') {
                            $opening_balance_debit += $collection->agent_price;
                        }
                        if ($collection->getTable() == 'payment') {
                            $opening_balance_debit += $collection->amount;
                        }
                        if ($collection->getTable() == 'receive') {
                            $opening_balance_credit += $collection->amount;
                        }
                        if ($collection->getTable() == 'reissue') {
                            $opening_balance_debit += $collection->now_agent_debit;
                        }
                        if ($collection->getTable() == 'refund') {
                            $opening_balance_credit += $collection->now_agent_fere;
                        }
                        if ($collection->getTable() == 'voidTicket') {
                            $opening_balance_debit += $collection->now_agent_fere;
                        }
                    }
                
                    // Operations for Supplier
                
                    $until_start_date_supplier_receive = Ticket::where([['supplier', $id], ['is_active', 1]])
                        ->where('user', Auth::id())
                        ->where('invoice_date', '<', $start_date)
                        ->get();
                
                   
                    $until_start_date_supplier_receiver = Receiver::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_supplier_refund = Refund::where([
                        ['supplier', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_supplier_paymenter = Payment::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_supplier_void_ticket = VoidTicket::where([
                        ['user', Auth::id()],
                        ['supplier', $id],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_supplier_reissue = ReissueTicket::where([
                        ['supplier', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_supplier_order = Order::where('user', Auth::id())
                        ->where('supplier', $id)
                        ->where('date', '<', $start_date)
                        ->get();
                
                    $until_start_date_supplier_collections = $until_start_date_supplier_receive
                        ->merge($until_start_date_supplier_receiver)
                        ->merge($until_start_date_supplier_refund)
                        ->merge($until_start_date_supplier_paymenter)
                        ->merge($until_start_date_supplier_void_ticket)
                        ->merge($until_start_date_supplier_reissue)
                        ->merge($until_start_date_supplier_order);
                
                    foreach ($until_start_date_supplier_collections as $collection) {
                        if ($collection->getTable() == 'order') {
                            $opening_balance_debit += $collection->payable_amount;
                        }
                        if ($collection->getTable() == 'tickets') {
                            $opening_balance_debit += $collection->supplier_price;
                        }
                        if ($collection->getTable() == 'payment') {
                            $opening_balance_debit += $collection->amount;
                        }
                        if ($collection->getTable() == 'receive') {
                            $opening_balance_credit += $collection->amount;
                        }
                        if ($collection->getTable() == 'reissue') {
                            $opening_balance_debit += $collection->now_supplier_debit;
                        }
                        if ($collection->getTable() == 'refund') {
                            $opening_balance_credit += $collection->now_supplier_fere;
                        }
                        if ($collection->getTable() == 'voidTicket') {
                            $opening_balance_debit += $collection->now_supplier_fere;
                        }
                    }
                }
                
    
                $receive = $receive->get();
                $receiver = $receiver->get();
                $paymenter = $paymenter->get();
                $void_ticket = $void_ticket->get();
                $refund = $refund->get();
                $reissue = $reissue->get();
    
                $order = Order::where('user', Auth::id())
                    ->where('supplier', $id);
                $order = $order->where(function ($query) use ($start_date, $end_date) {
                    if (!is_null($start_date)) {
                        $query->where('date', '>=', $start_date);
                    }
    
                    if (!is_null($end_date)) {
                        $query->where('date', '<=', $end_date);
                    }
                });
                $order = $order->get();
    
    
                // $order = $order->get();
                // dd($order, $void_ticket);
                $mergedCollection = $receive->concat($receiver)->concat($paymenter)->concat($void_ticket)->concat($reissue)->concat($refund)->concat($order);
                $sortedCollection = $mergedCollection->sortBy('date');
    
                $balance = $opening_balance_credit -  $opening_balance_debit ;
                $debit = 0;
                $credit = 0;
                // dd($mergedCollection);
    
                $supplierName = Supplier::where('id', $id)->value('name');
                // dd($acountname, $id);
                // dd($sortedCollection);
                foreach ($sortedCollection as $index => $item) {
                    // dd($item->getTable());
                  
                    if ($item->getTable() == "tickets") {
                        // Handle logic specific to Ticket model
                        $credit += $item->supplier_price;
                        $balance += $item->supplier_price;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        
                        $html .= <<<HTML
                                                    <tr>
                                                        <td class="w-[10%]"> $item->invoice_date </td>
                                                        <td class="w-[11%]"> $item->invoice </td>
                                                        <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                        <td class="w-[28%] pr-3">
                                                            PAX NAME: <span class="font-semibold"> $item->passenger </span><br>
                                                            PNR:  $item->pnr ,  $item->sector <br>
                                                            FLIGHT DATE:  $item->flight_date <br>
                                                            $item->airline_code -  $item->airline_name <br>
                                                            Remarks:  $item->remark 
                                                        </td>
                                                        <td class="w-[12%] totaldebit"> </td>
                                                        <td class="w-[12%] totalcredit">$item->supplier_price </td>
                                                        <!-- <td class="w-[12%] text-center"> $item->previous_amount  Dr</td> -->
                                                        <td class="w-[12%] totaltotal">$currentAmount</td>
                                                    </tr>
                                                HTML;
                    }
                    elseif ($item->getTable() == "refund") {
                        // dd($item);
                        $balance -= $item->now_supplier_fare;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $debit += $item->now_supplier_fare;
    
                        $agentname = Agent::where('id', $id)->value('name');
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr >
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        <!-- Remarks:  Refund
                                                        Agent New Amount: {$item->now_supplier_fare}
                                                        Agent Previous Amount: {$item->prev_agent_amount} -->
                                                        <b>Refund</b> to Customer : $agentname ,  
                                                        {$item->invoice}<br> Ticket No : {$item->airline_code}/{$item->ticket_no}, <br>
                                                        Sector :{$ticket->sector} ,<br> on {$item->date} <b> PAX Name : {$ticket->passenger}</b>
                                                        Remarks: {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit">{$item->now_supplier_fare}</td>
                                                    <td class="w-[12%] totalcredit"></td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "receive") {
                        // dd($item);
                        $balance += $item->amount;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $credit += $item->amount;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        Remarks:  {$item->remark} <br>
                                                        <b>Receive<b>
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->amount}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "payment") {
    
                        $balance -= $item->amount;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $debit += $item->amount;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        Remarks:  {$item->remark} <br>
                                                        <b>Payment<b>
                                                    </td>
                                                    <td class="w-[12%] totaldebit">{$item->amount}</td>
                                                    <td class="w-[12%] totalcredit"></td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "reissue") {
                        // $currentAmount = $item->now_supplier_amount;
                        // $currentAmount = $currentAmount >= 0 ? $currentAmount . ' DR' : $currentAmount . ' CR';
                        // dd($item);
                        $balance += $item->now_supplier_fare;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
                        $credit += $item->now_supplier_fare;
                        $html .= <<<HTML
                                                <tr >
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->airline_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                         
                                                        <b>Reissue</b> to Customer : $supplierName ,  
                                                        {$item->invoice}<br> Ticket No : {$item->airline_code}/{$item->ticket_no}, <br>
                                                        Sector :{$ticket->sector} ,<br> on {$item->date} <b> PAX Name : {$ticket->passenger}</b><br/>
                                                        Remarks:  {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->now_supplier_fare}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "voidTicket") {
                        // dd($item);
                        // $currentAmount = $item->now_supplier_amount;
                        // $currentAmount = $currentAmount >= 0 ? $currentAmount . ' DR' : $currentAmount . ' CR';
                        $balance += $item->now_supplier_fare;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $credit += $item->now_supplier_fare;
                        $ticket = Ticket::where([['user', Auth::id()], ['ticket_no', $item->ticket_no]])->first();
    
                        $html .= <<<HTML
                                                <tr >
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$item->ticket_code}/{$item->ticket_no} </td>
                                                    <td class="w-[28%]">
                                                        <b>Void</b> to Customer : $supplierName ,  
                                                        {$item->invoice}<br> Ticket No : {$item->airline_code}/{$item->ticket_no}, <br>
                                                        Sector :{$ticket->sector} ,<br> on {$item->date} <b> PAX Name : {$ticket->passenger}</b><br>
                                                        Remarks:  {$item->remark}
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->now_supplier_fare}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    } elseif ($item->getTable() == "order") {
                        
                        $balance += $item->payable_amount;
                        $currentAmount = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                        $credit += $item->payable_amount;
    
                        $typeneme = Type::where('id', $item->type)->value('name');
                        $html .= <<<HTML
                                                <tr>
                                                    <td class="w-[10%]"> {$item->date} </td>
                                                    <td class="w-[11%]"> {$item->invoice} </td>
                                                    <td class="w-[15%]"> {$typeneme} </td>
                                                    <td class="w-[28%]">
                                                        
                                                        Passenger: {$item->name} <br>
                                                        Passport: {$item->passport_no}<br>
                                                        Remarks:  {$item->remark} <br>
                                                    </td>
                                                    <td class="w-[12%] totaldebit"></td>
                                                    <td class="w-[12%] totalcredit">{$item->payable_amount}</td>
                                                    <td class="w-[12%] totaltotal">{$currentAmount}</td>
                                                </tr>
                                                HTML;
                    }
                }
                $balance = $balance >= 0 ? $balance . ' CR' : $balance . ' DR';
                // $balance = $balance >= 0 ? $balance . ' DR' : $balance . ' CR';
                $htmlpart = ViewFacade::make('report.general_ledger.GeneralLadger', [
              
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'html'   => $html,
                    'balance' => $balance,
                    'debit' => $debit,
                    'credit' => $credit,
                    'holdername' => $supplierName,
                    'opening_balance_debit' => $opening_balance_debit,
                    'opening_balance_credit' => $opening_balance_credit,
                    'opening_balance' => $opening_balance,
                   
                ])->render();
                return response()->json(['html' => $htmlpart]);
            }
        }
        else{
            return view('welcome');
        }
    }
    
    public function ticket_seles_report()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $types = Type::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $orders = Order::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            // dd($suppliers);
            return view('report.seles.ticketseles_index', compact('suppliers', 'agents', 'types'));
        }
        else{
            return view('welcome');
        }
       
    }
    public function receive_report_index()
    {
        if(Auth::user()){
            $methods = Transaction::where('is_delete', 0)->where('user', Auth::id())->get();
            $agents = Agent::where('is_delete', 0)->where('user', Auth::id())->get();
            $suppliers = Supplier::where('is_delete', 0)->where('user', Auth::id())->get();
            return view('report.receive.index', compact('methods', 'agents', 'suppliers'));
        }
        else{
            return view('welcome');
        }
        
    }

    public function receive_report_info(Request $request)
    {
        if(Auth::user()){
              // dd($request->all());
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        $invoice = $request->input('voucher') ?? null;

        $tableName = $customerid = null;
        if ($request->customer) {
            list($tableName, $customerid) = explode('_', $request->customer);
        }

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query1 = Receiver::where('receive.user', $user);

        if ($tableName !== null) {
            $query1->where('receive_from', $tableName);
        }

        if ($customerid !== null) {
            $query1->where('agent_supplier_id', $customerid);
        }

        if ($request->method !== null) {
            $query1->where('method', $request->method);
        }

        if ($start_date !== null) {
            $query1->whereDate('date', '>=', $start_date);
        }

        if ($end_date !== null) {
            $query1->whereDate('date', '<=', $end_date);
        }

        if ($invoice !== null) {
            $query1->where('invoice', $invoice);
        }


        $query1->leftJoin('transaction as transaction_left', 'transaction_left.id', '=', 'receive.method');


        if ($tableName !== null) {
            $query1->join(DB::raw("$tableName as dynamicTable"), 'receive.agent_supplier_id', '=', 'dynamicTable.id')
                ->where('dynamicTable.user', $user)
                ->select('dynamicTable.name', 'receive.*', 'transaction_left.name as method_name');
        } else {
            $query1->select('receive.*', 'transaction_left.name as method_name');
        }

        $result = $query1->get();

        if ($tableName === null && $customerid === null) {
            // dd('mk');
            foreach ($result as $row) {
                if ($row->receive_from == 'agent' || $row->receive_from == 'Agent') {
                    $row->name = Agent::where('id', $row->agent_supplier_id)->value('name');
                } else {
                    $row->name = Supplier::where('id', $row->agent_supplier_id)->value('name');
                }
            }
        }

        $html = '
            
            
            <main class=" mx-auto w-[95%] ">
            
                <div class="py-3 flex  gap-y-4 mb-3 ">
                    <h2 class="font-bold text-2xl">Received Report</h2>
                    <!-- <button type="button" class="text-md bg-white px-3 font-medium rounded">Print</button> -->
                </div>
                <table class="table-auto w-full shadow-2xl">
                    <thead>
                    <tr class="bg-[#0E7490] text-white">
                        <th class="px-4 py-2 text-md text-left">Date</th>
                        <th class="px-4 py-2 text-md text-left">Voucher No</th>
                        <th class="px-4 py-2 text-md text-left">Receive From</th>
                        <th class="px-4 py-2 text-md text-left">Receive Mode</th>
                        <th class="px-4 py-2 text-md text-left">Narration</th>
                        <th class="px-4 py-2 text-md text-left">Amount</th>
                        <th class="px-4 py-2 text-md text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody id="data" class="divide-y divide-gray-500">';
        foreach ($result as $key => $item) :
            $printUrl = url('/receive_voucher', ['id' => $item->id]);
            $deleteUrl = url('/delete_receive', ['id' => $item->id]);
            $html .= <<<HTML
                            <tr class="">
                                <td class="w-[10%] px-4 py-2 text-sm text-left"> $item->date </td>
                                <td class="w-[11%] px-4 py-2 text-sm text-left"> $item->invoice </td>
                                <td class="w-[15%] px-4 py-2 text-sm text-left"> $item->name </td>
                                <td class="w-[28%] px-4 py-2 text-sm text-left">
                                  $item->method_name
                                </td>
                                <td class="w-[12%] px-4 py-2 text-sm text-left"> $item->remark </td>
                                <td class="w-[12%] px-4 py-2 text-sm text-left amount">$item->amount</td>
                                <!-- <td class="w-[12%] text-center"> $item->previous_amount  Dr</td> -->
                                <td class="px-2 py-1 text-center flex justify-center gap-2"><a href='$printUrl' class=" text-black  rounded-md text-md"><i class="fa fa-fw fa-print text-md"></i></a>
                                <button type="button" class="deleteBtn text-black rounded-md text-md text-danger" data-url='$deleteUrl'>
                                    <i class="fa fa-trash fa-fw text-md"></i>
                                </button>
                                </td>
                            </tr>
                            HTML;
        endforeach;

        $html .= ' 
                        <tr>
                            <td class="px-4 py-2 text-left font-bold" colspan="5">Total Amount </td>
                            <td class="ml-5 font-bold text-md px-2 " id="total_amount"></td>
                        </tr>
                    </tbody>
                </table>
            </main>
            <script type="text/javascript">
                function calculateTotalAmount() {
                    const amountElements = document.querySelectorAll(".amount");
                
                    let totalAmount = 0;
                    
                    amountElements.forEach(element => {
                        // Parse the text content of the element to get the numeric value
                        const amount = parseFloat(element.textContent);
                        // Add the amount to the total
                        totalAmount += amount;
                    });
                    document.getElementById("total_amount").innerHTML = totalAmount;
                    
                    console.log("Total amount:", totalAmount);
                }
                calculateTotalAmount();
                $(document).ready(function(){
                $(".deleteBtn").click(function(event){
                    event.preventDefault(); // Prevent the default action of the button
                    const deleteUrl = $(this).data("url");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won\'t be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your item has been deleted.",
                                icon: "success"
                            }).then(() => {
                                // Redirect to the delete URL
                                window.location.href = deleteUrl;
                            });
                        }
                    });
                });
            });
                
            </script>
        ';

        return $html;
        }
        else{
            return view('welcome');
        }
      
    }
    public function payment_report_index()
    {
        if(Auth::user()){
            $methods = Transaction::where('is_delete', 0)->where('user', Auth::id())->get();
            $agents = Agent::where('is_delete', 0)->where('user', Auth::id())->get();
            $suppliers = Supplier::where('is_delete', 0)->where('user', Auth::id())->get();
            return view('report.payment.index', compact('methods', 'agents', 'suppliers'));
        }
        else{
            return view('welcome');
        }
       
    }
    public function payment_voucher(Request $request, $id)
    {
        if(Auth::user()){
            $user = Auth::id();
            $payment_voucher = Payment::findOrFail($id);
            $transaction = Transaction::where([
                ['id', $payment_voucher->method],
                ['user', $user]
            ])->select('name')->first();
            
            $methodName = $transaction ? $transaction->name : null;
            $payment_voucher->method = $methodName;
            if ($payment_voucher->receive_from == 'agent') {
                $supplier = Agent::where([
                    ['id', $payment_voucher->agent_supplier_id]
                ])->first();
            } else {
                $supplier = Supplier::where([
                    ['id', $payment_voucher->agent_supplier_id]
                ])->first();
            }
            return view('report.payment.voucher', compact('payment_voucher', 'supplier'));
        }
        else{
            return view('welcome');
        }
       
    }
    public function receive_voucher(Request $request, $id)
    {
        if(Auth::user()){
            $user = Auth::id();
            $receive_voucher = Receiver::findOrFail($id);
            // dd($receive_voucher);
            $transaction = Transaction::where([
                ['id', $receive_voucher->method],
                ['user', $user]
            ])->select('name')->first();
            
            $methodName = $transaction ? $transaction->name : null;
            $receive_voucher->method = $methodName;
            $id = $receive_voucher->agent_supplier_id;
            $opening_balance = 0;
            if ($receive_voucher->receive_from == 'agent') {

                $agent = Agent::where([
                    ['id', $receive_voucher->agent_supplier_id]
                ])->first();

                $tickets = Ticket::where([['agent', $id],  ['is_active', 1]]);

                $tickets = $tickets->where('user', Auth::id());
                $refund = Refund::where('user', Auth::id());

                $receiver = Receiver::where([
                    ['receive_from', '=', 'agent'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
    
                $refund = $refund->where([
                    ['agent', $id],
                    ['user', Auth::id()]
                ]);
    
                $paymenter = Payment::where([
                    ['receive_from', '=', 'agent'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
                
                $void_ticket = VoidTicket::where([['user', Auth::id()], ['agent', $id]]);
                $reissue = ReissueTicket::where([['agent', $id], ['user', Auth::id()]]);
                $order = Order::where('user', Auth::id())
                    ->where('agent', $id);

                
                $tickets = $tickets->get();
                $order = $order->get();
                $receiver = $receiver->get();
                $paymenter = $paymenter->get();
                $void_ticket = $void_ticket->get();
                $reissue = $reissue->get();
                $refund = $refund->get();

                $mergedCollection = $tickets->concat($receiver)->concat($paymenter)->concat($void_ticket)->concat($reissue)->concat($refund)->concat($order);
                $opening_balance = Agent::where('id', $id)->value('opening_balance');

                foreach ($mergedCollection as $collection){
                    // dd($collection);
                    if ($collection->getTable() == 'order'){
                        $opening_balance += $collection->contact_amount;
                    }
                    if ($collection->getTable() == 'tickets'){
                        $opening_balance += $collection->agent_price;
                    }
                    if ($collection->getTable() == 'payment'){
                        $opening_balance += $collection->amount;
                    }
                    if ($collection->getTable() == 'receive'){
                        $opening_balance -= $collection->amount;
                    }
                    if ($collection->getTable() == 'reissue'){
                        $opening_balance += $collection->now_agent_debit;
                    }
                    if ($collection->getTable() == 'refund'){
                        $opening_balance -= $collection->now_agent_fere;
                    }
                    if ($collection->getTable() == 'voidTicket'){
                        $opening_balance += $collection->now_agent_fere;
                    }
                    
                }
                // dd($opening_balance);

            } else {
                $agent = Supplier::where([
                    ['id', $receive_voucher->agent_supplier_id]
                ])->first();
                $tickets = Ticket::where([['supplier', $id],  ['is_active', 1]]);

                $tickets = $tickets->where('user', Auth::id());
                $refund = Refund::where('user', Auth::id());

                $receiver = Receiver::where([
                    ['receive_from', '=', 'supplier'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
    
                $refund = $refund->where([
                    ['supplier', $id],
                    ['user', Auth::id()]
                ]);
    
                $paymenter = Payment::where([
                    ['receive_from', '=', 'supplier'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);
                
                $void_ticket = VoidTicket::where([['user', Auth::id()], ['supplier', $id]]);
                $reissue = ReissueTicket::where([['supplier', $id], ['user', Auth::id()]]);
                $order = Order::where('user', Auth::id())
                    ->where('supplier', $id);

                
                $tickets = $tickets->get();
                $order = $order->get();
                $receiver = $receiver->get();
                $paymenter = $paymenter->get();
                $void_ticket = $void_ticket->get();
                $reissue = $reissue->get();
                $refund = $refund->get();

                $mergedCollection = $tickets->concat($receiver)->concat($paymenter)->concat($void_ticket)->concat($reissue)->concat($refund)->concat($order);
                $opening_balance = Supplier::where('id', $id)->value('opening_balance');

                foreach ($mergedCollection as $collection){
                    // dd($collection);
                    if ($collection->getTable() == 'order') {
                        $opening_balance += $collection->payable_amount;
                    }
                    if ($collection->getTable() == 'tickets') {
                        $opening_balance += $collection->supplier_price;
                    }
                    if ($collection->getTable() == 'payment') {
                        $opening_balance -= $collection->amount;
                        //  dd($opening_balance);
                    }
                    if ($collection->getTable() == 'receive') {
                        $opening_balance += $collection->amount;
                    }
                    if ($collection->getTable() == 'reissue') {
                        $opening_balance += $collection->now_supplier_fare;
                    }
                    if ($collection->getTable() == 'refund') {
                        $opening_balance += $collection->now_supplier_fare;
                    }
                    if ($collection->getTable() == 'voidTicket') {
                        $opening_balance += $collection->now_supplier_fare;
                    }
                    
                }
            }
            // dd($agent);
    
            return view('report.receive.voucher', compact('receive_voucher', 'agent', 'opening_balance'));
        }
        else{
            return view('welcome');
        }
     
    }
    
    public function payment_report_info_1(Request $request)
    {

        // dd($request->all());
        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        $tableName = $customerid = null;
        if ($request->customer != null) {
            list($tableName, $customerid) = explode('_', $request->customer);
        }

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query1 = Payment::where('payment.user', $user);

        if ($tableName !== null) {
            $query1->where('receive_from', $tableName);
        }

        if ($customerid !== null) {
            $query1->where('agent_supplier_id', $customerid);
        }

        if ($request->method !== null) {
            $query1->where('method', $request->method);
        }
        if ($request->voucher !== null) {
            $query1->where('invoice', $request->voucher);
        }

        if ($start_date !== null && $end_date !== null) {
            $query1->whereBetween('date', [$start_date, $end_date]);
        } elseif ($start_date !== null) {
            $query1->whereDate('date', '>=', $start_date);
        } elseif ($end_date !== null) {
            $query1->whereDate('date', '<=', $end_date);
        }

        $query1->leftJoin('transaction as transaction_left', 'transaction_left.id', '=', 'payment.method');

        if ($tableName !== null) {
            $query1->leftJoin(DB::raw("$tableName as dynamicTable"), 'payment.agent_supplier_id', '=', 'dynamicTable.id')
                ->where('dynamicTable.user', $user)
                ->select('dynamicTable.name', 'payment.*', 'transaction_left.name as method_name');
        } else {
            $query1->select('payment.*', 'transaction_left.name as method_name');
        }

        $result = $query1->get();

        if ($tableName === null && $customerid === null) {
            // dd('mk');
            foreach ($result as $row) {
                if ($row->receive_from == 'agent' || $row->receive_from == 'Agent') {
                    $row->name = Agent::where('id', $row->agent_supplier_id)->value('name');
                } else {
                    $row->name = Supplier::where('id', $row->agent_supplier_id)->value('name');
                }
            }
        }


        // dd($result);
        $html = '
            
            
            <main class=" mx-auto w-[95%] ">
            
                <div class=" py-3 flex gap-y-4 mb-3">
                    <h2 class=" font-bold text-2xl">Payment Report</h2>
                    <!-- <button type="button" class="text-md bg-white px-3 font-medium rounded">Print</button> -->
                </div>
                <table class="table-auto w-full shadow-2xl">
                    <thead>
                    <tr class="bg-[#0E7490] text-white">
                    
                        <th class="px-4 py-2 text-md text-left">Date</th>
                        <th class="px-4 py-2 text-md text-left">Voucher No</th>
                        <th class="px-4 py-2 text-md text-left">Payment From</th>
                        <th class="px-4 py-2 text-md text-left">Payment Mode</th>
                        <th class="px-4 py-2 text-md text-left">Narration</th>
                        <th class="px-4 py-2 text-md text-left">Amount</th>
                        <th class="px-4 py-2 text-md text-center">Action</th>
                    </tr>
                   
                    </thead>
                    <tbody id="data" class="divide-y divide-gray-500">';
        foreach ($result as $key => $item) :
            $printUrl = url('/payment_voucher', ['id' => $item]);
            $deleteUrl = url('/delete_payment', ['id' => $item]);
            $html .= <<<HTML
                            <tr class="">
                                <td class="w-[10%] px-4 py-2 text-sm text-left"> $item->date </td>
                                <td class="w-[11%] px-4 py-2 text-sm text-left"> $item->invoice </td>
                                <td class="w-[15%] px-4 py-2 text-sm text-left"> $item->name </td>
                                <td class="w-[28%] px-4 py-2 text-sm text-left">
                                $item->method_name
                                </td>
                                <td class="w-[12%] px-4 py-2 text-sm text-left"> $item->remark </td>
                                <td class="w-[12%] px-4 py-2 text-sm text-left amount">$item->amount</td>
                                <!-- <td class="w-[12%] text-center"> $item->previous_amount  Dr</td> -->
                                <td class="px-2 py-1 text-center flex justify-center gap-2"><a href='$printUrl' class=" text-black  rounded-md text-md"><i class="fa fa-fw fa-print text-md"></i></a><a href=''>
                                <button type="button" id="deleteBtn" class="text-black rounded-md text-md text-danger">
                                    <i class="fa fa-trash fa-fw text-md"></i>
                                </button>
                                </a></td>
                            </tr>
                            HTML;
        endforeach;

        $html .= ' 

                        <tr>

                        <td class="px-4 py-2 text-left font-bold" colspan="3"></td>
                        <td class="px-4 py-2 text-left font-bold" colspan="2">Total Amount </td>
                        <td class="ml-5 font-bold text-md px-2 " id="total_amount"></td>
                        </tr>
                    </tbody>
                </table>
            </main>
            <script type="text/javascript">
                function calculateTotalAmount() {
                    const amountElements = document.querySelectorAll(".amount");
                
                    let totalAmount = 0;
                    
                    amountElements.forEach(element => {
                        // Parse the text content of the element to get the numeric value
                        const amount = parseFloat(element.textContent);
                        // Add the amount to the total
                        totalAmount += amount;
                    });
                    document.getElementById("total_amount").innerHTML = totalAmount;
                    
                    console.log("Total amount:", totalAmount);
                }
                calculateTotalAmount();
                $(document).ready(function(){
                    $("#deleteBtn").click(function(event){
                        event.preventDefault(); // Prevent the default action of the button
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won\'t be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, delete it!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Your item has been deleted.",
                                    icon: "success"
                                }).then(() => {
                                    // Redirect to the delete URL
                                    window.location.href = "'.$deleteUrl.'";
                                });
                            }
                        });
                    });
                });
                
                
            </script>
           
        ';

        return $html;
        }
        else{
            return view('welcome');
        }

    }

    public function payment_report_info(Request $request)
    {

        // dd($request->all());
        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        $voucher = $request->input('voucher') ?? null;

        $tableName = $customerid = null;
        if ($request->customer != null) {
            list($tableName, $customerid) = explode('_', $request->customer);
        }

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query1 = Payment::where('payment.user', $user);

        if ($tableName !== null) {
            $query1->where('receive_from', $tableName);
        }

        if ($customerid !== null) {
            $query1->where('agent_supplier_id', $customerid);
        }

        if ($request->method !== null) {
            $query1->where('method', $request->method);
        }
        if ($voucher !== null) {
            $query1->where('invoice', $voucher);
        }

        if ($start_date !== null && $end_date !== null) {
            $query1->whereBetween('date', [$start_date, $end_date]);
        } elseif ($start_date !== null) {
            $query1->whereDate('date', '>=', $start_date);
        } elseif ($end_date !== null) {
            $query1->whereDate('date', '<=', $end_date);
        }

        $query1->leftJoin('transaction as transaction_left', 'transaction_left.id', '=', 'payment.method');

        if ($tableName !== null) {
            $query1->leftJoin(DB::raw("$tableName as dynamicTable"), 'payment.agent_supplier_id', '=', 'dynamicTable.id')
                ->where('dynamicTable.user', $user)
                ->select('dynamicTable.name', 'payment.*', 'transaction_left.name as method_name');
        } else {
            $query1->select('payment.*', 'transaction_left.name as method_name');
        }

        $result = $query1->get();

        if ($tableName === null && $customerid === null) {
            // dd('mk');
            foreach ($result as $row) {
                if ($row->receive_from == 'agent' || $row->receive_from == 'Agent') {
                    $row->name = Agent::where('id', $row->agent_supplier_id)->value('name');
                } else {
                    $row->name = Supplier::where('id', $row->agent_supplier_id)->value('name');
                }
            }
        }


        // dd($result);
        $html = '
        <main class=" mx-auto w-[95%] ">
            <div class=" py-3 flex gap-y-4 mb-3">
                <h2 class=" font-bold text-2xl">Payment Report</h2>
                <!-- <button type="button" class="text-md bg-white px-3 font-medium rounded">Print</button> -->
            </div>
            <table class="table-auto w-full shadow-2xl">
                <thead>
                <tr class="bg-[#0E7490] text-white">
                    <th class="px-4 py-2 text-md text-left">Date</th>
                    <th class="px-4 py-2 text-md text-left">Voucher No</th>
                    <th class="px-4 py-2 text-md text-left">Payment From</th>
                    <th class="px-4 py-2 text-md text-left">Payment Mode</th>
                    <th class="px-4 py-2 text-md text-left">Narration</th>
                    <th class="px-4 py-2 text-md text-left">Amount</th>
                    <th class="px-4 py-2 text-md text-center">Action</th>
                </tr>
                </thead>
                <tbody id="data" class="divide-y divide-gray-500">';

        foreach ($result as $key => $item) {
            $printUrl = url('/payment_voucher', ['id' => $item->id]);
            $deleteUrl = url('/delete_payment', ['id' => $item->id]);

            $html .= <<<HTML
                <tr class="">
                    <td class="w-[10%] px-4 py-2 text-sm text-left"> $item->date </td>
                    <td class="w-[11%] px-4 py-2 text-sm text-left"> $item->invoice </td>
                    <td class="w-[15%] px-4 py-2 text-sm text-left"> $item->name </td>
                    <td class="w-[28%] px-4 py-2 text-sm text-left">
                    $item->method_name
                    </td>
                    <td class="w-[12%] px-4 py-2 text-sm text-left"> $item->remark </td>
                    <td class="w-[12%] px-4 py-2 text-sm text-left amount">$item->amount</td>
                    <td class="px-2 py-1 text-center flex justify-center gap-2">
                        <a href='$printUrl' class=" text-black rounded-md text-md">
                            <i class="fa fa-fw fa-print text-md"></i>
                        </a>
                        <button type="button" class="deleteBtn text-black rounded-md text-md text-danger" data-url='$deleteUrl'>
                            <i class="fa fa-trash fa-fw text-md"></i>
                        </button>
                    </td>
                </tr>
            HTML;
        }

        $html .= '
                <tr>
                    <td class="px-4 py-2 text-left font-bold" colspan="3"></td>
                    <td class="px-4 py-2 text-left font-bold" colspan="2">Total Amount </td>
                    <td class="ml-5 font-bold text-md px-2 " id="total_amount"></td>
                </tr>
            </tbody>
        </table>
        </main>
        <script type="text/javascript">
            function calculateTotalAmount() {
                const amountElements = document.querySelectorAll(".amount");
            
                let totalAmount = 0;
                
                amountElements.forEach(element => {
                    // Parse the text content of the element to get the numeric value
                    const amount = parseFloat(element.textContent);
                    // Add the amount to the total
                    totalAmount += amount;
                });
                document.getElementById("total_amount").innerHTML = totalAmount;
                
                console.log("Total amount:", totalAmount);
            }
            calculateTotalAmount();
            $(document).ready(function(){
                $(".deleteBtn").click(function(event){
                    event.preventDefault(); // Prevent the default action of the button
                    const deleteUrl = $(this).data("url");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won\'t be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your item has been deleted.",
                                icon: "success"
                            }).then(() => {
                                // Redirect to the delete URL
                                window.location.href = deleteUrl;
                            });
                        }
                    });
                });
            });
        </script>
        ';



        return $html;
        }
        else{
            return view('welcome');
        }

    }
    public function ait_report_index()
    {
        // dd('na');
        if(Auth::user()){
            return view('report.ait.index');
        }
        else{
            return view('welcome');
        }
        
    }

    public function ait_report_info(Request $request)
    {
        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();
        $query1 = DB::table('ait')
            ->leftJoin('tickets', 'ait.ticket_invoice', '=', 'tickets.invoice')
            ->where('ait.user', $user)
            ->select(
                'ait.*', 
                'tickets.*'
            );

        if ($start_date) {
            $query1->whereDate('ait.created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query1->whereDate('ait.created_at', '<=', $end_date);
        }

        $result = $query1->get();
        // dd($result);
        $html = '
            <!doctype html>
            <html>
            
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" />
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                theme: {
                    extend: {
                    colors: {
                        clifford: "#da373d",
                    }
                    }
                }
                }
            </script>
            <style>
            
            </style>
            </head>
            
            <body class="flex ">
            
            <main class="flex-1 mx-auto max-w-7xl px-10">
           
            <div class="">
                 <h2 class="text-center font-light text-3xl my-2">SALLU AIR SERVICE</h2>
                 <h2 class="text-center font-bold text-xl my-2 underline">AIT Report</h2>
                 <div class="flex items-center w-[35%] mx-auto justify-between mb-2">
                     <div class="text-md">
                         <p><span class="font-semibold">Period Date :</span>';
        $start_date;
        $html .= '';
        $end_date;
        $html .= '</p> 
                         <p>From Date : <span class="font-semibold">';
        $html .= $start_date;
        $html .= '</span></p>
                     </div>
                     <div class="text-md">
                         <p>To Date : <span class="font-semibold">';
        $html .= $end_date;
        $html .= '</span></p>
                         
                     </div>
                 </div>
                 <!-- <p class="">From Date : 14-09-2024 </p> -->
                 <table class=" table-auto w-[100%] mx-auto border-2 border-gray-400 devide-2 text-sm my-1">
                     <thead>
                       <tr class="border-y-2 border-black bg-cyan-700 text-white">
                         <th class="text-start">SL</th>
                         <th class="text-start">Ticket No</th>
                         <th class="text-start">Air Name</th>
                         <th class="text-center">AIT AMOUNT</th>
                         <th class="text-center"> AMOUNT</th>
                        
                        
                       </tr>
                     </thead>
                     <tbody class="divide-y-2">';
        foreach ($result as $index => $item) :
            $index++;
            $html .= <<<HTML
                        
                       <tr class="">
                        <td>$index</td>
                        <td>$item->ticket_code - $item->ticket_no</td>
                        <td>$item->airline_name</td>
                        <td class="ait_amount text-center">$item->ait_amount</td>
                        <td class="amount text-center">$item->supplier_price</td>
                        <!-- <td class="px-2 py-1 flex gap-2"><button type="button" class="bg-green-700 text-white px-3 rounded-md text-sm">Print</button><button type="button" class="bg-red-700 text-white px-3 rounded-md text-sm">Download</button></td> -->
                       </tr>
                       HTML;
        endforeach;

        $html .= '
                       <tr class="bg-white text-black font-bold">
                        
                       
                        <td>Total Amount</td>
                        <td></td>
                        <td></td>
                        <td id="total_ait"></td>
                        <td id="total_amount"></td>
                        
                        
                       </tr>
                     
                     </tbody>
                   </table>
            </div>
         
         
           </main>
            
           <script type="text/javascript">
                function calculateTotalAmount() {
                    const amountElements = document.querySelectorAll(".amount");
                    const ait_amountElements = document.querySelectorAll(".ait_amount");
                
                    let totalAmount = 0;
                    let totalAmount_ait = 0;
                    
                    amountElements.forEach(element => {
                        // Parse the text content of the element to get the numeric value
                        const amount = parseFloat(element.textContent);
                        // Add the amount to the total
                        totalAmount += amount;
                    });
                    ait_amountElements.forEach(element => {
                        // Parse the text content of the element to get the numeric value
                        const amount = parseFloat(element.textContent);
                        // Add the amount to the total
                        totalAmount_ait += amount;
                    });
                    document.getElementById("total_amount").innerHTML = totalAmount;
                    document.getElementById("total_ait").innerHTML = totalAmount_ait;
                    
                    // console.log("Total amount:", totalAmount);
                }
                calculateTotalAmount();
                
                
            </script>
            <script src="https://unpkg.com/flowbite@1.4.0/dist/flowbite.js"></script>
            </body>
            
            </html>
        ';

        return $html;
        }
        else{
            return view('welcome');
        }

    }
    // public function due_reminder()
    // {
    //     if (Auth::user()) {
    //         $userId = Auth::id();

    //         // Fetch all active and non-deleted agents and suppliers
    //         $agents = Agent::where([
    //             ['is_delete', 0],
    //             ['is_active', 1],
    //             ['user', $userId]
    //         ])->get();

    //         $suppliers = Supplier::where([
    //             ['is_delete', 0],
    //             ['is_active', 1],
    //             ['user', $userId]
    //         ])->get();

    //         $latestTransactions = [];

    //         // Get latest transactions for agents
    //         foreach ($agents as $agent) {
    //             $latestReceive = Receiver::where('agent_supplier_id', $agent->id)
    //                 ->where('receive_from', 'agent')
    //                 ->orderBy('created_at', 'desc')
    //                 ->first();

    //             $latestPayment = Payment::where('agent_supplier_id', $agent->id)
    //                 ->where('receive_from', 'agent')
    //                 ->orderBy('created_at', 'desc')
    //                 ->first();

    //             $latestTransactions[] = [
    //                 'entity' => $agent,
    //                 'latest_receive' => $latestReceive,
    //                 'latest_payment' => $latestPayment,
    //                 'type' => 'agent'
    //             ];
    //         }

    //         // Get latest transactions for suppliers
    //         foreach ($suppliers as $supplier) {
    //             $latestReceive = Receiver::where('agent_supplier_id', $supplier->id)
    //                 ->where('receive_from', 'supplier')
    //                 ->orderBy('created_at', 'desc')
    //                 ->first();

    //             $latestPayment = Payment::where('agent_supplier_id', $supplier->id)
    //                 ->where('receive_from', 'supplier')
    //                 ->orderBy('created_at', 'desc')
    //                 ->first();

    //             $latestTransactions[] = [
    //                 'entity' => $supplier,
    //                 'latest_receive' => $latestReceive,
    //                 'latest_payment' => $latestPayment,
    //                 'type' => 'supplier'
    //             ];
    //         }

    //         // Prepare transactions with names and other details
    //         $filteredTransactionsWithNames = [];
    //         foreach ($latestTransactions as $transaction) {
    //             $entity = $transaction['entity'];
    //             $type = $transaction['type'];
    //             $receiveFrom = $type == 'agent' ? 'agent' : 'supplier';
    //             $latestReceive = $transaction['latest_receive'];
    //             $latestPayment = $transaction['latest_payment'];

    //             if($type === 'agent'){
    //                 $due_amount = Agent::where('id', $entity->id)->value('amount');
    //             }
    //             else{
    //                 $due_amount = Supplier::where('id', $entity->id)->value('amount');
    //             }
    //             $transactionDetails = [
    //                 'agent_supplier_id' => $entity->id,
    //                 'agent_supplier_name' => $entity->name . ' (<span style="color: red;">' . $receiveFrom . '</span>)',
    //                 'agent_supplier_email' => $entity->email,
    //                 'agent_supplier_phone' => $entity->phone,
    //                 'agent_supplier_company' => $entity->company,
    //                 'due_amount' => $due_amount,
    //                 'date' => $latestReceive ? $latestReceive->created_at : ($latestPayment ? $latestPayment->created_at : null),
    //                 'amount' => $latestReceive ? $latestReceive->amount : ($latestPayment ? $latestPayment->amount : null)
    //             ];

    //             $filteredTransactionsWithNames[] = $transactionDetails;
    //         }

    //         return view('report.due_reminder.DueReminder', compact('filteredTransactionsWithNames', 'agents', 'suppliers'));
    //     } else {
    //         return view('welcome');
    //     }
    // }
    public function due_reminder()
    {
        if (Auth::user()) {
            $userId = Auth::id();
    
            // Fetch all active and non-deleted agents and suppliers
            $agents = Agent::where([
                ['is_delete', 0],
                ['is_active', 1],
                ['user', $userId]
            ])->get();
    
            $suppliers = Supplier::where([
                ['is_delete', 0],
                ['is_active', 1],
                ['user', $userId]
            ])->get();
    
            $latestTransactions = [];
            $filteredTransactionsWithNames = [];
    
            // Get latest transactions for agents
            foreach ($agents as $agent) {
                $latestReceive = Receiver::where('agent_supplier_id', $agent->id)
                    ->where('receive_from', 'agent')
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                $latestPayment = Payment::where('agent_supplier_id', $agent->id)
                    ->where('receive_from', 'agent')
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                $latestTransactions[] = [
                    'entity' => $agent,
                    'latest_receive' => $latestReceive,
                    'latest_payment' => $latestPayment,
                    'type' => 'agent'
                ];
            }
    
            // Get latest transactions for suppliers
            foreach ($suppliers as $supplier) {
                $latestReceive = Receiver::where('agent_supplier_id', $supplier->id)
                    ->where('receive_from', 'supplier')
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                $latestPayment = Payment::where('agent_supplier_id', $supplier->id)
                    ->where('receive_from', 'supplier')
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                $latestTransactions[] = [
                    'entity' => $supplier,
                    'latest_receive' => $latestReceive,
                    'latest_payment' => $latestPayment,
                    'type' => 'supplier'
                ];
            }
    
            // Prepare transactions with names and other details
            $concatenatedCollection = [];
            // dd($latestTransactions);
            foreach ($latestTransactions as $transaction) {
                $entity = $transaction['entity'];
                $type = $transaction['type'];
                $receiveFrom = $type == 'agent' ? 'agent' : 'supplier';
                $latestReceive = $transaction['latest_receive'];
                $latestPayment = $transaction['latest_payment'];
                
                $id = $entity->id;
                if ($type === 'agent') {
                    $orders = Order::where([['agent', $id], ['user', Auth::id()]])->get();
                    $tickets = Ticket::where([['agent', $id], ['is_active', 1], ['user', Auth::id()]])->get();
                    // dd($tickets);
                    $refunds = Refund::where('user', Auth::id())->where('agent', $id)->get();
                    $receivers = Receiver::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()]
                    ])->get();
                    $payments = Payment::where([
                        ['receive_from', '=', 'agent'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()]
                    ])->get();
                    $void_tickets = VoidTicket::where([['user', Auth::id()], ['agent', $id]])->get();
                    $reissues = ReissueTicket::where([['agent', $id], ['user', Auth::id()]])->get();
                } else {
                    $orders = Order::where([['supplier', $id], ['user', Auth::id()]])->get();
                    $tickets = Ticket::where([['supplier', $id], ['reissued_new_ticket', 1], ['user', Auth::id()]])->get();
                    $refunds = Refund::where('user', Auth::id())->where('supplier', $id)->get();
                    $receivers = Receiver::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()]
                    ])->get();
                    $payments = Payment::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()]
                    ])->get();
                    $void_tickets = VoidTicket::where([['user', Auth::id()], ['supplier', $id]])->get();
                    $reissues = ReissueTicket::where([['supplier', $id], ['user', Auth::id()]])->get();
                }
    
                // Concatenate all collections into a single collection
                $concatenatedCollection = $orders
                    ->concat($tickets)
                    ->concat($refunds)
                    ->concat($receivers)
                    ->concat($payments)
                    ->concat($void_tickets)
                    ->concat($reissues);
    
                $due_amount = 0;
                $amountTracking = []; // Array to track amounts per table
    
               
                foreach ($concatenatedCollection as $singlecollection) {
                    // dd($singlecollection);
                    $table = $singlecollection->getTable();
                    $amount = 0;
                
                    if ($table == 'order') {
                        // Agent amount calculation for 'order' table
                        $amount = $type === 'agent' ? $singlecollection->contact_amount : -$singlecollection->payable_amount;
                    } elseif ($table == 'tickets') {
                        // Agent amount calculation for 'tickets' table
                        $amount = $type === 'agent' ? $singlecollection->agent_price : -$singlecollection->supplier_price;
                    } elseif ($table == 'reissue') {
                        // Agent amount calculation for 'reissue' table
                        if ($type === 'agent') {
                            // if($id == 5){
                            //     dd($amount);
                            // }
                           
                            $amount += $singlecollection->now_agent_fere;
                        } else {
                            $amount -= $singlecollection->now_supplier_fare;
                        }
                    }  elseif ($table == 'refund') {
                        // Agent amount calculation for 'refund' table
                        if ($type === 'agent') {
                            // $amount -= $singlecollection->prev_agent_amount;
                            $amount -= $singlecollection->now_agent_fere;
                        } else {
                            $amount = $singlecollection->now_supplier_fare;
                        }
                    } elseif ($table == 'receive') {
                        // Agent amount calculation for 'receive' table
                        $amount = $type === 'agent' ? -$singlecollection->amount : -$singlecollection->amount;
                    } elseif ($table == 'payment') {
                        // Agent amount calculation for 'payment' table
                        $amount = $type === 'agent' ? $singlecollection->amount : $singlecollection->amount;
                    } elseif ($table == 'voidTicket') {
                        // Agent amount calculation for 'voidTicket' table
                        $amount = $type === 'agent' ? $singlecollection->now_agent_fere : -$singlecollection->now_supplier_fare;
                    }
                
                    $due_amount += $amount;
                    $amountTracking[] = ['table' => $table, 'amount' => $amount];
                }
                
    
                $transactionDetails = [
                    'agent_supplier_id' => $entity->id,
                    'agent_supplier_name' => $entity->name . ' (<span style="color: red;">' . $receiveFrom . '</span>)',
                    'agent_supplier_email' => $entity->email,
                    'agent_supplier_phone' => $entity->phone,
                    'agent_supplier_company' => $entity->company,
                    'due_amount' => $due_amount,
                    'date' => $latestReceive ? $latestReceive->created_at : ($latestPayment ? $latestPayment->created_at : null),
                    'amount' => $latestReceive ? $latestReceive->amount : ($latestPayment ? $latestPayment->amount : null),
                    'amountTracking' => $amountTracking // Add tracking details to the output
                ];
    
                // Uncomment this line if you need to debug specific transactions
                // if ($entity->id == 3 && $receiveFrom == 'agent') {
                //     dd($transactionDetails, $concatenatedCollection, $amountTracking);
                // }
                // dd( $amountTracking);
    
                $filteredTransactionsWithNames[] = $transactionDetails;
            }
    
            return view('report.due_reminder.DueReminder', compact('filteredTransactionsWithNames', 'agents', 'suppliers'));
        } else {
            return view('welcome');
        }
    }
    public function due_reminder_specific(Request $request)
    {
        if(Auth::user()){

            // dd($request->all());
            $supplierName = $request->supplierName;


            list($tableName, $clientID) = explode('_', $supplierName);
    
            $modelClassName = ucfirst($tableName);
    
            $model = app("App\\Models\\$modelClassName");
    
            $ssid = $model::where([
                ['is_delete', 0],
                ['is_active', 1],
                ['user', Auth::id()]
            ])->pluck('id');
    
    
            $ssid = $ssid->toArray();
    
            $latestReceives = [];
            $latestPayments = [];
    
    
            $latestReceive = Receiver::where('agent_supplier_id', $clientID)
                ->where('receive_from', $tableName)
                ->orderBy('created_at', 'desc')
                ->first(); // Retrieve the latest receive record for this agent
    
            $latestPayment = Payment::where('agent_supplier_id', $clientID)
                ->where('receive_from', $tableName)
                ->orderBy('created_at', 'desc')
                ->first(); // Retrieve the latest receive record for this agent
    
            $latestReceives[] = $latestReceive; // Add the latest receive record to the array
            $latestPayments[] = $latestPayment; // Add the latest receive record to the array
    
    
            $latestReceivesCollection = collect($latestReceives);
            $latestPaymentsCollection = collect($latestPayments);
    
            // Merge the collections
            $allTransactions = $latestReceivesCollection->merge($latestPaymentsCollection);
    
            // Sort the merged collection by created_at timestamp in descending order
            $latestTransaction = $allTransactions->sortByDesc('created_at');
    
            // Output the result
            $filteredTransactions = [];
    
            foreach ($latestTransaction as $transaction) {
                // Check if the transaction is not empty
                if (!empty($transaction)) {
                    // Check if there's already a transaction with the same receive_from and agent_supplier_id
                    $existingTransaction = collect($filteredTransactions)->first(function ($filteredTransaction) use ($transaction) {
                        return $filteredTransaction['receive_from'] == $transaction['receive_from']
                            && $filteredTransaction['agent_supplier_id'] == $transaction['agent_supplier_id'];
                    });
    
                    // If no existing transaction found, add the current transaction to the filtered transactions
                    if (!$existingTransaction) {
                        $filteredTransactions[] = $transaction;
                    }
                }
            }
    
            // Output the filtered transactions
            // dd($filteredTransactions);
            $filteredTransactionsWithNames = [];
    
            foreach ($filteredTransactions as $transaction) {
                // Determine the model based on the value of receive_from
                $modelName = ($transaction['receive_from'] === 'agent') ? 'App\Models\Agent' : 'App\Models\Supplier';
    
                // Retrieve the model instance
                $model = $modelName::find($transaction['agent_supplier_id']);
    
                // If the model instance is found, add its name to the transaction
                if ($model) {
                    $transaction['agent_supplier_name'] = $model->name;
                    $transaction['agent_supplier_email'] = $model->email;
                    $transaction['agent_supplier_phone'] = $model->phone;
                    $transaction['agent_supplier_company'] = $model->company;
                    $filteredTransactionsWithNames[] = $transaction;
                }
            }
            $agents = Agent::where('is_delete', 0)->where('user', Auth::id())->get();
            $suppliers = Supplier::where('is_delete', 0)->where('user', Auth::id())->get();
    
            return view('report.due_reminder.DueReminder', compact('filteredTransactionsWithNames', 'agents', 'suppliers'));
        }
        else{
            return view('welcome');
        }
       
    }
    public function sales_ticket()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $types = Type::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $orders = Order::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            return view('report.sales_ticket.index', compact('suppliers', 'agents', 'types'));
        }
        else{
            return view('welcome');
        }
        
    }
    public function sales_visa()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $types = Type::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $orders = Order::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            return view('report.sales_visa.index', compact('suppliers', 'agents', 'types'));
        }
        else{
            return view('welcome');
        }
        
    }


    public function segment_view()
    {
        if(Auth::user()){
            return view('report.segment.index');
        }
        else{
            return view('welcome');
        }
       
    }

    public function sector_city_view()
    {
        if(Auth::user()){
            $cities = Ticket::where([
                ['is_delete', 0],
                ['is_active', 1]
            ])
                ->distinct()
                ->pluck('s_from');
    
            $cities2 = Ticket::where([
                ['is_delete', 0],
                ['is_active', 1]
            ])
                ->distinct()
                ->pluck('e_to');
    
            // dd($cities, $cities2);
            return view('report.sector_city.index', compact('cities', 'cities2'));
        }
        else{
            return view('welcome');
        }

       
    }

    public function segment_report(Request $request)
    {
        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['user', $user],
            ]);


        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $alldata = $query->get();

        $totalCount = 0;
        $totalSupplierPrice = 0;

        foreach ($alldata as $data) {
            $sector = $data->sector;
            $parts = explode('-', $sector);
            $count = count($parts) - 1;
            $data->count = $count;
            $totalCount += $count;

            // Assuming $supplier_price is a property of the $data object
            $totalSupplierPrice += $data->supplier_price;
        }

        // dd($alldata);
        $htmlTable = '';

        $htmlTable = '
        <div class="bg-white p-4 shadow-xl">
        <h2 class="text-center font-bold text-3xl my-2">Segment Report </h2>
        <div class="flex items-center justify-between mb-2">
            <div class="text-lg">
                <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
                <p><span class="font-semibold">Period Date :</span>' . $start_date . ' to ' . $end_date . '</p> 
            </div>
            <div class="flex items-center">
                
                
            </div>
        </div>
        <table class="table-auto w-full bordered shadow-sm bg-white border-black text-sm my-1">
        <thead>
            <tr class="border-y-2 border-black bg-cyan-700 text-white">
                <th class="text-start px-2">SL</th>
                <th class="text-start">Ticket No</th>
                <th class="text-start">Passenger Name</th>
                <th class="text-start">Sector</th>
                <th class="text-start">GDS</th>
                <th class="text-start">Airlines</th>
                
                
                <th class="text-start">Sector No</th>
                <th class="text-start">Payable Amount</th>
                
            </tr>
        </thead>
        <tbody class="divide-y-2">';

        // Loop through each record in $alldata and add a row to the table
        foreach ($alldata as $index => $data) {
            $htmlTable .= '<tr>
                <td class="py-2 px-4>' . $index + 1 . '</td>
                <td class="py-2 px-4>' . $index + 1 . '</td>
               
                <td class="py-2">' . $data->ticket_code . '-' . $data->ticket_no . '</td>
                

                <td class="py-2">' . $data->passenger . '</td>
               
                <td class="py-2">' . $data->sector . '</td>
                <td class="py-2">' . '1G' . '</td>
                <td class="py-2">' . $data->airline_name . '</td>
                
                
                <td class="py-2">' . $data->count . '</td>
                <td class="py-2">' . $data->supplier_price . '</td>
               
            </tr>';
        }

        $htmlTable .= '<tr class="bg-green"> 
                        <td class="py-2"></td> 
                        <td class="py-2"></td> 
                        <td class="py-2"></td> 
                        <td class="py-2"></td> 
                        
                        <td colspan="2" class="font-bold text-md">Total Segment: </td>
                        <td class="py-2 font-bold">' . $totalCount . '</td>
                        <td class="py-2 font-bold">' . $totalSupplierPrice . '</td>
                        </tr>';

        // Close the HTML table
        $htmlTable .= '</tbody></table></div>';





        return $htmlTable;
        }
        else{
            return view('welcome');
        }

    }

    public function sector_city_report(Request $request)
    {
        if(Auth::user()){
            

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        $start = $request->input('start_from') ?? null;
        $end = $request->input('end_to') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['user', $user],
            ]);

        if ($start) {
            $query->where('s_from', $start);
        }

        if ($end) {
            $query->where('e_to', $end);
        }


        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $alldata = $query->get();

        $totalCount = 0;
        $totalSupplierPrice = 0;

        foreach ($alldata as $data) {
            $sector = $data->sector;
            $parts = explode('-', $sector);
            $count = count($parts) - 1;
            $data->count = $count;
            $totalCount += $count;

            // Assuming $supplier_price is a property of the $data object
            $totalSupplierPrice += $data->supplier_price;
        }

        // dd($alldata);
        $htmlTable = '';

        $htmlTable = '
        <h2 class="text-center font-bold text-3xl my-2">Segment Report </h2>
        <div class="flex items-center justify-between mb-2">
            <div class="text-lg">
                <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
                <p><span class="font-semibold">Period Date :</span>' . $start_date . ' to ' . $end_date . '</p> 
            </div>
            <div class="flex items-center">
                
                
            </div>
        </div>
        <table class="table-auto w-full border-2 border-gray-400 devide-2 text-sm my-1">
        <thead>
            <tr class="border-y-2 border-black bg-cyan-700 text-white">
                <th class="text-start px-2">SL</th>
                <th class="text-start">Ticket No</th>
                <th class="text-start">Passenger Name</th>
                <th class="text-start">Sector</th>
                <th class="text-start">GDS</th>
                <th class="text-start">Airlines</th>
                
                
                <th class="text-start">Sector No</th>
                <th class="text-start">Payable Amount</th>
                
            </tr>
        </thead>
        <tbody class="divide-y-2">';

        // Loop through each record in $alldata and add a row to the table
        foreach ($alldata as $index => $data) {
            $htmlTable .= '<tr>
                <td class="py-2 px-2">' . $index + 1 . '</td>
               
                <td py-2>' . $data->ticket_code . '-' . $data->ticket_no . '</td>
                <td py-2>' . $data->passenger . '</td>
               
                <td py-2>' . $data->sector . '</td>
                <td py-2>' . '1G' . '</td>
                <td py-2>' . $data->airline_name . '</td>
                
                
                <td py-2>' . $data->count . '</td>
                <td py-2>' . $data->supplier_price . '</td>
               
            </tr>';
        }

        $htmlTable .= '<tr class="bg-green"> 
                        <td py-2></td> 
                        <td py-2></td> 
                        <td py-2></td> 
                        <td py-2></td> 
                        
                        <td colspan="2" class="font-bold">Total Segment: </td>
                        <td class="py-2 font-bold">' . $totalCount . '</td>
                        <td class="py-2 font-bold">' . $totalSupplierPrice . '</td>
                        </tr>';

        // Close the HTML table
        $htmlTable .= '</tbody></table>';





        return $htmlTable;
        }
        else{
            return view('welcome');
        }
    }


   
    public function sales_report_ticket(Request $request)
    {
        if(Auth::user()){
            
        $agent = $request->input('agent') ?? null;
        $supplier = $request->input('supplier') ?? null;

        $show_profit = $request->input('show_profit') ?? null;
        $show_supplier = $request->input('show_supplier') ?? null;
        $show_agent = $request->input('show_agent') ?? null;

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['user', $user],
            ]);

        if ($agent !== null) {
            $query->where('agent', $agent);
        }

        if ($supplier !== null) {
            $query->where('supplier', $supplier);
        }

        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $alldata = $query->get();

        // dd(Auth::user()->name);
        // dd($alldata, $supplier, $agent);

        $htmlTable = '
            <h2 class="text-center font-bold text-3xl my-2">Sales Report (Ticket)</h2>
            <div class="flex items-center justify-between mb-2">
                <div class="text-lg">
                    <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
                    <p><span class="font-semibold">Period Date :</span>' . $start_date . ' to' . $end_date . ' </p>
                </div>
                <div class="flex items-center">
                    
                    
                </div>
            </div>
            <table class="table-auto w-full bordered shadow-xl bg-white border-black text-sm my-1">
            <thead>
            <tr class="border-y-2 border-black bg-cyan-700 text-white">
                <th class="text-start">Booking Date</th>
                <th class="text-start">Ticket No</th>
                <th class="text-start">Passenger Name</th>
                <th class="text-start">Flight Date</th>
                <th class="text-start">Sector</th>
                <th class="text-start">Airlines</th>
                ';

        if ($show_agent != null) {
            $htmlTable .= '
                    <th class="text-start">Agent</th>
                    <th class="text-start">Agent Price</th>
                    ';
        }
        if ($show_supplier != null) {
            $htmlTable .= '
                    <th class="text-start">Supplier</th>
                    <th class="text-start">Supplier Price</th>
                    ';
        }
        if ($show_profit != null) {
            $htmlTable .= '
                    <th class="text-start">Net Markup</th>                    
                    ';
        }

        $htmlTable .= '
                <th class="text-start">Balance Amount</th>
                    
                </tr>
                </thead>
             ';
        $total_agent_price = $total_supplier_price = $total_profit = $total_balance = $count = 0;
        $htmlTable .= '  
            <tbody class="divide-y-2">';

        // Loop through each record in $alldata and add a row to the table
        foreach ($alldata as $data) {
            $agent = Agent::where('id', $data->agent)->value('name');
            $supplier = Supplier::where('id', $data->supplier)->value('name');

            $total_agent_price += $data->agent_price;
            $total_supplier_price += $data->supplier_price;
            $total_profit += $data->profit;
            $total_balance += $data->agent_new_amount;
            $count++;

            $htmlTable .= '<tr>
                        <td class="py-2 pl-2">' . (new DateTime($data->invoice_date))->format('d-m-Y') . '</td>
                        <td class="py-2">' . $data->ticket_no . '</td>
                        <td class="py-2">' . $data->passenger . '</td>
                      
                        <td class="py-2">' . (new DateTime($data->flight_date))->format('d-m-Y') . '</td>
                        <td class="py-2">' . $data->sector . '</td>
                        <td class="py-2">' . $data->airline_name . '</td>';
            if ($show_agent != null) {
                $htmlTable .= '
                            <td class="text-start py-2">' . $agent . '</td>
                            <td class="text-start py-2">' . $data->agent_price . '</td>
                            ';
            }
            if ($show_supplier != null) {
                $htmlTable .= '
                            <td class="text-start py-2">' . $supplier . '</td>
                            <td class="text-start py-2">' . $data->supplier_price . '</td>
                            ';
            }
            if ($show_profit != null) {
                $htmlTable .= '
                            <td class="text-start py-2">' . $data->profit . '</td>
                            ';
            }
            $htmlTable .= '<td class="py-2">' . $data->agent_new_amount . '</td>
                    </tr>';
        }

        // Close the HTML table
        $htmlTable .= '
                <tr class="bg-dark-600" >
                    <td class="text-start py-2"><b> Total - ' . $count . ' </b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>';
        if ($show_agent != null) {
            $htmlTable .= '
                        <td></td>
                        <td class="text-start py-2"><b>' . $total_agent_price . '</td>';
        }

        if ($show_supplier != null) {
            $htmlTable .= '
                        <td></td>
                        <td class="text-start py-2"><b>' . $total_supplier_price . '</td>';
        }

        if ($show_profit != null) {
            $htmlTable .= '
                       
                        <td class="text-start py-2"><b>' . $total_profit . '</td>';
        }
        $htmlTable .= '
                         <td class="text-start py-2"><b>' . $total_balance . '</td>
                        </tr>
                        </tbody></table>';



        return $htmlTable;
        }
        else{
            return view('welcome');
        }
    }



    // public function sales_report_visa(Request $request)
    // {
    //     if(Auth::user()){
            
    //     $agent = $request->input('agent') ?? null;
    //     $supplier = $request->input('supplier') ?? null;
    //     $type = $request->input('type') ?? null;

    //     $show_profit = $request->input('show_profit') ?? null;
    //     $show_supplier = $request->input('show_supplier') ?? null;
    //     $show_agent = $request->input('show_agent') ?? null;

    //     $start_date = $request->input('start_date') ?? null;
    //     $end_date = $request->input('end_date') ?? null;

    //     if ($start_date) {
    //         $start_date = (new DateTime($start_date))->format('Y-m-d');
    //     }

    //     if ($end_date) {
    //         $end_date = (new DateTime($end_date))->format('Y-m-d');
    //     }

    //     $user = Auth::id();

    //     $query = DB::table('order')
    //         ->where([
    //             ['is_active', 1],
    //             ['is_delete', 0],
    //             ['user', $user],
    //         ]);

    //     if ($agent !== null) {
    //         $query->where('agent', $agent);
    //     }

    //     if ($supplier !== null) {
    //         $query->where('supplier', $supplier);
    //     }

    //     if ($type !== null) {
    //         $query->where('type', $type);
    //     }

    //     if ($start_date !== null && $end_date !== null) {
    //         $query->whereBetween('date', [$start_date, $end_date]);
    //     }
    //     $alldata = $query->get();

    //     // dd($alldata, $supplier, $agent);
    //     $htmlTable = '
    //         <h2 class="text-center font-bold text-3xl my-2">Sales Report (Visa)</h2>
    //             <div class="flex items-center justify-between mb-2">
    //                 <div class="text-lg">
    //                     <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
    //                     <p><span class="font-semibold">Period Date :</span> ' . $start_date . ' to ' . $end_date . ' </p>
    //                 </div>
    //                 <div class="flex items-center">
                       
                        
    //                 </div>
    //             </div>
    //             <table class="table-auto w-full bordered shadow-xl bg-white border-black text-sm my-1">
    //             <thead>
    //             <tr class="border-y-2 border-black bg-cyan-700 text-white ">
    //                 <th class="text-start">Booking Date</th>
    //                 <th class="text-start">Invoice No</th>
                   
    //                 <th class="text-start">Type</th>
    //                 <th class="text-start">Passenger Name</th>
    //                 <th class="text-start">Passport No</th>
    //                 <th class="text-start">Country</th>';
    //     if ($show_agent != null) {
    //         $htmlTable .= '
    //                     <th class="text-start">Agent</th>
    //                     <th class="text-start">Agent Price</th>
    //                     ';
    //     }
    //     if ($show_supplier != null) {
    //         $htmlTable .= '
    //                     <th class="text-start">Supplier</th>
    //                     <th class="text-start">Supplier Price</th>
    //                     ';
    //     }
    //     if ($show_profit != null) {
    //         $htmlTable .= '
    //                     <th class="text-start">Profit</th>
                    
    //                     ';
    //     }

    //     $htmlTable .= '  
                    
    //             </tr>
    //             </thead>
    //             <tbody class="border-y-2">';

    //     // Loop through each record in $alldata and add a row to the table
    //     foreach ($alldata as $data) {
    //         // dd($data->profit);
    //         $agent = Agent::where('id', $data->agent)->value('name');
    //         $supplier = Supplier::where('id', $data->supplier)->value('name');
    //         $type = Type::where('id', $data->type)->value('name');

    //         $htmlTable .= '<tr class="py-4 border-gray-300 border-y">
    //                     <td class="py-2 pl-2">' . (new DateTime($data->date))->format('d-m-Y') . '</td>
    //                     <td class="py-2">' . $data->invoice . '</td>
    //                     <td class="py-2">' . $type . '</td>
    //                     <td class="py-2">' . $data->name . '</td>
    //                     <td class="py-2">' . $data->passport_no . '</td>
    //                     <td class="py-2">' . $data->country . '</td>';
    //         if ($show_agent != null) {
    //             $htmlTable .= '
    //                         <td class="py-2">' . $agent . '</td>
    //                         <td class="py-2">' . $data->contact_amount . '</td>
    //                         ';
    //         }
    //         if ($show_supplier != null) {
    //             $htmlTable .= '
    //                         <td class="py-2">' . $supplier . '</td>
    //                         <td class="py-2">' . $data->payable_amount . '</td>
    //                         ';
    //         }
    //         if ($show_profit != null) {
    //             $htmlTable .= '
    //                         <td>' . $data->profit . '</td>
                         
    //                         ';
    //         }



    //         $htmlTable .= '</tr>';
    //     }

    //     // Close the HTML table
    //     $htmlTable .= '</tbody></table>';


    //     return $htmlTable;
    //     }
    //     else{
    //         return view('welcome');
    //     }
    // }
    public function sales_report_visa(Request $request)
    {
        if(Auth::user()){
            
        $agent = $request->input('agent') ?? null;
        $supplier = $request->input('supplier') ?? null;
        $type = $request->input('type') ?? null;

        $show_profit = $request->input('show_profit') ?? null;
        $show_supplier = $request->input('show_supplier') ?? null;
        $show_agent = $request->input('show_agent') ?? null;

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('order')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['user', $user],
            ]);

        if ($agent !== null) {
            $query->where('agent', $agent);
        }

        if ($supplier !== null) {
            $query->where('supplier', $supplier);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }

        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }
        $alldata = $query->get();

        // dd($alldata, $supplier, $agent);
        $htmlTable = '
            <h2 class="text-center font-bold text-3xl my-2">Sales Report (Visa)</h2>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-lg">
                        <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
                        <p><span class="font-semibold">Period Date :</span> ' . $start_date . ' to ' . $end_date . ' </p>
                    </div>
                    <div class="flex items-center">
                       
                        
                    </div>
                </div>
                <table class="table-auto w-full bordered shadow-xl bg-white border-black text-sm my-1">
                <thead>
                <tr class="border-y-2 border-black bg-cyan-700 text-white ">
                    <th class="text-start">Booking Date</th>
                    <th class="text-start">Invoice No</th>
                   
                    <th class="text-start">Type</th>
                    <th class="text-start">Passenger Name</th>
                    <th class="text-start">Passport No</th>
                    <th class="text-start">Country</th>';
        if ($show_agent != null) {
            $htmlTable .= '
                        <th class="text-start">Agent</th>
                        <th class="text-start">Agent Price</th>
                        ';
        }
        if ($show_supplier != null) {
            $htmlTable .= '
                        <th class="text-start">Supplier</th>
                        <th class="text-start">Supplier Price</th>
                        ';
        }
        if ($show_profit != null) {
            $htmlTable .= '
                        <th class="text-start">Profit</th>
                    
                        ';
        }

        $htmlTable .= '  
                    
                </tr>
                </thead>
                <tbody class="border-y-2">';

        // Loop through each record in $alldata and add a row to the table
        // Initialize total variables
        $totalAgentAmount = 0;
        $totalSupplierAmount = 0;
        $totalProfit = 0;

        foreach ($alldata as $data) {
            // Fetch related data
            $agent = Agent::where('id', $data->agent)->value('name');
            $supplier = Supplier::where('id', $data->supplier)->value('name');
            $type = Type::where('id', $data->type)->value('name');

            // Increment total amounts
            if ($show_agent != null) {
                $totalAgentAmount += $data->contact_amount;
            }
            if ($show_supplier != null) {
                $totalSupplierAmount += $data->payable_amount;
            }
            if ($show_profit != null) {
                $totalProfit += $data->profit;
            }

            // Build table row
            $htmlTable .= '<tr class="py-4 border-gray-300 border-y">
                            <td class="py-2 pl-2">' . (new DateTime($data->date))->format('d-m-Y') . '</td>
                            <td class="py-2">' . $data->invoice . '</td>
                            <td class="py-2">' . $type . '</td>
                            <td class="py-2">' . $data->name . '</td>
                            <td class="py-2">' . $data->passport_no . '</td>
                            <td class="py-2">' . $data->country . '</td>';
            if ($show_agent != null) {
                $htmlTable .= '
                            <td class="py-2">' . $agent . '</td>
                            <td class="py-2">' . $data->contact_amount . '</td>
                            ';
            }
            if ($show_supplier != null) {
                $htmlTable .= '
                            <td class="py-2">' . $supplier . '</td>
                            <td class="py-2">' . $data->payable_amount . '</td>
                            ';
            }
            if ($show_profit != null) {
                $htmlTable .= '
                            <td>' . $data->profit . '</td>
                            ';
            }
            $htmlTable .= '</tr>';
        }

        // Append the totals row
        $htmlTable .= '<tr class="py-4 border-gray-300 border-y font-bold">
                        <td class="py-2 pl-2" colspan="' . ($show_agent != null ? '7' : '5') . '">Total</td>';
        if ($show_agent != null) {
            $htmlTable .= '
                        <td class="py-2">' . $totalAgentAmount . '</td>';
        }
        if ($show_supplier != null) {
            
            $htmlTable .= '
            <td></td>
            
            <td class="py-2">' . $totalSupplierAmount . '</td>';
            
        }
        if ($show_profit != null)
        {
            $htmlTable .= '
                        
                        <td>' . $totalProfit . '</td>';
        }
        $htmlTable .= '</tr>';

        // Close the HTML table
        $htmlTable .= '</tbody></table>';


        return $htmlTable;
        }
        else{
            return view('welcome');
        }
    }
    
    public function void_ticket()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            // $types = Type::where([['is_delete',0],['is_active',1],['user',$user]])->get();
            // $orders = Order::where([['is_delete',0],['is_active',1],['user', $user]])->get();
            $query = DB::table('voidticket')
            ->leftJoin('tickets', function ($join) {
                $join->on('voidticket.ticket_no', '=', 'tickets.ticket_no');
            })
            ->where('voidticket.user', $user)
            ->select('voidticket.*', 'voidticket.date as void_date', 'tickets.*');

            $void_tickets = $query->get();
            return view('report.void_ticket.index', compact('suppliers', 'agents', 'void_tickets'));
        }
        else{
            return view('welcome');
        }
       
    }

    public function reissue_ticket()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
            $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();

            $query = DB::table('reissue')
            ->leftJoin('tickets', function ($join) {
                $join->on('reissue.ticket_no', '=', 'tickets.ticket_no')
                    ->where('tickets.ticket_code', '=', DB::raw('reissue.ticket_code'));
            })
            ->where('reissue.user', $user)
            ->select('reissue.*', 'reissue.date as reissue_date', 'tickets.*');

            $reissue_tickets = $query->get();

            // dd($reissue_tickets);

            return view('report.reissue_ticket.index', compact('suppliers', 'agents', 'reissue_tickets'));
        }
        else{
            return view('welcome');
        }
       
    }

    public function void_ticket_report(Request $request)
    {
        if(Auth::user()){
            
        $agent = $request->input('agent') ?? null;
        $supplier = $request->input('supplier') ?? null;

        $show_profit = $request->input('show_profit') ?? null;
        $show_supplier = $request->input('show_supplier') ?? null;
        $show_agent = $request->input('show_agent') ?? null;

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('voidticket')
            ->leftJoin('tickets', function ($join) {
                $join->on('voidticket.ticket_no', '=', 'tickets.ticket_no');
            })

            ->leftJoin('agent', 'voidticket.agent', '=', 'agent.id') // Join with agent table
            ->leftJoin('supplier', 'voidticket.supplier', '=', 'supplier.id') // Join with supplier table
            ->select('voidticket.*', 'agent.name as agent_name', 'supplier.name as supplier_name', 'tickets.*') // Select agent and supplier names
            ->where('voidticket.user', $user);
        
        if ($agent !== null) {
            $query->where('voidticket.agent', $agent);
        }
        
        if ($supplier !== null) {
            $query->where('voidticket.supplier', $supplier);
        }
        
        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('voidticket.date', [$start_date, $end_date]);
        }
        
        $alldata = $query->get();
        
        // dd($alldata);
        $html = ViewFacade::make('report.void_ticket.report', [
              
            'start_date' => $start_date,
            'end_date' => $end_date,
            'show_profit' => $show_profit,
            'show_agent' => $show_agent,
            'show_supplier' => $show_supplier,
            'alldata' => $alldata,
          
          
        ])->render();
        
        return response()->json(['html' => $html]);
        
        }
        else{
            return view('welcome');
        }

    }

    public function reissue_ticket_report(Request $request)
    {
        if(Auth::user()){
            
        $agent = $request->input('agent') ?? null;
        $supplier = $request->input('supplier') ?? null;

        $show_profit = $request->input('show_profit') ?? null;
        $show_supplier = $request->input('show_supplier') ?? null;
        $show_agent = $request->input('show_agent') ?? null;

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('reissue')
            ->leftJoin('tickets', function ($join) {
                $join->on('reissue.ticket_no', '=', 'tickets.ticket_no')
                    ->where('tickets.ticket_code', '=', DB::raw('reissue.ticket_code'));
            })
            ->leftJoin('agent', 'reissue.agent', '=', 'agent.id') // Join with agent table
            ->leftJoin('supplier', 'reissue.supplier', '=', 'supplier.id') // Join with supplier table
            ->select('reissue.*', 'agent.name as agent_name', 'supplier.name as supplier_name', 'tickets.*') // Select agent and supplier names
            ->where('reissue.user', $user);
        
        if ($agent !== null) {
            $query->where('reissue.agent', $agent);
        }
        
        if ($supplier !== null) {
            $query->where('reissue.supplier', $supplier);
        }
        
        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('reissue.date', [$start_date, $end_date]);
        }
        
        $alldata = $query->get();
    


        $html = ViewFacade::make('report.reissue_ticket.report', [
              
            'start_date' => $start_date,
            'end_date' => $end_date,
            'show_profit' => $show_profit,
            'show_agent' => $show_agent,
            'show_supplier' => $show_supplier,
            'alldata' => $alldata,
          
          
        ])->render();
        
        return response()->json(['html' => $html]);
        }
        else{
            return view('welcome');
        }

    }

    public function profit_loss_report(Request $request)
    {

        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],

                ['user', $user],
            ]);

        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $tickets = $query->sum('profit');
        $ticket_purchase = $query->sum('supplier_price');
        $ticket_sell = $query->sum('agent_price');

        $query2 = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['is_refund', 1],
                ['user', $user],
            ]);

        if ($start_date !== null && $end_date !== null) {
            $query2->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $refundticket = $query2->sum('refund_profit');
        $refund_purchase = $query2->sum('supplier_price');
        $refund_sell = $query2->sum('agent_price');

        $query3 = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['is_void', 1],
                ['user', $user],
            ]);

        if ($start_date !== null && $end_date !== null) {
            $query3->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $voidticket = $query3->sum('void_profit');
        $voidticket_purchase = $query3->sum('supplier_price');
        $voidticket_sell = $query3->sum('agent_price');

        $query4 = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['is_reissue', 1],
                ['user', $user],
            ]);

        if ($start_date !== null && $end_date !== null) {
            $query4->whereBetween('invoice_date', [$start_date, $end_date]);
        }

        $query5 = DB::table('order')
        ->where([
            ['is_active', 1],
            ['is_delete', 0],
            ['user', $user],
        ]);
        $orderprofit = $query5->sum('profit');

        $orderloss = DB::table('order')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->where('user', $user)
            ->where('profit', '<', 0) // Only consider negative profits
            ->sum('profit');
        $ticketloss = DB::table('tickets')
            ->where('is_active', 1)
            ->where('is_delete', 0)
            ->where('user', $user)
            ->where('profit', '<', 0) // Only consider negative profits
            ->sum('profit');

        $netloss = $orderloss + $ticketloss;
        $order_purchase = $query5->sum('payable_amount');
        $order_sell = $query5->sum('contact_amount');

        $reissueticket = $query4->sum('reissue_profit');
        $reissueticket_purchase = $query4->sum('supplier_price');
        $reissueticket_sell = $query4->sum('agent_price');

        $gross_profit = $tickets + $reissueticket + $refundticket + $voidticket + $orderprofit;
        $total_purchase = $ticket_purchase + $reissueticket_purchase + $refund_purchase + $voidticket_purchase + $order_purchase;
        $total_sell = $ticket_sell + $reissueticket_sell + $refund_sell + $voidticket_sell + $order_sell;
        // dd(Auth::user());
        // dd($reissueticket_purchase, $reissueticket_sell, $voidticket_sell, $voidticket_purchase, $refund_purchase, $refund_sell, $ticket_purchase, $ticket_sell);
        $htmlTable = '';

        $htmlTable = '
        <!doctype html>
            <html>

            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" />
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                theme: {
                    extend: {
                    colors: {
                        clifford: "#da373d",
                    }
                    }
                }
                }
            </script>
            <style>
            
            </style>
            </head>
        ';


        $htmlTable .= '
        <body class="flex">
          
          <main class=" mx-auto w-[100%] bg-white shadow-lg py-6">
           
            <div class=" px-7 py-3 flex flex-col gap-y-2 shadow-2xl">
                <h2 class="text-center font-medium text-2xl ">Company Name : '.Auth::user()->name.'</h2>
                <p class="text-center text-lg">Company Address : '.Auth::user()->company_address.'</p>
                <p class="text-center font-semibold text-xl underline">Report: Profit/Loss A/c</p>
                <p class="text-center font-semibold text-xl">Form Date : ' . $start_date .  ' To Date : ' . $end_date . '</p>
                
            </div>
            <div class="flex mb-10">
            <table class="table-auto w-full ">
                <thead>
                  <tr class="bg-[#0E7490] text-white flex justify-between">
                    <th class="px-4 py-2 text-left">Particular</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                   
                  </tr>
                </thead>
                <tbody id="data" class="text-lg px-2 border ">
                  <tr class="flex justify-between px-2 border-y">
                    <td class="px-2 py-2 font-semibold">Purchase Accounts</td>
                    <td class="px-2 py-2 font-semibold">' . $total_purchase . '</td>
                    
                  </tr>
                  <tr class="flex justify-between px-2 pl-8">
                    <td class="px-2  w-[30%]">Tickets</td>
                    <td class="px-2 ">' . $ticket_purchase . '</td>
                    <td class="px-2 "></td>
                    
                  </tr>
                  <tr class="flex justify-between px-2 pl-8">
                    <td class="px-2  w-[30%]">Visas</td>
                    <td class="px-2 ">' . $order_purchase . '</td>
                    <td class="px-2 "></td>
                    
                  </tr>
                 
               
                  <tr class="flex justify-between px-2 py-1">
                    <td class="px-2 py-2 w-[30%]">Gross Profit</td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2 py-2 font-semibold">' . $gross_profit . '</td>
                    
                  </tr>
                  <tr class="flex justify-between px-2 py-1">
                    <td class="px-2 py-2 w-[30%]"></td>
                    <td class="px-2 py-2"></td>
                    <td class="px-2  font-semibold border-y border-black"></td>
                    
                  </tr>
                  </tbody>
              </table>

                <table class="table-auto w-full">
                <thead>
                    <tr class="bg-[#0E7490] text-white flex justify-between">
                      <th class="px-4 py-2 text-left">Particular</th>
                      <th class="px-4 py-2 text-left">Amount</th>
                     
                    </tr>
                  </thead>
                  <tbody id="data" class="text-lg px-2 border ">
                    <tr class="flex justify-between px-2 border-y">
                        <td class="px-2 py-2 font-semibold">Sell Accounts</td>
                        <td class="px-2 py-2 font-semibold">' . $total_sell . '</td>
                    
                    </tr>
                    <tr class="flex justify-between px-2 pl-8">
                      <td class="px-2 w-[30%]">Tickets</td>
                      <td class="px-2">' . $ticket_sell . '</td>
                      <td class="px-2"></td>
                      
                    </tr>
                  
                    <tr class="flex justify-between px-2 pl-8">
                      <td class="px-2 w-[30%]">Visa</td>
                      <td class="px-2">'.$order_sell.'</td>
                      <td class="px-2"></td>
                      
                    </tr>
                
                 
                      <tr class="flex justify-between px-2 py-1">
                        
                        <td class="px-2 py-2 w-[30%]">Net Loss</td>
                        <td class="px-2 py-2 font-bold">'.$netloss.'</td>
                        <td class="px-2 py-2 font-semibold"></td>
                        
                      </tr>
                      
                    
                    
                    </tbody>
              </table>
            </div>
            <div class="w-[50%] flex justify-end">
            <p class="font-bold text-xl">Gross Profit : ' . $gross_profit . '</p>
            <p class="font-medium text-lg"></p>
            </div>
          </main>
          <script type="text/javascript">
            
            
          </script>
          <script src="https://unpkg.com/flowbite@1.4.0/dist/flowbite.js"></script>
        </body>
        ';
        $htmlTable .= '</html>';

        return $htmlTable;
        }
        else{
            return view('welcome');
        }
    }
    public function profit_loss_view()
    {
        if(Auth::user()){
            return view('report.profit_loss.index');
        }
        else{
            return view('welcome');
        }

    }
    public function sales_analysis()
    {
        if(Auth::user()){
            return view('report.sales_analysis.index');
        }
        else{
            return view('welcome');
        }
    }

    public function sales_analysis_report(Request $request)
    {
        if(Auth::user()){
            
        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query1 = Receiver::where('user', $user);
        $query2 = Payment::where('user', $user);
        $query3 = Ticket::where('user', $user);
        // $query4 = Ticket::where('user', $user);

        // Apply date checks if start_date and/or end_date are provided
        if ($start_date) {
            $query1->whereDate('date', '>=', $start_date);
            $query2->whereDate('date', '>=', $start_date);
            $query3->whereDate('invoice_date', '>=', $start_date);
            // $query4->whereDate('invoice_date', '>=', $start_date);
        }

        if ($end_date) {
            $query1->whereDate('date', '<=', $end_date);
            $query2->whereDate('date', '<=', $end_date);
            $query3->whereDate('invoice_date', '<=', $end_date);
            // $query4->whereDate('invoice_date', '<=', $end_date);
        }

        // Fetch results from the queries
        $results1 = $query1->get();
        $results2 = $query2->get();
        $results3 = $query3->get();
        // $results4 = $query4->get();


        $sumsByDay = [
            'receivetotalAmount' => [],
            'salestotalAmount' => [],
            'purchasetotalAmount' => [],
            'profittotalAmount' => [],
            'paymenttotalAmount' => []
        ];

        // Calculate sums day-wise
        foreach ($results1 as $result) {
            $createdAt = $result->date; // Assuming 'created_at' is a DateTime object
            $amount = $result->amount; // Assuming 'amount' is the attribute containing the amount value
            $type = 'receivetotalAmount'; // Assuming 'type' is the attribute indicating receive or payment

            // Initialize the sum for the day if it doesn't exist
            if (!isset($sumsByDay[$type][$createdAt])) {
                $sumsByDay[$type][$createdAt] = 0;
            }

            // Update the sum for the day
            $sumsByDay[$type][$createdAt] += $amount;
        }
        // Calculate sums day-wise
        foreach ($results2 as $result) {
            $createdAt = $result->date; // Assuming 'created_at' is a DateTime object
            $amount = $result->amount; // Assuming 'amount' is the attribute containing the amount value
            $type = 'paymenttotalAmount'; // Assuming 'type' is the attribute indicating receive or payment

            // Initialize the sum for the day if it doesn't exist
            if (!isset($sumsByDay[$type][$createdAt])) {
                $sumsByDay[$type][$createdAt] = 0;
            }

            // Update the sum for the day
            $sumsByDay[$type][$createdAt] += $amount;
        }

        foreach ($results3 as $result) {
            // Convert DateTime to string for array key
            $createdAt = $result->invoice_date; // Assuming 'invoice_date' is a DateTime object
            $sale = $result->agent_price; // Assuming 'agent_price' is the attribute containing the sales price
            $purchase = $result->supplier_price; // Assuming 'supplier_price' is the attribute containing the purchase price
            $profit = $result->profit; // Calculate profit
        
            // Initialize the sum for the day if it doesn't exist
            if (!isset($sumsByDay['salestotalAmount'][$createdAt])) {
                $sumsByDay['salestotalAmount'][$createdAt] = 0;
            }
            if (!isset($sumsByDay['purchasetotalAmount'][$createdAt])) {
                $sumsByDay['purchasetotalAmount'][$createdAt] = 0;
            }
            if (!isset($sumsByDay['profittotalAmount'][$createdAt])) {
                $sumsByDay['profittotalAmount'][$createdAt] = 0;
            }
        
            // Update the sums for the day
            $sumsByDay['salestotalAmount'][$createdAt] += $sale;
            $sumsByDay['purchasetotalAmount'][$createdAt] += $purchase;
            $sumsByDay['profittotalAmount'][$createdAt] += $profit;

            // dd($createdAt, $sale, $purchase, $profit);
        }
      

      

        // Step 1: Collect all unique dates
        $allDates = $this->getDateRange($start_date, $end_date);


        // Step 2: Iterate over all unique dates and build the table data
        $tableData = [];

        foreach ($allDates as $day) {
            $receivetotalAmount = isset($sumsByDay['receivetotalAmount'][$day]) ? $sumsByDay['receivetotalAmount'][$day] : 0;
            $salesAmount = isset($sumsByDay['salestotalAmount'][$day]) ? $sumsByDay['salestotalAmount'][$day] : 0;
            $purchaseAmount = isset($sumsByDay['purchasetotalAmount'][$day]) ? $sumsByDay['purchasetotalAmount'][$day] : 0;
            $profitAmount = isset($sumsByDay['profittotalAmount'][$day]) ? $sumsByDay['profittotalAmount'][$day] : 0;
            $paymentAmount = isset($sumsByDay['paymenttotalAmount'][$day]) ? $sumsByDay['paymenttotalAmount'][$day] : 0;

            $tableData[] = [
                'date' => $day,
                'receivetotalAmount' => $receivetotalAmount,
                'salestotalAmount' => $salesAmount,
                'purchasetotalAmount' => $purchaseAmount,
                'profittotalAmount' => $profitAmount,
                'paymenttotalAmount' => $paymentAmount,
            ];
        }

        // dd($allDates);
        // return view('report.sales_analysis.index', compact('tableData', 'start_date', 'end_date'));
        $html = ViewFacade::make('report.sales_analysis.sales_analysis', [
            'tableData' => $tableData,
            'start_date' => $start_date,
            'end_date' => $end_date
        ])->render();
        return response()->json(['html' => $html]);
    }
    else{
        return view('welcome');
    }
    }
    
    public function getDateRange($start_date, $end_date) {
        $dates = [];
        $current = strtotime($start_date);
        $end = strtotime($end_date);
    
        while ($current <= $end) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
    
        return $dates;
    }

    public function sales_exicutive_stuff()
    {
        if(Auth::user()){
        $user = Auth::id();
        $stuffs = Ticket::where('is_delete', 0)
            ->where('is_active', 1)
            ->distinct()
            ->pluck('stuff');

        $suppliers = Supplier::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();
        $agents = Agent::where([['is_delete', 0], ['is_active', 1], ['user', $user]])->get();

        // dd($stuffs);
        return view('report.sales_exicutive_stuff.index', compact('stuffs', 'agents', 'suppliers'));
        }

        else{
            return view('welcome');
        }
    }

    public function seles_executive_report_stuff(Request $request)
    {
        if(Auth::user()){
            // dd($request->all());
        $stuff = $request->stuff;

        $agent = $request->input('agent') ?? null;
        $supplier = $request->input('supplier') ?? null;

        $show_profit = $request->input('show_profit') ?? null;
        $show_supplier = $request->input('show_supplier') ?? null;
        $show_agent = $request->input('show_agent') ?? null;

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;

        if ($start_date) {
            $start_date = (new DateTime($start_date))->format('Y-m-d');
        }

        if ($end_date) {
            $end_date = (new DateTime($end_date))->format('Y-m-d');
        }

        $user = Auth::id();

        $query = DB::table('tickets')
            ->where([
                ['is_active', 1],
                ['is_delete', 0],
                ['user', $user],
            ]);

        if ($stuff !== null) {
            $query->where('stuff', $stuff);
        }

        if ($agent !== null) {
            $query->where('agent', $agent);
        }

        if ($supplier !== null) {
            $query->where('supplier', $supplier);
        }

        if ($start_date !== null && $end_date !== null) {
            $query->whereBetween('invoice_date', [$start_date, $end_date]);
        }
        $alldata = $query->get();
        $groupedData = $alldata->groupBy('stuff');

        // dd($groupedData);
        // dd($groupedData, $supplier, $agent, $stuff, $show_profit, $show_supplier, $show_agent);
        $htmlTable = '';

        // dd("asffa");
        // if($show_profit != null && $show_supplier == null && $show_agent == !null){
        // dd("asda");
        $htmlTable = ''; // Initialize variable to hold HTML tables
        $htmlTable .= '<h2 class="text-center font-bold text-3xl my-2">Sales Exicutive Report</h2>
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-lg">
                            <h2 class="font-semibold">Company Name : '.Auth::user()->name.'</h2>
                            <p><span class="font-semibold">Period Date :</span>' . $start_date . ' to ' . $end_date . '</p> 
                            </div>
                        <div class="flex items-center">
                           
                            
                        </div>
                    </div>';
        // Loop through each group
        // dd($groupedData);
        foreach ($groupedData as $stuff => $group) {
            $total_agent_price = $total_supplier_price = $total_profit = $total_balance = $count = 0;
            // dd($group, $stuff);
            // Add stuff name as a banner above the tickets

            $htmlTable .= '<div style="margin-bottom: 20px;">';
            $htmlTable .= '<h2 class="text-start uppercase" style="font-weight:bold;" >' . $stuff . '</h2>';

            // Start a new table for each group
            $htmlTable .= '<table border="1" class="table-auto w-full bordered shadow-xl bg-white border-black text-sm my-1">
                        <thead>
                            <tr class="border-y-2 border-black bg-cyan-700 text-white">
                                <th class="text-start">Booking Date</th>
                                <th class="text-start">Ticket No</th>
                                <th class="text-start">Passenger Name</th>
                                <th class="text-start">Flight Date</th>
                                <th class="text-start">Sector</th>
                                <th class="text-start">Airlines</th>';
            if ($show_agent != null) {
                $htmlTable .= '
                                        <th class="text-start max-w-[150px]">Agent</th>
                                        <th class="text-start">Agent Amount</th>';
            }
            if ($show_supplier != null) {
                $htmlTable .= '
                                        <th class="text-start max-w-[150px]">Supplier</th>
                                        <th class="text-start">Supplier Amount</th>';
            }
            if ($show_profit != null) {
                $htmlTable .= '
                                    <th class="text-start">Net Markup</th>';
            }
            $htmlTable .= '
                                <th class="text-start">Balance Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2">';

            // Loop through each record in the group and add a row to the table
            foreach ($group as $data) {
                // dd($data);
                if ($data->stuff == $stuff) {

                    $count++;
                    $total_agent_price += $data->agent_price;
                    $total_supplier_price += $data->supplier_price;
                    $total_profit += $data->profit;
                    $total_balance += $data->agent_new_amount;
                }
                $htmlTable .= '<tr>
                            <td class="py-2">' . (new DateTime($data->invoice_date))->format('d-m-Y') . '</td>
                            <td class="py-2">' . $data->ticket_no . '</td>
                            <td class="py-2">' . $data->passenger . '</td>
                            <td class="py-2">' . (new DateTime($data->flight_date))->format('d-m-Y') . '</td>
                            <td class="py-2">' . $data->sector . '</td>
                            <td class="py-2">' . $data->airline_name . '</td>';
                if ($show_agent != null) {
                    $htmlTable .= '
                                    <td class="py-2 max-w-[150px]">' . Agent::where('id', $data->agent)->value('name') . '</td>
                                    <td class="text-center">' . $data->agent_price . '</td>';
                }
                if ($show_supplier != null) {
                    $htmlTable .= '
                                    <td class="py-2 max-w-[150px]">' . Supplier::where('id', $data->supplier)->value('name') ."-". Supplier::where('id', $data->supplier)->value('company') . '</td>
                                    <td class="text-center">' . $data->supplier_price . '</td>';
                }
                if ($show_profit != null) {
                    $htmlTable .= '
                                    <td class="text-center">' . $data->profit . '</td>';
                }
                $htmlTable .= '
                            <td class="py-2 text-center">' . $data->agent_new_amount . '</td>
                        </tr>';
            }
            $htmlTable .= '<tr class="w-100 py-2" style="background:white; "><td><b>Total Ticket: ' . $count . '</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>';
            if ($show_agent != null) {
                $htmlTable .= '
                        <td></td>
                        <td class="text-center py-2"><b>' . $total_agent_price . '</td>';
            }

            if ($show_supplier != null) {
                $htmlTable .= '
                        <td></td>
                        <td class="text-center py-2"><b>' . $total_supplier_price . '</td>';
            }

            if ($show_profit != null) {
                $htmlTable .= '
                       
                        <td class="text-center py-2"><b>' . $total_profit . '</td>';
            }
            $htmlTable .= '
                         <td class="text-center py-2"><b>' . $total_balance . '</td>
                        
                    </tr>';

            // Close the HTML table and the stuff banner
            $htmlTable .= '</tbody></table>';
            $htmlTable .= '</div>';
        }
        // dd($htmlTable);
        // }


        return $htmlTable;
        }
        else{
            return view('welcome');
        }
    }
    public function bank_book(){
        if(Auth::user()){
            $methods = Transaction::where([['user', Auth::id()],['name', '!=', 'CASH']])->get();
            return view('report.bankbook.index', compact('methods'));
        }
        else{
            return view('welcome'); 
        }
        
    }

    public function cash_book(){
        if(Auth::user()){
            $methods = Transaction::where([['user', Auth::id()],['name', '=', 'CASH']])->first();
            // dd($methods);
            return view('report.cashbook.index', compact('methods'));
        }
        else{
            return view('welcome'); 
        }
        
    }

    public function bank_book_report(Request $request){

        if(Auth::user()){
            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;
            $method = $request->input('method') ?? null;
    
            if ($start_date) {
                $start_date = (new DateTime($start_date))->format('Y-m-d');
            }
            
            if ($end_date) {
                $end_date = (new DateTime($end_date))->format('Y-m-d');
            }
            
            $user = Auth::id();
            
            $query1 = Receiver::where('user', $user);
            $query2 = Payment::where('user', $user);
    
            if ($start_date) {
                $query1->whereDate('date', '>=', $start_date);
                $query2->whereDate('date', '>=', $start_date);
                
            }
            
            if ($end_date) {
                $query1->whereDate('date', '<=', $end_date);
                $query2->whereDate('date', '<=', $end_date);
               
            }
    
            if($method) {
                $query1->where('method',  $method);
                $query2->where('method',  $method);
            }
    
            $receive = $query1->get();
            $payment = $query2->get();
    
            $merged = $receive->concat($payment);
    
            $sorted = $merged->sortBy('date');
    
            foreach ($sorted as $data){
                $data->method = Transaction::where('id', $data->method)->value('name');
                $data->date = (new DateTime($data->date))->format('d/m/Y');
                if($data->receive_from == 'agent'){
                    $data->name = Agent::where('id', $data->agent_supplier_id)->value('name');
                }
                else{
                    $data->name = Supplier::where('id', $data->agent_supplier_id)->value('name');
     
                }
            }
            // dd($sorted);
            $html = ViewFacade::make('report.bankbook.bankbook', [
                'datas' => $sorted,
                'start_date' => $start_date,
                'end_date' => $end_date
            ])->render();
            return response()->json(['html' => $html]);
        }
        else{
            return view('welcome'); 
        }
       
    }
    

    public function cash_book_report(Request $request){

        if(Auth::user()){
            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;
           
            $method = $request->input('method') ?? null;
           
            if ($start_date) {
                $start_date = (new DateTime($start_date))->format('Y-m-d');
            }
            
            if ($end_date) {
                $end_date = (new DateTime($end_date))->format('Y-m-d');
            }
            
            $user = Auth::id();
            
            $query1 = Receiver::where('user', $user);
            $query2 = Payment::where('user', $user);
    
            if ($start_date) {
                $query1->whereDate('date', '>=', $start_date);
                $query2->whereDate('date', '>=', $start_date);
                
            }
            
            if ($end_date) {
                $query1->whereDate('date', '<=', $end_date);
                $query2->whereDate('date', '<=', $end_date);
               
            }

            if($method) {
                $query1->where('method',  $method);
                $query2->where('method',  $method);
            }
    
            $receive = $query1->get();
            $payment = $query2->get();
    
            $merged = $receive->concat($payment);
    
            $sorted = $merged->sortBy('date');
    
            foreach ($sorted as $data){
                $data->method = Transaction::where('id', $data->method)->value('name');
                $data->date = (new DateTime($data->date))->format('d/m/Y');
                if($data->receive_from == 'agent'){
                    $data->name = Agent::where('id', $data->agent_supplier_id)->value('name');
                }
                else{
                    $data->name = Supplier::where('id', $data->agent_supplier_id)->value('name');
     
                }
            }
            // dd($sorted);
            $html = ViewFacade::make('report.cashbook.cashbook', [
                'datas' => $sorted,
                'start_date' => $start_date,
                'end_date' => $end_date
            ])->render();
            return response()->json(['html' => $html]);
        }
        else{
            return view('welcome'); 
        }
       
    }

    public function dailystate(){
        if(Auth::user()){
            return view('report.dailystate.index');
        }
        else{
            return view('welcome'); 
        }
    }

    public function dailystate_report(Request $request){
        if(Auth::user()){
            $date = $request->input('date') ?? null;
    
            if ($date) {
                $date = (new DateTime($date))->format('Y-m-d');
            }

            $user = Auth::id();
            
            $query1 = Receiver::where('user', $user);
            $query2 = Payment::where('user', $user);
    
            if ($date) {
                $query1->where('date', '=', $date);
                $query2->where('date', '=', $date);
                
            }
            $receive = $query1->get();
            $payment = $query2->get();
    
            $merged = $receive->concat($payment);
    
            $sorted = $merged->sortBy('date');
    
            foreach ($sorted as $data){
                $data->method = Transaction::where('id', $data->method)->value('name');
                $data->date = (new DateTime($data->date))->format('d/m/Y');
                if($data->receive_from == 'agent'){
                    $data->name = Agent::where('id', $data->agent_supplier_id)->value('name');
                }
                else{
                    $data->name = Supplier::where('id', $data->agent_supplier_id)->value('name');
     
                }
            }
            // dd($sorted);
            $html = ViewFacade::make('report.dailystate.dailystate', [
                'datas' => $sorted,
                'date' => $date
            ])->render();
            
            return response()->json(['html' => $html]);


        }
        else{
            return view('welcome');
        }
    }

    public function profitreport_view(){
        if(Auth::user()){
         
            return view('report.profitreport.index');
        }
        else{
            return view('welcome');
        }
    }

    public function profitreport(Request $request){
        if(Auth::user()){
            $user = Auth::id();
            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;

               
            if ($start_date) {
                $start_date = (new DateTime($start_date))->format('Y-m-d');
            }
            
            if ($end_date) {
                $end_date = (new DateTime($end_date))->format('Y-m-d');
            }
            
            if ($start_date && $end_date) {
                $tickets = Ticket::where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['user', $user]
                ])->whereBetween('invoice_date', [$start_date, $end_date])->get();
            } elseif ($start_date) {
                $tickets = Ticket::where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['user', $user],
                    ['invoice_date', '>=', $start_date]
                ])->get();
            } elseif ($end_date) {
                $tickets = Ticket::where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['user', $user],
                    ['invoice_date', '<=', $end_date]
                ])->get();
            } else {
                $tickets = Ticket::where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['user', $user]
                ])->get();
            }
            
            
            $countTicket = $tickets->count();
            $selling_price = $tickets->pluck('agent_price')->sum();
            $buying_price = $tickets->pluck('supplier_price')->sum();
            $ticket_profit = $tickets->pluck('profit')->sum();
            
            $from_ticket = [
                'name' => 'Tickets',
                'count' => $countTicket,
                'buying_price' => $buying_price,
                'selling_price' => $selling_price,
                'profit' => $ticket_profit
            ];
            
            $types = Type::where([
                ['user', $user],
                ['is_active', 1],
                ['is_delete', 0]
            ])->pluck('id', 'name');
            
            $orders = Order::where([
                ['user', $user],
                ['is_delete', 0],
                ['is_active', 1]
            ]);

            if ($start_date) {
                $orders->whereDate('date', '>=', $start_date);
            }
            
            if ($end_date) {
                $orders->whereDate('date', '<=', $end_date);
            }
            
            $typeData = [];
            
            foreach($types as $typeName => $typeId){
                $typeOrders = $orders->where('type', $typeId)->get();
                //  dd($typeOrders);
                $count = $typeOrders->count();
                $selling_price = $typeOrders->pluck('contact_amount')->sum();
                $buying_price = $typeOrders->pluck('payable_amount')->sum();
                $single_profit = $typeOrders->pluck('profit')->sum();
            
                $typeData[] = [
                    'name' => $typeName,
                    'count' => $count,
                    'buying_price' => $buying_price,
                    'selling_price' => $selling_price,
                    'profit' => $single_profit
                ];
            }
            
            // $typeData now contains the calculated data for each type
            $typedatalength = count($typeData);
            // dd($typedatalength, $typeData);
            $typeData[$typedatalength] = $from_ticket;   
                        // dd($from_ticket, $typeData);
            $html = ViewFacade::make('report.profitreport.ProfitReport', [
                'typeData' => $typeData,
                'start_date' => $start_date,
                'end_date' => $end_date
            ])->render();
            return response()->json(['html' => $html]);
            // return view('report.profitreport.ProfitReport', compact('typeData'));
        }
        else{
            return view('welcome');
        }
    }
    public function trialbalance(){
        if(Auth::user()){
            return view('report.trailbalance.index');
        }
        else{
            return view('welcome');
        }
    }

    public function trialbalance_report(Request $request){
        if(Auth::user()){
            // dd($request->all());
            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;

               
           // Convert start_date and end_date to DateTime objects if they are provided
            if ($start_date) {
                $start_date = (new DateTime($start_date))->format('Y-m-d');
            }

            if ($end_date) {
                $end_date = (new DateTime($end_date))->format('Y-m-d');
            }

            // Get the authenticated user ID
            $user = Auth::id();

            // Initialize query builders for each model
            $receives = Receiver::where('user', $user);
            $payments = Payment::where('user', $user);
            $tickets = Ticket::where([['user', $user],['is_delete',0]]);
            $orders = Order::where([['user', $user],['is_delete',0]]);


            // Apply date-wise search if start_date and end_date are available
            if ($start_date && $end_date) {
                $receives->whereBetween('date', [$start_date, $end_date]);
                $payments->whereBetween('date', [$start_date, $end_date]);
                $tickets->whereBetween('invoice_Date', [$start_date, $end_date]);
                $orders->whereBetween('date', [$start_date, $end_date]);
            } elseif ($start_date) {
                // Apply only start_date filter if available
                $receives->where('date', '>=', $start_date);
                $payments->where('date', '>=', $start_date);
                $tickets->where('invoice_date', '>=', $start_date);
                $orders->where('date', '>=', $start_date);
            } elseif ($end_date) {
                // Apply only end_date filter if available
                $receives->where('date', '<=', $end_date);
                $payments->where('date', '<=', $end_date);
                $tickets->where('invoice_date', '<=', $end_date);
                $orders->where('date', '<=', $end_date);
            }

            // Execute queries to retrieve data
            $receivesData = $receives->get();
            $paymentsData = $payments->get();
            // $ticketsData = $tickets->get();
            // $ordersData = $orders->get();
            // Get the sum of amount for receives
          
            // Get the sum of agent_price for tickets
            $totalTicketAgentPrice = $tickets->sum('agent_price');

            // Get the sum of supplier_price for tickets
            $totalTicketSupplierPrice = $tickets->sum('supplier_price');

            // Get the sum of agent_price for orders
            $totalOrderAgentPrice = $orders->sum('contact_amount');

            // Get the sum of supplier_price for orders
            $totalOrderSupplierPrice = $orders->sum('payable_amount');

            // Initialize arrays to store totals
            $transactionMethods = Transaction::where('user', $user)->distinct()->pluck('name', 'id');

            $totals = [];
            $totalDebit = 0;
            $totalCredit = 0;

            $totalDebit += $totalOrderAgentPrice + $totalTicketAgentPrice;
            $totalCredit += $totalOrderSupplierPrice + $totalTicketSupplierPrice;

            foreach ($transactionMethods as $id => $name) {
                $totals[$name] = [
                    'debit' => 0,
                    'credit' => 0
                ];
            }

            // Calculate debit totals from receivesData
            foreach ($receivesData as $receive) {
                $methodName = $transactionMethods[$receive->method];
                $totals[$methodName]['debit'] += $receive->amount;
                $totalDebit += $receive->amount;
            }

            // Calculate credit totals from paymentsData
            foreach ($paymentsData as $payment) {
                $methodName = $transactionMethods[$payment->method];
                $totals[$methodName]['credit'] += $payment->amount;
                $totalCredit += $payment->amount;
            }
            // dd($totals);
            $agents = [];
            $suppliers = [];
            // $allagents = Agent::where('user', Auth::id())->get();
            // $allsuppliers = Supplier::where('user', Auth::id())->get();

            
            $allagents = Agent::where([['user', $user],['is_delete',0],['is_active',1]])->get();
            $allsuppliers = Supplier::where([['user', $user],['is_delete',0],['is_active',1]])->get();

            foreach ($allagents as $agent) {
                // $agents[$agent->name] = $agent->amount;
                $agents[] = ['name' => $agent->name, 'amount' => $agent->amount];
                $totalDebit += $agent->amount;
            }

            foreach ($allsuppliers as $supplier) {
                // $suppliers[$supplier->name] = $supplier->amount;
                $suppliers[] = ['name' => $supplier->name, 'amount' => $supplier->amount];
                $totalCredit += $supplier->amount;
            }

            $html = ViewFacade::make('report.trailbalance.TrialBalance', [
              
                'start_date' => $start_date,
                'end_date' => $end_date,
                // 'totalincash_debit' => $totalincash_debit,
                // 'totalinbank_debit' => $totalinbank_debit,
                // 'totalincash_credit' => $totalincash_credit,
                // 'totalinbank_credit' => $totalinbank_credit,
                'totals' => $totals,
                'totalOrderAgentPrice' => $totalOrderAgentPrice,
                'totalOrderSupplierPrice' => $totalOrderSupplierPrice,
                'totalTicketAgentPrice' => $totalTicketAgentPrice,
                'totalTicketSupplierPrice' => $totalTicketSupplierPrice,
                'agents' => $agents,
                'suppliers' => $suppliers,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
            ])->render();
            
            return response()->json(['html' => $html]);
            // dd($totalinbank, $totalincash, $totalOrderAgentPrice, $totalOrderSupplierPrice, $totalTicketAgentPrice, $totalTicketSupplierPrice);
            
        }
        else{
            return view('welcome');
        }
    }

    public function income_statement_view(){
        if(Auth::user()){
            return view('report.income_statement.index');
        }
        else{
            return view('welcome');
        }
    }

    public function income_statement_report(Request $request){
        if(Auth::user()){
            $user = Auth::id();

            $start_date = $request->input('start_date') ?? null;
            $end_date = $request->input('end_date') ?? null;

            // Fetch orders with optional date range filtering
            $ticketsQuery = Ticket::where('is_delete', 0)
            ->where('is_active', 1)
            ->where('user', $user); // Add user check here

            if ($start_date && $end_date) {
                $ticketsQuery->whereBetween('created_at', [$start_date, $end_date]);
            } elseif ($start_date) {
                $ticketsQuery->where('created_at', '>=', $start_date);
            } elseif ($end_date) {
                $ticketsQuery->where('created_at', '<=', $end_date);
            }

            $tickets = $ticketsQuery->get();

            // Fetch orders with optional date range filtering
            $ordersQuery = Order::where('is_active', 1)
            ->where('is_delete', 0)
            ->where('user', $user); // Add user check here

            if ($start_date && $end_date) {
                $ordersQuery->whereBetween('created_at', [$start_date, $end_date]);
            } elseif ($start_date) {
                $ordersQuery->where('created_at', '>=', $start_date);
            } elseif ($end_date) {
                $ordersQuery->where('created_at', '<=', $end_date);
            }

            $orders = $ordersQuery->get();

            $ticket_total_purchase = $tickets->sum('supplier_price');
            $ticket_total_sell = $tickets->sum('agent_price');

            $order_total_purchase = $orders->sum('payable_amount');
            $order_total_sell = $orders->sum('contact_amount');
            
            $total_purchase = $ticket_total_purchase + $order_total_purchase;
            $total_sell = $ticket_total_sell + $order_total_sell;

            $profit = $total_sell - $total_purchase;

            $html = ViewFacade::make('report.income_statement.IncomeStatement', [
              
                'start_date' => $start_date,
                'end_date' => $end_date,
               
                'total_purchase' => $total_purchase,
                'total_sell' => $total_sell,
                'profit' => $profit,
                'ticket_total_purchase' => $ticket_total_purchase,
                'ticket_total_sell' => $ticket_total_sell,
                'order_total_purchase' => $order_total_purchase,
                'order_total_sell' => $order_total_sell,
              
            ])->render();
            
            return response()->json(['html' => $html]);
            // return view('report.income_statement.thumbnails.1', compact('total_purchase', 'total_sell', 'profit', 'ticket_total_purchase', 'ticket_total_sell', 'order_total_purchase', 'order_total_sell'));
            
        }
        else{
            return view('welcome');
        }
    }
}
