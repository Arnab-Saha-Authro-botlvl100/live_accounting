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


    <div class="container-fluid mt-5">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="mb-2 flex items-center gap-6">
            <p class="font-bold text-2xl">List of Agents</p>
            <button class="py-2 px-4 border-green-700 hover:bg-green-700 hover:text-white duration-300 border-2 text-green-700  rounded-2xl font-bold " onchange="toggleVisibility()" id="addnewbtn">Add
                New Agent</button>
        </div>
        <div id="addAgent">
            <h1 class="mb-4 text-3xl w-[60%] mx-auto font-bold">Add Agent</h1>
            <div class="addagent w-[90%] md:w-[60%] p-7 mx-auto bg-white shadow-lg rounded-lg">
                @if(in_array('entry', $permissionsArray))
                    <form action="/addagent" method="POST">
                        @csrf <!-- Add this line to include CSRF protection in Laravel -->
                        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                                <input type="text" id="name" name="name" class="mt-1 p-2 w-full border " placeholder="Enter your name" required>
                            </div>
            
                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone:</label>
                                <input type="tel" id="phone" name="phone" class="mt-1 p-2 w-full border " placeholder="Enter your phone number" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                                <input type="text" id="email" name="email" class="mt-1 p-2 w-full border " placeholder="Enter an Email">
                            </div>
                
                            <div class="mb-4">
                                <label for="district" class="block text-sm font-medium text-gray-700">District:</label>
                                <input type="text" id="district" name="district" class="mt-1 p-2 w-full border " placeholder="Enter a district">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-10">
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address:</label>
                                <textarea id="address" name="address" class="mt-1 p-2 w-full border " placeholder="Enter an address" ></textarea>
                            </div>
                
                            <div class="mb-4">
                                <label for="country" class="block text-sm font-medium text-gray-700">Country:</label>
                                <input type="text" id="country" name="country" class="mt-1 p-2 w-full border " placeholder="Enter a Country">
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
                       
                       
                        <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg">Submit</button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Don't have permission to entry
                    </div>
                @endif
            </div>
        </div>
    
        <div class="allagents mt-8 shadow-lg bg-white p-3 rounded-lg">
            <table class="table table-hover no-wrap" id="agenttable">
                <thead class="bg-[#7CB0B2]">
                    <tr>
                        <th class="px-4 py-2">Serial</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Phone</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Address</th>
                        <th class="px-4 py-2">District</th>
                        <th class="px-4 py-2">Country</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($agents as $index => $agent)
                        <tr>
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ date('d-m-Y', strtotime($agent->created_at)) }}</td>
                            <td class="px-4 py-2">{{ $agent->name }}</td>
                            <td class="px-4 py-2">{{ $agent->phone }}</td>
                            <td class="px-4 py-2">{{ $agent->email }}</td>
                            <td class="px-4 py-2">{{ $agent->address }}</td>
                            <td class="px-4 py-2">{{ $agent->district }}</td>
                            <td class="px-4 py-2">{{ $agent->country }}</td>
                            <td class="px-4 py-2">{{ $agent->description }}</td>
                           <td class="px-4 py-2 flex">
                                @if(in_array('edit', $permissionsArray))
                                    <a href="{{ route('agent.edit', ['id' => encrypt($agent->id)]) }}" class=" text-green-800 px-2 py-1 rounded-md"><i class="text-xl fa fa-pencil fa-fw"></i></a>
                                @endif
                                @if(in_array('delete', $permissionsArray))
                                    <a href="#" onclick="confirmDelete('{{ route('agent.delete', ['id' => $agent->id]) }}')" class=" text-red-900 px-2 py-1 rounded-md"><i class="text-xl fa fa-trash-o fa-fw"></i></a>
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

            // $('#agenttable').DataTable(
            //     {
            //         responsive: true
            //     }
            // );
            new DataTable('#agenttable', {
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            }
})
        
        });
        var addnewBtn = document.getElementById('addnewbtn');
        var addAgent = document.getElementById('addAgent');
        addAgent.style.display = 'none';

        addnewBtn.addEventListener('click', function() {
            toggleVisibility();
        });
        function toggleVisibility() {
            if (addAgent.style.display === 'none') {
                addAgent.style.display = 'block';
            } else {
                addAgent.style.display = 'none';
            }
        }
        
    </script>
</x-app-layout>