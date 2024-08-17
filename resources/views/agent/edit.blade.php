<x-app-layout>
   
    

    <div class="container">
        {{-- @php
            dd($agent->description);
        @endphp --}}
        <form action="{{ route('agent.update', ['id' => $agent->id]) }}" method="post">
            @csrf <!-- Add this line to include CSRF protection in Laravel -->
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" id="name" name="name" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter your name" value="{{$agent->name}}">
                </div>
    
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                    <input type="tel" id="phone" name="phone" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter your phone number" value="{{$agent->phone}}">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="text" id="email" name="email" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter an Email" value="{{$agent->email}}">
                </div>
        
                <div class="mb-4">
                    <label for="district" class="block text-sm font-medium text-gray-700">District:</label>
                    <input type="text" id="district" name="district" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter a district" value="{{$agent->district}}">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address:</label>
                    <textarea id="address" name="address" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter an address" >{!! $agent->address !!}</textarea>
                </div>
        
                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700">Country:</label>
                    <input type="text" id="country" name="country" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter a Country" value="{{$agent->country}}">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea id="description" name="description" class="mt-1 p-2 w-full border rounded-md" placeholder="Enter a description" >{!! $agent->description !!}</textarea>
                </div>
    
                <div class="mb-4">
                    <label for="opening_balance" class="block text-sm font-medium text-gray-700">Opening Balance:</label>
                    <input type="number" id="opening_balance" name="opening_balance" class="mt-1 p-2 w-full border " placeholder="Enter Opening Balance" value="{{$agent->opening_balance}}">
                </div>
            </div>
    
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Submit</button>
        </form>
    </div>
    
</x-app-layout>