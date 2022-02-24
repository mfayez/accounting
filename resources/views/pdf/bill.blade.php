<!doctype html>
<html dir="{{ App::getLocale() == 'en' ? 'ltr' : 'rtl'}}">

<head>
    <meta charset="utf-8">
    <title>Invoice Master</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
        crossorigin="anonymous">
    <style>
        @media print {
            .btns {
                display: none;
            }

            .invoice {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper flex justify-between">
        <div class="invoice w-full lg:w-5/6 my-5 shadow-lg rounded-xl rtl:mr-2 ltr:ml-2">
            <div class="invoice-header flex justify-between p-5">
                <div class="invoice-details self-center">
                    <div class="mb-2">
                        <h2 class="text-3xl uppercase">@lang('Invoice')</h2>
                    </div>
                    <div>
                        <ul>
                            <li class="pb-2 text-gray-600">{{ __('Invoice Number') }}: {{ $data->internalID }}</li>
                            <li class="text-gray-600 pb-2">{{ __('Date Of Issue') }}: {{
                                \Carbon\Carbon::parse($data->dateTimeIssued)->toDateString() }}</li>
                            <li class="text-gray-600 pb-2">{{ __('Time Of Issue') }}: {{
                                \Carbon\Carbon::parse($data->dateTimeIssued)->toTimeString() }}</li>
                            <li class="text-gray-600">{{ __('Invoice ETA') }}: {{ $data->uuid ?? '-' }}</li>
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
                    <h4 class="mb-2 text-2xl">{{ __('Billed To') }}:</h4>
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
                            {{ __('Buyer Type') }}: {{ $data->receiver->type }}
                        </li>
                        <li class="text-gray-600">
                            {{ __('Buyer ID') }} : {{ $data->receiver->receiver_id }}
                        </li>
                    </ul>
                </div>
                <div class="company-details">
                    <h4 class="mb-2 text-2xl">{{ __('Issuer Details') }}:</h4>
                    <ul>
                        <li class="text-gray-600">
                            {{ $data->issuer->name }}
                        </li>
                        <li class="text-gray-600">
                            {{ $data->issuer->address->street }}
                        </li>
                        <li class="text-gray-600 pb-2">
                            {{ $data->issuer->address->regionCity }} , {{ $data->issuer->address->country }}
                        </li>
                        <li class="text-gray-600 pb-2">
                            {{ __('Issuer Type') }}: {{ $data->issuer->type }}
                        </li>
                        <li class="text-gray-600">
                            {{ __('Tax Registration Number') }}: {{ $data->issuer->issuer_id }}
                        </li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="items p-5">
                <table class="w-full">
                    <thead class="text-center bg-gray-300">
                        <tr>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Item') }}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Quantity') }}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Unit Price') }}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Total Sales Amount') }}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Tax Amount') }}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef]">{{ __('Total') }}</th>
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
                            <td class="p-2 border border-[#eceeef]">{{ sprintf("%0.2f", $total) }}</td>
                            <td class="p-2 border border-[#eceeef]">{{ sprintf("%0.2f", $line->total) }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="invoice-total py-5 text-right">
                    <h4 class="capitalize text-gray-600 text-xl font-bold">{{ __('Invoice Total') }}: {{ __('EGP')
                        }} {{
                        sprintf("%0.2f", $data->totalAmount)}}
                    </h4>
                </div>
            </div>
        </div>
        <div class="btns my-5 w-2/12 text-center">
            <a href="{{ route('pdf.invoice.download' , ['id' => $data->Id]) }}">
                <button class="bg-black text-white py-2 px-5 rounded ml-2">
                    <i class="fa fa-download"></i> {{ __('Save') }}
                </button>
            </a>
            <button class="bg-black text-white py-2 px-5 rounded ml-2" id="print">
                <i class="fa fa-print pointer-events-none"></i> {{ __('Print') }}</button>

            @if(App::getLocale() == 'en')
            <a href="{{ route('language' , 'ar') }}">
                <button class="text-white bg-[#4099de] px-5 py-2 rounded mt-2">
                    <i class="fa fa-flag"></i> AR
                </button>
            </a>
            @else
            <a href="{{ route('language' , 'en') }}">
                <button class="text-white bg-[#4099de] px-5 py-2 rounded mt-2">
                    <i class="fa fa-flag"></i> EN
                </button>
            </a>
            @endif

        </div>
    </div>
</body>
<script>
    document.querySelector('#print').addEventListener('click' , (e) => print());
</script>

</html>
