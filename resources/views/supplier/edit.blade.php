<x-app-layout>
   
    <div class="container mx-auto">
        {{-- @php
            dd($supplier->description);
        @endphp --}}
        <form action="{{ route('supplier.update', ['id' => $supplier->id]) }}" method="post">
            @csrf <!-- Add this line to include CSRF protection in Laravel -->
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-semibold text-gray-600">Name:</label>
                    <input type="text" class="form-input mt-1 block w-full border p-2" id="name" name="name" placeholder="Enter your name" value="{{$supplier->name}}">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-semibold text-gray-600">Phone:</label>
                    <input type="tel" class="form-input mt-1 block w-full border p-2" id="phone" name="phone" placeholder="Enter your phone number" value="{{$supplier->phone}}">
                </div>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-600">Email:</label>
                <input type="text" class="form-input mt-1 block w-full border p-2" id="email" name="email" placeholder="Enter an Email" value="{{$supplier->email}}">
            </div>
            <div class="mb-4">
                <label for="company" class="block text-sm font-semibold text-gray-600">Company:</label>
                <input type="text" class="form-input mt-1 block w-full border p-2" id="company" name="company" placeholder="Enter a company" value="{{$supplier->company}}">
            </div>
           
            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea class="form-input mt-1 block w-full border p-2" id="description" name="description" placeholder="Enter a description" >{!! $supplier->description !!}</textarea>
                </div>
    
                <div class="mb-4">
                    <label for="opening_balance" class="block text-sm font-medium text-gray-700">Opening Balance:</label>
                    <input type="number" id="opening_balance" name="opening_balance" class="mt-1 p-2 w-full border " placeholder="Enter Opening Balance" value="{{$supplier->opening_balance}}">
                </div>
            </div>
    
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
        </form>
    </div>
</x-app-layout>