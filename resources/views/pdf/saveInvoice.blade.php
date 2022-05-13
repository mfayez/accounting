<!doctype html>

<html dir="{{ App::getLocale() == 'en' ? 'ltr' : 'rtl' }}" lang="{{ App::getLocale() }}">


<head>

    <meta charset="utf-8">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Invoice Master</title>

</head>


<link rel="stylesheet" href="{{ asset('/css/pdf/pdf.css') }}">


@if(App::getLocale() == 'en')

<link rel="stylesheet" href="{{ asset('/css/pdf/en.css') }}">

@else

<link rel="stylesheet" href="{{ asset('/css/pdf/ar.css') }}">

@endif


<body>

    <div class="invoice">

        <div class="invoice-header">

            <div class="mb-2">

                <h2>{{ __('Invoice') }}</h2>

            </div>

            <div class="invoice-details">

			<table>
				<tbody>
					<tr>
	<td>	
			<ul>

			<li class="pb-2 text-gray-600">{{ __('Invoice Number') }}: {{ $data->internalID }}</li>

			<li class="text-gray-600 pb-2">{{ __('Date Of Issue') }}: {{

                        \Carbon\Carbon::parse($data->dateTimeIssued)->toDateString() }}</li>

                    <li class="text-gray-600 pb-2">{{ __('Time Of Issue') }}: {{

                        \Carbon\Carbon::parse($data->dateTimeIssued)->toTimeString() }}</li>

                    <li class="text-gray-600">{{ __('Invoice ETA') }}: {{ $data->uuid ?? '-' }}</li>

                </ul>
	</td><td>
                <div>

                    <img src="{{ BranchLogo($data->issuer_id) }}" alt="logo" width="150" height="150">

                </div>
	</td><td>
                <div>
                    {!!
						strcmp(SETTINGS_VAL('invoice settings', 'showQR', ''), '1') == 0 ?
						str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\"?>", "", 
						QrCode::size(150)->generate(env("PREVIEW_URL", "https://invoicing.eta.gov.eg/print/documents/"). $data->uuid .
						'/share/'. $data->longId))
						: '';
					!!}
                </div>
	</td>
					</tr>
				</tbody>
			</table>

            </div>

        </div>

        <hr>

        <div class="invoice-company-address">

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

            <table>

                <thead>

                    <tr>

                        <th>{{ __('Item') }}</th>

                        <th>{{ __('Quantity') }}</th>

                        <th>{{ __('Unit Price') }}</th>

                        <th>{{ __('Total Sales Amount') }}</th>

                        <th>{{ __('Tax Amount') }}</th>

                        <th>{{ __('Total') }}</th>

                    </tr>

                </thead>

                <tbody>

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

                        <td>{{ $line->description }}</td>

                        <td>{{ $line->quantity }}</td>

                        <td>{{ $line->unitValue->amountEGP }}</td>

                        <td>{{ $line->salesTotal }}</td>

                        <td>{{ $total }}</td>

                        <td>{{ $line->total }}</td>

                    </tr>

                    @endforeach


                </tbody>

            </table>

            <div class="invoice-total">

                <h4>{{ __('Invoice Total') }}: {{ __('EGP') }} {{ round($data->totalAmount, 2) }}</h4>
				<h4>{{ Numbers::TafqeetMoney(round($data->totalAmount, 2),'EGP');}} </h4>

            </div>

        </div>

        <hr>
		<table style="width:100%;"><tbody><tr>
			<td style="width:50px;"></td>
			<td style="width:50%;"> {{__('Accounting') }}</td>
			<td style="width:50px;"></td>
			<td style="width:50%;"> {{__('Approved By') }}</td>
		</tr></tbody></table>
        <hr>
		<table style="width:100%;"><tbody><tr>
			<td style="width:50px;"></td>
			<td> {{ SETTINGS_VAL('invoice settings', 'footer', ''); }}</td>
		</tr></tbody></table>
    </div>

</body>


</html>
