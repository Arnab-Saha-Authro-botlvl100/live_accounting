<x-app-layout>
    @if(session('employee'))
    @php
        $employee = session('employee');
        // dd($employee['permission']);
        $permissionString = $employee['permission'];
        $permissionsArray = explode(',', $permissionString);
        $role = $employee['role'];
        // dd($role, $employee);
    @endphp
    @else
        @php
            $permissionsArray = ['entry', 'edit', 'delete', 'print', 'view'];
            $role = 'admin';
        @endphp
    @endif
    <div class="container mx-auto mt-5">
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="mb-2 flex items-center gap-6">
            <p class="font-bold text-2xl">List of Suppliers</p>
            <button class="py-2 px-4 border-green-700 hover:bg-green-700 hover:text-white duration-300 border-2 text-green-700  rounded-2xl font-bold " onchange="toggleVisibility()" id="addnewbtn">Add
                New Supplier</button>
        </div>
        <div id="addSupplier" class="py-2">
        
            <div class="bg-white shadow-md rounded-lg w-[100%] lg:w-[60%] p-6 mb-8">
                @if(in_array('entry', $permissionsArray))
                <form action="/addsupplier" method="post">
                    @csrf <!-- Add this line to include CSRF protection in Laravel -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold text-gray-600">Name:</label>
                            <input type="text" class="form-input mt-1 block w-full border p-2" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-semibold text-gray-600">Phone:</label>
                            <input type="tel" class="form-input mt-1 block w-full border p-2" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-semibold text-gray-600">Email:</label>
                            <input type="text" class="form-input mt-1 block w-full border p-2" id="email" name="email" placeholder="Enter an Email">
                        </div>
                        <div class="mb-4">
                            <label for="company" class="block text-sm font-semibold text-gray-600">Company:</label>
                            <input type="text" class="form-input mt-1 block w-full border p-2" id="company" name="company" placeholder="Enter a company">
                        </div>
                    </div>
                   
                    <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                            <textarea id="description" name="description" class="mt-1 p-2 w-full border " placeholder="Enter a description" ></textarea>
                        </div>
            
                        <div class="mb-4">
                            <label for="opening_balance" class="block text-sm font-medium text-gray-700">Opening Balance:</label>
                            <input type="number" id="opening_balance" name="opening_balance" class="mt-1 p-2 w-full border " placeholder="Enter Opening Balance">
                        </div>
                    </div>
        
                    <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
                </form>
                @else
                    <div class="alert alert-warning">
                        Don't have permission to entry
                    </div>
                @endif

            </div>
        </div>
        <div class="bg-white shadow-md p-6 w-[100%] mx-auto">
            <table class="table divide-y divide-gray-200 table-hover" id="suppliertable">
                <thead class="bg-[#7CB0B2]">
                    <tr>
                        <th class="px-4 py-2 ">SL</th>
                        <th class="px-4 py-2 ">Name</th>
                        <th class="px-4 py-2 ">Phone</th>
                        <th class="px-4 py-2 ">Email</th>
                        <th class="px-4 py-2 ">Company</th>
                        <th class="px-4 py-2 ">Description</th>
                        <th class="px-4 py-2 ">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $index => $supplier)
                        <tr>
                            <td class="px-4 py-2  w-[30px]">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 ">{{ $supplier->name }}</td>
                            <td class="px-4 py-2 ">{{ $supplier->phone }}</td>
                            <td class="px-4 py-2 ">{{ $supplier->email }}</td>
                            <td class="px-4 py-2 ">{{ $supplier->company }}</td>
                            <td class="px-4 py-2 ">{{ $supplier->description }}</td>
                            <td class="px-4 py-2  w-[75px]">
                                @if(in_array('edit', $permissionsArray))
                                <a href="{{ route('supplier.edit', ['id' => encrypt($supplier->id)]) }}" class=" hover:underline"><i class="text-xl fa fa-pencil fa-fw"></i></a>
                                @endif
                                @if(in_array('delete', $permissionsArray))
                                <a href="#" onclick="confirmDelete('{{ route('supplier.delete', ['id' => $supplier->id]) }}')" class=" hover:underline ml-2"><i class="text-xl fa fa-trash-o fa-fw"></i></a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
         function confirmDelete(deleteUrl) {
            // Display a confirmation dialog
            const isConfirmed = window.confirm("Are you sure you want to delete?");

            // If the user confirms, proceed with the delete action
            if (isConfirmed) {
                window.location.href = deleteUrl;
            }
        }
        $(document).ready(function() {
            $('.datepicker').datepicker({
                autoclose: true
            });
    
            $('.select2').select2({
                theme:'classic',
            });

            // $('#suppliertable').DataTable();
            new DataTable('#suppliertable', {
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            }
        });
        });
        var addnewBtn = document.getElementById('addnewbtn');
        var addSupplier = document.getElementById('addSupplier');
        addSupplier.style.display = 'none';

        addnewBtn.addEventListener('click', function() {
            toggleVisibility();
        });
        function toggleVisibility() {
            if (addSupplier.style.display === 'none') {
                addSupplier.style.display = 'block';
            } else {
                addSupplier.style.display = 'none';
            }
        }
        
    </script>
    
</x-app-layout>