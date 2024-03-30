<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Agent;
use Illuminate\Support\Facades\Auth; // Add this line


class AgentController extends Controller
{
    public function index()
    {
        $user = Auth::id();
        $agents = Agent::where([['is_delete',0],['is_active',1],['user',$user]])->get();
        return view('agent/index', compact('agents'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'string|max:255',
            'address' => 'string|max:255',
            'district' => 'string|max:255',
            'email' => 'string|max:255',
            'description' => 'string',
        ]);
        $validatedData['user'] = Auth::id();
        Agent::create($validatedData);
        return redirect()->route('agent.view')->with('success', 'Agent added successfully');
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $agent = Agent::findOrFail($id);
        return view('agent.edit', compact('agent'));
    }
        public function update(Request $request, $id)
        {
            // dd($request->all(), $id);
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'description' => '|string',
            ]);

            if($validatedData){
                $agent = Agent::find($id);
                $agent->name = $request->name;
                $agent->phone = $request->phone;
                $agent->email = $request->email;
                $agent->address = $request->address;
                $agent->district = $request->district;
                $agent->country = $request->country;
                $agent->description = $request->description;
                

                if($agent->save()){
                    return redirect()->route('agent.view')->with('success', 'Agent updated successfully');
                }
                else{
                    return redirect()->route('agent.view')->with('error', 'Agent updated failed');
                }
            }         

            return redirect()->route('agent.view')->with('error', 'Agent updated failed');
        }

    public function delete($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->is_delete = 1;
        if($agent->save()){
            return redirect()->route('agent.view')->with('success', 'Agent deleted successfully');
        }
        else{
            return redirect()->route('agent.view')->with('error', 'Agent deleted failed');
        }
        return redirect()->route('agent.view')->with('error', 'Agent deleted failed');
        
    }
}

?>