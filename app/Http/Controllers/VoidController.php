<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this line
use App\Models\Supplier;
use App\Models\Agent;
use App\Models\Type;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\VoidTicket;
use Illuminate\View\View;
use DateTime;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Type\VoidType;

class VoidController extends Controller
{
    public function view(Request $request)
    {
        $user = Auth::id();
        $suppliers = Supplier::where([['is_delete',0],['is_active',1],['user',$user]])->get();
        $agents = Agent::where([['is_delete',0],['is_active',1],['user',$user]])->get();
        
        $query = VoidTicket::where([['user',$user]]);

        // Add search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('ticket_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('date', 'like', '%' . $searchTerm . '%');
            });
        }

        $void_tickets = $query->paginate(10);
        foreach($void_tickets as $order){
           
            $order->agent = Agent::where('id', $order->agent)->value('name');
            $order->supplier = Supplier::where('id', $order->supplier)->value('name');
        }
        return view('ticket.void', compact('void_tickets'));
    }
    

    public function void_entry(Request $request)
    {
        try {
            DB::beginTransaction();

            $flag = $this->voidTicket($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error or handle it as needed
            return redirect()->back()->with('error', 'Error voiding ticket: ' . $e->getMessage());
        }

        $message = $flag ? 'Void Ticket added successfully' : 'Adding Void Ticket failed';
        $type = $flag ? 'success' : 'error';

        return redirect()->route('void.view')->with($type, $message);
    }

    private function voidTicket(Request $request)
    {
        $ticket = Ticket::where('ticket_no', $request->ticket)->first();

        if (!$ticket) {
            return false; // Ticket not found
        }
        // dd($request->all());
        $voidticket = new VoidTicket();
        $voidticket->ticket_no = $request->ticket;
        $voidticket->ticket_code = $request->ticket_code;
        $voidticket->date = now();
        $voidticket->agent = $request->agent;
        $voidticket->passenger_name = $request->name;
        $voidticket->supplier = $request->supplier;
        $voidticket->prev_agent_amount = $request->agent_fare;
        $voidticket->prev_supply_amount = $request->supplier_fare;
        $voidticket->now_agent_fere = $request->agent_refundfare;
        $voidticket->now_supplier_fare = $request->supplier_refundfare;
        $voidticket->user = Auth::id();
        $agentRefundFare = $request->input('agent_refundfare');
        $supplierRefundFare = $request->input('supplier_refundfare');
        $profit = $agentRefundFare - $supplierRefundFare;

        $voidticket->void_profit = $profit;

     
        $ticketParams = [
            'voidTicket' => $voidticket,
            'ticket' => $ticket,
            'agentFare' => $request->agent_fare,
            'supplierFare' => $request->supplier_fare,
            'agentId' => $request->agent,
            'supplierId' => $request->supplier,
            'profit' => $profit,
            'agentRefundFare' => $request->agent_refundfare,
            'supplierRefundFare' => $request->supplier_refundfare,
        ];
        
        $flag = $this->updateTicket($ticketParams);
        
        return $flag;
    }

    private function updateTicket(array $params)
    {
        $voidticket = $params['voidTicket'];
        $ticket = $params['ticket'];
        $agentFare = $params['agentFare'];
        $supplierFare = $params['supplierFare'];
        $agent = $params['agentId'];
        $supplier = $params['supplierId'];
        $profit = $params['profit'];
        $agentRefundFare = $params['agentRefundFare'];
        $supplierRefundFare = $params['supplierRefundFare'];

       
        // Your existing logic for updating ticket, agent, and supplier
        $ticket->is_void = 1;
        $ticket->void_profit = $profit;

        $agent = Agent::where('id', $agent)->first();
        $agent->amount -= $agentFare;
        $agent->amount += $agentRefundFare;
        
        $supplier = Supplier::where('id', $supplier)->first();
        $supplier->amount -= $supplierFare;
        $supplier->amount += $supplierRefundFare;

        return $ticket->save() && $agent->save() && $supplier->save() &&  $voidticket->save();
    }

}