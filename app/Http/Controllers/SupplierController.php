<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add this line
use App\Models\Supplier;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index()
    {
        if(Auth::user()){
            $user = Auth::id();
            $suppliers = Supplier::where([['is_delete',0],['is_active',1],['user',$user]])->get();
            // dd($suppliers);
            return view('supplier/index', compact('suppliers'));
        }
        else{
            return view('welcome');
        }
      
    }

    public function store(Request $request)
    {
        if(Auth::user()){
            
            $supplier = new Supplier();
            $supplier->name = $request['name'];
            $supplier->phone = $request['phone'];
            $supplier->company = $request['company'];
            $supplier->email = $request['email'];
            $supplier->description = $request['description'];
            $supplier->opening_balance = $request['opening_balance'];
           
            $supplier->user = Auth::id();
            $isdone = $supplier->save();

            if($isdone){
                return redirect()->route('supplier.view')->with('success', 'Supplier added successfully');
            }
            // $validatedData['user'] = Auth::id();
            // Supplier::create($validatedData);
            return redirect()->route('supplier.view')->with('error', 'Supplier is not added');
        }
        else{
            return view('welcome');
        }

    }

    public function edit($id)
    {
        if(Auth::user()){
            $id = decrypt($id);
            $supplier = Supplier::findOrFail($id);
            return view('supplier.edit', compact('supplier'));
        }
        else{
            return view('welcome');
        }
        
    }
        public function update(Request $request, $id)
        {
            if(Auth::user()){
                  // dd($request->all(), $id);
            // $validatedData = $request->validate([
            //     'name' => 'string|max:255',
            //     'phone' => 'string|max:20',
            //     'description' => 'string',
            //     'company' => 'string|max:255',
            //     'email' => 'string|max:255',
            // ]);

            // if($validatedData){
                $supplier = Supplier::find($id);
                $supplier->name = $request->name;
                $supplier->phone = $request->phone;
                $supplier->company = $request->company;
                $supplier->email = $request->email;
                $supplier->description = $request->description;
                $supplier->opening_balance = $request->opening_balance;
                

                if($supplier->save()){
                    return redirect()->route('supplier.view')->with('success', 'Supplier updated successfully');
                }
                else{
                    return redirect()->route('supplier.view')->with('error', 'Supplier updated failed');
                }
                  

            return redirect()->route('supplier.view')->with('error', 'Supplier updated failed');
            }
            else{
                return view('welcome');
            }
          
        }

    public function delete($id)
    {
        if(Auth::user()){
            $supplier = Supplier::findOrFail($id);
            $supplier->is_delete = 1;
            if($supplier->save()){
                return redirect()->route('supplier.view')->with('success', 'Supplier deleted successfully');
            }
            else{
                return redirect()->route('supplier.view')->with('error', 'Supplier deleted failed');
            }
            return redirect()->route('supplier.view')->with('error', 'Supplier deleted failed');
        }
        else{
            return view('welcome');
        }
        
        
    }
}

?>