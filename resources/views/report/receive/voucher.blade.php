<x-app-layout>

    <div
        class="buttons justify-end flex gap-3 shadow-2xl py-2 border-2 border-stale-300 px-4 max-w-[1060px] mt-5 mx-auto">
        
        <button id="printBtn" class="text-white bg-red-600 font-bold text-md py-1 px-4">Print</button>
        <button onclick="goBack()" class="text-white bg-sky-900 font-bold text-md py-1 px-4 ">Go Back</button>
    </div>
    <div id="printSection" class="">
        <div class="flex-1 mt-3 mx-auto max-w-[1100px] bg-white shadow-3xl border-gray-200 px-3 py-2 pb-7">

            <div class="flex justify-between items-center pb-2">
                <div class=""><img src="{{ url(Auth::user()->company_logo) }}" alt="logo" width="200px" height="220px"/></div>
                <div class="w-[350px]">
                    <h3 class="company-name font-bold text-3xl ">{{Auth::user()->name}}</h3>
                    <p class="company-address text-lg font-medium">{{Auth::user()->company_address}}</p>
                    <p class="company-address text-lg font-medium">{{Auth::user()->company_address}}</p>
                    <p class="company-phone text-lg font-medium">Mob : {{Auth::user()->mobile_no}}</p>
                    <p class="company-email text-lg font-medium">Email : {{Auth::user()->email}}</p>
                </div>
            </div>
            <hr class="h-[2px] bg-gray-600" />
            <h1 class="text-2xl font-bold text-center my-3">Money Receipt (Customer Copy)</h1>
            <div class="flex justify-between items-center">
                <div>
                    <div><span class="font-semibold">Date</span> : {{ (new DateTime($receive_voucher->date))->format('d-m-Y') }}</div>
                    <div><span class="font-semibold">Receipt No</span> : {{ $receive_voucher->invoice }}</div>
                </div>
                <div class="flex flex-col gap-y-1 max-w-[360px]">
                    <h3 class="font-bold text-xl">Client Details</h3>
                    <p class="text-lg"><span class="font-semibold">Client Name</span> : {{ $agent->name }}</p>
                    <p class="text-lg"><span class="font-semibold">Client Address :</span> {{ $agent->address }}</p>
                    <p class="text-lg"><span class="font-semibold">Email :</span> {{ $agent->email }}</p>
                    <p class="text-lg"><span class="font-semibold">Mob :</span> {{ $agent->phone }}</p>
                </div>
            </div>
            <table class="w-full my-3 border-y border-black">
                <thead class="border-y border-black bg-gray-50">
                    <tr>

                        <th class="text-lg">Payment Mode</th>
                        <th class="text-lg">Narration</th>
                        <th class="text-lg text-center">Received Amount</th>
                    </tr>
                </thead>
                <tbody class="h-[50px]">
                    <tr class=" py-2">
                        <td class="text-xl">{{ $receive_voucher->method }}</td>
                        <td class="text-xl">{{ $receive_voucher->remark }}</td>
                        <td class="text-xl text-center">{{ number_format($receive_voucher->amount, 0, '.', ',') }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="w-full flex justify-between">
                <div>Amount in Words : <span class="">@php function numberToWords($number)
                    {
                        $words = [
                            0 => 'Zero',
                            1 => 'One',
                            2 => 'Two',
                            3 => 'Three',
                            4 => 'Four',
                            5 => 'Five',
                            6 => 'Six',
                            7 => 'Seven',
                            8 => 'Eight',
                            9 => 'Nine',
                            10 => 'Ten',
                            11 => 'Eleven',
                            12 => 'Twelve',
                            13 => 'Thirteen',
                            14 => 'Fourteen',
                            15 => 'Fifteen',
                            16 => 'Sixteen',
                            17 => 'Seventeen',
                            18 => 'Eighteen',
                            19 => 'Nineteen',
                            20 => 'Twenty',
                            30 => 'Thirty',
                            40 => 'Forty',
                            50 => 'Fifty',
                            60 => 'Sixty',
                            70 => 'Seventy',
                            80 => 'Eighty',
                            90 => 'Ninety',
                            100 => 'Hundred',
                            1000 => 'Thousand',
                            1000000 => 'Million',
                            1000000000 => 'Billion',
                            1000000000000 => 'Trillion',
                            1000000000000000 => 'Quadrillion',
                            1000000000000000000 => 'Quintillion',
                        ];

                        if (!is_numeric($number)) {
                            return false;
                        }

                        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
                            // overflow
                            trigger_error(
                                'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                                E_USER_WARNING,
                            );
                            return false;
                        }

                        if ($number < 0) {
                            return '-' . numberToWords(abs($number));
                        }

                        $string = $fraction = null;

                        if (strpos($number, '.') !== false) {
                            [$number, $fraction] = explode('.', $number);
                        }

                        switch (true) {
                            case $number < 21:
                                $string = $words[$number];
                                break;
                            case $number < 100:
                                $tens = ((int) ($number / 10)) * 10;
                                $units = $number % 10;
                                $string = $words[$tens];
                                if ($units) {
                                    $string .= '-' . $words[$units];
                                }
                                break;
                            case $number < 1000:
                                $hundreds = $number / 100;
                                $remainder = $number % 100;
                                $string = $words[$hundreds] . ' ' . $words[100];
                                if ($remainder) {
                                    $string .= ' ' . numberToWords($remainder);
                                }
                                break;
                            default:
                                $baseUnit = pow(1000, floor(log($number, 1000)));
                                $numBaseUnits = (int) ($number / $baseUnit);
                                $remainder = $number % $baseUnit;
                                $string = numberToWords($numBaseUnits) . ' ' . $words[$baseUnit];
                                if ($remainder) {
                                    $string .= $remainder < 100 ? ' and ' : ', ';
                                    $string .= numberToWords($remainder);
                                }
                                break;
                        }

                        if (null !== $fraction && is_numeric($fraction)) {
                            $string .= ' point';
                            $words = [];
                            foreach (str_split((string) $fraction) as $number) {
                                $words[] = $number;
                            }
                            $string .= ' ' . implode(' ', $words);
                        }

                        return $string;
                    }

                    // Example usage:
                    $amount = $receive_voucher->amount;
                    $amountInWords = numberToWords($amount);
                echo $amountInWords; @endphp Only</span></div>
                <div class="flex  flex-col gap-y-2 w-[35%]">
                    <div class="flex justify-between bg-gray-100 text-md px-3">
                        <p>Current Amount</p>
                        <td>
                            @if($receive_voucher->receive_from === 'agent')
                                {{ number_format($opening_balance + $receive_voucher->amount, 0, '.', ',') }}
                            @elseif($receive_voucher->receive_from === 'supplier')
                                {{ number_format($opening_balance - $receive_voucher->amount, 0, '.', ',') }}
                            @endif
                        </td>
                    </div>
                    <div class="flex justify-between bg-gray-100 text-md px-3 font-bold">
                        <p>Received Amount</p>
                        <p>{{ number_format($receive_voucher->amount, 0, '.', ',') }}</p>
                    </div>
                    <div class="flex justify-between bg-gray-100 text-md px-3">
                        <p>Balance Due</p>
                        <p>{{ $opening_balance}}</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-[80px] mb-2">
                <p class="border-t border-gray-400 border-dashed px-4">Authority Signature</p>
                <p class="border-t border-gray-400 border-dashed px-4">Customer Signature</p>
            </div>
        </div>
        <div
            class="flex-1 mx-auto max-w-[1100px] bg-white shadow-3xl border-t border-dashed border-gray-400 px-3 py-3 pt-5">

            <div class="flex justify-between items-center pb-2">
                <div class=""><img src="{{ url(Auth::user()->company_logo) }}" alt="logo" width="200px" height="220px"/></div>
                <div class="w-[350px]">
                    <h3 class="company-name font-bold text-3xl ">{{Auth::user()->name}}</h3>
                    <p class="company-address text-lg font-medium">{{Auth::user()->company_address}}</p>
                    <p class="company-phone text-lg font-medium">Mob : {{Auth::user()->mobile_no}}</p>
                    <p class="company-email text-lg font-medium">Email : {{Auth::user()->email}}</p>
                </div>
            </div>
            <hr class="h-[2px] bg-gray-600" />
            <h1 class="text-2xl font-bold text-center my-3">Money Receipt (Office Copy)</h1>
            <div class="flex justify-between items-center">
                <div>
                    <div><span class="font-semibold">Date</span> : {{ (new DateTime($receive_voucher->date))->format('d-m-Y') }}</div>
                    <div><span class="font-semibold">Receipt No</span> : {{ $receive_voucher->invoice }}</div>
                </div>
                <div class="flex flex-col gap-y-1 max-w-[360px]">
                    <h3 class="font-bold text-xl">Client Details</h3>
                    <p class="text-lg"><span class="font-semibold">Client Name</span> : {{ $agent->name }}</p>
                    <p class="text-lg"><span class="font-semibold">Client Address :</span> {{ $agent->address }}</p>
                    <p class="text-lg"><span class="font-semibold">Email :</span> {{ $agent->email }}</p>
                    <p class="text-lg"><span class="font-semibold">Mob :</span> {{ $agent->phone }}</p>
                </div>
            </div>
            <table class="w-full my-3 border-y border-black">
                <thead class="border-y border-black bg-gray-50">
                    <tr>

                        <th class="text-lg">Payment Mode</th>
                        <th class="text-lg">Narration</th>
                        <th class="text-lg text-center">Received Amount</th>
                    </tr>
                </thead>
                <tbody class="h-[50px]">
                    <tr class=" py-2">
                        <td class="text-xl">{{ $receive_voucher->method }}</td>
                        <td class="text-xl">{{ $receive_voucher->remark }}</td>
                        <td class="text-xl text-center">{{ number_format($receive_voucher->amount, 0, '.', ',') }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="w-full flex justify-between">
                <div>Amount in Words : <span class="">@php 

                    // Example usage:
                    $amount = $receive_voucher->amount;
                    $amountInWords = numberToWords($amount);
                echo $amountInWords; @endphp Only</span></div>
                <div class="flex  flex-col gap-y-2 w-[35%]">
                    <div class="flex justify-between bg-gray-100 text-md px-3">
                        <p>Current Amount</p>
                        <td>
                            @if($receive_voucher->receive_from === 'agent')
                                {{ number_format($opening_balance + $receive_voucher->amount, 0, '.', ',') }}
                            @elseif($receive_voucher->receive_from === 'supplier')
                                {{ number_format($opening_balance - $receive_voucher->amount, 0, '.', ',') }}
                            @endif
                        </td>
                    </div>
                    <div class="flex justify-between bg-gray-100 text-md px-3 font-bold">
                        <p>Received Amount</p>
                        <p>{{ number_format($receive_voucher->amount, 0, '.', ',') }}</p>
                    </div>
                    <div class="flex justify-between bg-gray-100 text-md px-3">
                        <p>Balance Due</p>
                        <p>{{ $opening_balance}}</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-[80px]">
                <p class="border-t border-gray-400 border-dashed px-4">Authority Signature</p>
                <p class="border-t border-gray-400 border-dashed px-4">Customer Signature</p>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get the "Print" button
                const printButton = document.getElementById('printBtn');
                const printSection = document.getElementById('printSection');

                // Attach a click event listener to the "Print" button
                printButton.addEventListener('click', function() {
                    // Open the print dialog for the printSection
                    window.print();
                });
            });
        </script>

        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #printSection,
                #printSection * {
                    visibility: visible;
                }

                #printSection {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    max-width: 100%;
                    box-sizing: border-box;
                    padding: 10px;
                    /* Adjust padding as needed */
                }
            }
        </style>
</x-app-layout>