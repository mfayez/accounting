<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Master</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'DejaVu Sans';
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(4) {
        text-align: left;
    }
    
    .invoice-box table tr td:nth-child(3) {
        text-align: left;
    }
    .invoice-box table tr td:nth-child(3) {
        text-align: left;
    }
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(4) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
	body{font-family: 'DejaVu Sans';}
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
								<div>Al Sabil</div>
                            </td>
                           <td /> 
                            <td style="font-family: DejaVu Sans;text-align:left;">
                                Invoice #: {{$data->internalID}}<br>
                                Invoice ETA #: {{$data->uuid}}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                Buyer: {{$data->receiver->name}}<br>
                                Type: {{$data->receiver->type}}<br>
								ID: {{$data->receiver->receiver_id}}
                            </td>
                            <td style="font-family: 'DejaVu Sans' text-aligh:left;">
                                Issuer: {{$data->issuer->name}}<br>
                                Type: {{$data->issuer->type}}<br>
								Tax Registration #: {{$data->issuer->issuer_id}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Item
                </td>
                <td>Quantity
                </td>
                <td>Unit Price
                </td>
                <td>Sales
                </td>
                <td>Tax
                </td>
                <td>Total
                </td>
            </tr>
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
            <tr class="item">
                <td>{{$line->description}}
                </td>
                <td>{{$line->quantity}}
                </td>
                <td>{{$line->unitValue->amountEGP}}
                </td>
                <td>{{$line->salesTotal}}
                </td>
                <td>{{$total}}
                </td>
                <td>{{$line->total}}
                </td>
            </tr>
  			@endforeach 
            
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                
                <td>
                   Total
                </td>
                <td>
                   {{$data->totalAmount}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
