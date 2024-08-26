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


class GeneralLedgerController extends Controller
{
    //
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
                    ['user', Auth::id()]
                ]);
    
                $paymenter = Payment::where([
                    ['receive_from', '=', 'agent'],
                    ['agent_supplier_id', '=', $id],
                    ['user', Auth::id()]
                ]);

                
    
                // $order = $order->where(['agent', $id]);
    
                $void_ticket = VoidTicket::where([['user', Auth::id()], ['agent', $id]]);
                $reissue = ReissueTicket::where([['agent', $id], ['user', Auth::id()]]);

                $opening_balance_debit = $opening_balance_credit = 0;

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

                    
                        // foreach ($until_start_date_collections as $collection) {
                        //     if ($collection->getTable() == 'order') {
                        //         $opening_balance_debit += $collection->payable_amount;
                        //     }
                        //     if ($collection->getTable() == 'tickets') {
                        //         $opening_balance_debit += $collection->supplier_price;
                        //     }
                        //     if ($collection->getTable() == 'payment') {
                        //         $opening_balance_debit -= $collection->amount;
                        //         //  dd($opening_balance_debit);
                        //     }
                        //     if ($collection->getTable() == 'receive') {
                        //         $opening_balance_credit += $collection->amount;
                        //     }
                        //     if ($collection->getTable() == 'reissue') {
                        //         $opening_balance_debit += $collection->now_supplier_fare;
                        //     }
                        //     if ($collection->getTable() == 'refund') {
                        //         $opening_balance_credit += $collection->now_supplier_fare;
                        //     }
                        //     if ($collection->getTable() == 'voidTicket') {
                        //         $opening_balance_debit += $collection->now_supplier_fare;
                        //     }
                        // }

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
                                $opening_balance_debit -= $collection->amount;
                            }
                            if ($collection->getTable() == 'reissue'){
                                $opening_balance_debit += $collection->now_agent_debit;
                            }
                            if ($collection->getTable() == 'refund'){
                                $opening_balance_debit -= $collection->now_agent_fere;
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
    
                $opening_balance = Agent::where('id', $id)->value('opening_balance');
    
                $balance =  $opening_balance_debit + $opening_balance;
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
    
                $receive = Ticket::where([['supplier', $id], ['is_active', 1], ['is_delete', 0]]);
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

                $opening_balance_debit = $opening_balance_credit  = 0;

                if (!is_null($start_date)) {
                   
                    $until_start_date_receive = Ticket::where([['supplier', $id], ['is_active', 1],['is_delete', 0]])
                        ->where('user', Auth::id())
                        ->where('invoice_date', '<', $start_date)
                        ->get();
                
                    $until_start_date_receiver = Receiver::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_refund = Refund::where([
                        ['supplier', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_paymenter = Payment::where([
                        ['receive_from', '=', 'supplier'],
                        ['agent_supplier_id', '=', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_void_ticket = VoidTicket::where([
                        ['user', Auth::id()],
                        ['supplier', $id],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_reissue = ReissueTicket::where([
                        ['supplier', $id],
                        ['user', Auth::id()],
                        ['date', '<', $start_date]
                    ])->get();
                
                    $until_start_date_order = Order::where('user', Auth::id())
                        ->where('supplier', $id)
                        ->where('date', '<', $start_date)
                        ->get();
                
                    $until_start_date_collections = $until_start_date_receive
                        ->merge($until_start_date_receiver)
                        ->merge($until_start_date_refund)
                        ->merge($until_start_date_paymenter)
                        ->merge($until_start_date_void_ticket)
                        ->merge($until_start_date_reissue)
                        ->merge($until_start_date_order);

                    
                    // dd($until_start_date_receive->sum('supplier_price')
                    // , $opening_balance_debit);
                
                    foreach ($until_start_date_collections as $collection) {
                        if ($collection->getTable() == 'order') {
                            $opening_balance_debit += $collection->payable_amount;
                        }
                        if ($collection->getTable() == 'tickets') {
                            $opening_balance_debit += $collection->supplier_price;
                        }
                        if ($collection->getTable() == 'payment') {
                            $opening_balance_debit -= $collection->amount;
                            //  dd($opening_balance_debit);
                        }
                        if ($collection->getTable() == 'receive') {
                            $opening_balance_debit += $collection->amount;
                        }
                        if ($collection->getTable() == 'reissue') {
                            $opening_balance_debit += $collection->now_supplier_fare;
                        }
                        if ($collection->getTable() == 'refund') {
                            $opening_balance_debit += $collection->now_supplier_fare;
                        }
                        if ($collection->getTable() == 'voidTicket') {
                            $opening_balance_debit += $collection->now_supplier_fare;
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

                $opening_balance = Supplier::where('id', $id)->value('opening_balance');
    
                $balance =  $opening_balance_debit + $opening_balance;
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
}
