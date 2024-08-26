<h2 class="text-center font-bold text-3xl my-2">Reissue Report</h2>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-lg">
                        <h2 class="font-semibold">Company Name : {{Auth::user()->name }}'</h2>
                        <p><span class="font-semibold">Period Date :</span>{{ $start_date }}  to {{$end_date }} </p> 
                    </div>
                    <div class="flex items-center">
                       
                        
                    </div>
                </div>
                <table class="table-auto w-full bordered shadow-xl bg-white border-black text-sm my-1">
                <thead>
                <tr class="border-y-2 border-black bg-cyan-700 text-white">
                    <th class="text-start">Date</th>
                    <th class="text-start">Ticket No</th>
                    <th class="text-start">Passenger Name</th>
                    
                 
                    <th class="text-start">Flight Date</th>
                    <th class="text-start">Sector</th>
                    <th class="text-start">Airlines</th>
                   
                    @if($show_agent != null)
                        <th class="text-start">Agent</th>
                    @endif

                    @if($show_supplier != null)
                        <th class="text-start">Supplier</th>
                    @endif

                    @if($show_profit != null)
                        <th class="text-start">Net Markup (Void)</th>
                    @endif

                 
                </tr>
                </thead>
                <tbody class="divide-y-2">

                @foreach ($alldata as $data)
                    <tr>
                        <td class="px-2 pl-2">{{ (new DateTime($data->date))->format('d-m-Y') }}</td>
                        <td class="py-2">{{ $data->ticket_code }} - {{ $data->ticket_no }}</td>
                        <td class="py-2">{{ $data->passenger }}</td>
                        <td class="py-2">{{ (new DateTime($data->flight_date))->format('d-m-Y') }}</td>
                        <td class="py-2">{{ $data->sector }}</td>
                        <td class="py-2">{{ $data->airline_name }}</td>
                        @if($show_agent != null)
                            <td class="py-2">{{ $data->agent_name }}</td>
                        @endif
                        @if($show_supplier != null)
                            <td class="py-2">{{ $data->supplier_name }}</td>
                        @endif
                        @if($show_profit != null)
                            <td class="py-2">{{ $data->reissue_profit }}</td>
                        @endif
                    </tr>
                @endforeach


                </tbody>
            </table>