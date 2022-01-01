<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Master</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="invoice w-full lg:w-4/6 my-5 mx-auto shadow-lg rounded-xl">
        <div class="invoice-header flex justify-between p-5">
            <div class="invoice-details self-center">
                <div class="mb-2">
                    <h2 class="text-3xl uppercase">Invoice</h2>
                </div>
                <div>
                    <ul>
                        <li class="pb-2 text-gray-600">Invoice Number: {{ $data->internalID }}</li>
                        <li class="text-gray-600 pb-2">Date Of Issue: {{
                            \Carbon\Carbon::parse($data->dateTimeIssued)->toDateString() }}</li>
                        <li class="text-gray-600 pb-2">Time Of Issue: {{
                            \Carbon\Carbon::parse($data->dateTimeIssued)->toTimeString() }}</li>
                        <li class="text-gray-600">Invoice ETA: {{ $data->uuid ?? '-' }}</li>
                    </ul>
                </div>
            </div>
            <div class="logo self-center">
                <img src="{{ asset('images/invoice_logo.jpg') }}" alt="logo" class="w-32 h-32">
            </div>
        </div>
        <hr>
        <div class="invoice-company-address flex justify-between p-5">
            <div class="billed-to">
                <h4 class="mb-2 text-2xl">Billed To:</h4>
                <ul>
                    <li class="text-gray-600">
                        {{ $data->receiver->name }}
                    </li>
                    <li class="text-gray-600">
                        {{ $data->receiver->address->street }}
                    </li>
                    <li class="text-gray-600 pb-2">
                        {{ $data->receiver->address->regionCity }} , {{ $data->receiver->address->country }}
                    </li>
                    <li class="text-gray-600 pb-2">
                        Buyer Type: {{ $data->receiver->type }}
                    </li>
                    <li class="text-gray-600">
                        Buyer ID : {{ $data->receiver->receiver_id }}
                    </li>
                </ul>
            </div>
            <div class="company-details">
                <h4 class="mb-2 text-2xl">Issuer Details:</h4>
                <ul>
                    <li class="text-gray-600">
                        Issuer: {{ $data->issuer->name }}
                    </li>
                    <li class="text-gray-600">
                        {{ $data->issuer->address->street }}
                    </li>
                    <li class="text-gray-600 pb-2">
                        {{ $data->issuer->address->regionCity }} , {{ $data->issuer->address->country }}
                    </li>
                    <li class="text-gray-600 pb-2">
                        Issuer Type: {{ $data->issuer->type }}
                    </li>
                    <li class="text-gray-600">
                        Tax Registration: {{ $data->issuer->issuer_id }}
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="items p-5">
            <table class="w-full">
                <thead class="text-center bg-gray-300">
                    <tr>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Item</th>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Quantity</th>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Unit Price</th>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Sales</th>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Tax</th>
                        <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">Total</th>
                    </tr>
                </thead>
                <tbody class="text-center border border-[#eceeef]">
                    @foreach($data->invoiceLines ?? '' as $line)
                    @php
                    $total = 0;
                    if (!$line->taxableItems)
                    $line->taxableItems = [];
                    @endphp

                    @foreach($line->taxableItems as $tax)
                    @php
                    $total = $total + $tax->amount;
                    @endphp
                    @endforeach
                    <tr>
                        <td class="p-2 border border-[#eceeef]">{{ $line->description }}</td>
                        <td class="p-2 border border-[#eceeef]">{{ $line->quantity }}</td>
                        <td class="p-2 border border-[#eceeef]">{{ $line->unitValue->amountEGP }}</td>
                        <td class="p-2 border border-[#eceeef]">{{ $line->salesTotal }}</td>
                        <td class="p-2 border border-[#eceeef]">{{ $total }}</td>
                        <td class="p-2 border border-[#eceeef]">{{ $line->total }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="invoice-total py-5 text-right">
                <h4 class="capitalize text-gray-600 text-xl font-bold">invoice total: EGP {{ $data->totalAmount }}</h4>
            </div>
        </div>
    </div>
</body>

</html>