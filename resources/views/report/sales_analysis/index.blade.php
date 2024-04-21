<x-app-layout>
    <div class="container-fluid">
        <form id="reportForm" action="{{ route('sales_analysis_report') }}" method="POST">
            @csrf
            <div class="row">
               
                <div class="form-group col-md-3">
                    <label for="start_date">Start Date</label>
                    <div class="input-group date" style="width: 100%">
                        <input type="text" class="form-control datepicker" name="start_date" id="start_date" placeholder="Start Date" />
                    </div>      
                </div>
                <div class="form-group col-md-3">
                    <label for="end_date">End Date</label>
                    <div class="input-group date" style="width: 100%">
                        <input type="text" class="form-control datepicker" name="end_date" id="end_date" placeholder="End Date" />
                    </div>      
                </div>
               
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
  </div>

    
  <main class="flex-1 mx-auto max-w-7xl px-10">
  
   <div class="">
        <h2 class="text-center  font-light text-3xl my-2">SALLU AIR SERVICE</h2>
        <h2 class="text-center  font-bold text-xl my-2 underline">Sales Analysis Report</h2>
        <div class="flex items-center w-[35%] mx-auto justify-between mb-2">
            <div class="text-md">
                
                <p>From Date : <span class="font-semibold">{{ isset($start_date) ? $start_date : '' }}</span></p>
              </div>
            <div class="text-md">
                <p>To Date : <span class="font-semibold">{{ isset($end_date) ? $end_date : '' }}</span></p>
                
            </div>
           
        </div>
        <!-- <p class="">From Date : 14-09-2024 </p> -->
        
        @if(isset($tableData))
            <div>
                <table class="table-auto w-[100%] mx-auto border-2 border-gray-400 devide-2 text-sm my-1">
                    <thead>
                        <tr class="border-y-2 border-black bg-cyan-700">
                            <th class="text-start">SL</th>
                            <th class="text-start">Trans. Date</th>
                            <th class="text-start">Sale Amount</th>
                            <th class="text-start">Purchase Amount</th>
                            <th class="text-start">Profit Amount</th>
                            <th class="text-start">Receive Amount</th>
                            <th class="text-start">Payment Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2">
                        @foreach($tableData as $index => $row)
                        <tr class="bg-neutral-400 text-black font-bold">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['salestotalAmount'] }}</td>
                            <td>{{ $row['purchasetotalAmount'] }}</td>
                            <td>{{ $row['profittotalAmount'] }}</td>
                            <td>{{ $row['receivetotalAmount'] }}</td>
                            <td>{{ $row['paymenttotalAmount'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            
        @endif
        

  </main>
 

  <script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            autoclose: true
        });

        $('.select2').select2();

        // $('#ordertable').DataTable();

        $('#reportForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (response) {
                    // Update the reportdiv with the response
                    $('#reportdiv').html(response);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
    });

    
</script>
</x-app-layout>