<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		$data = $this->all();
		//calculcate tax totals
		$taxTotals = [];
		$taxTotals2 = [];
		foreach($data['invoiceLines'] as $item) {
			foreach($item['taxItems'] as $taxItem) {
				$key1 = $taxItem['taxType']['Code'];
				if (array_key_exists($key1, $taxTotals)) {
					$taxTotals[$key1] = $taxTotals[$key1] + floatval($taxItem['value']);
				} else {
					$taxTotals[$key1] = floatval($taxItem['value']);
				}
			}
		}
		foreach($taxTotals as $key=>$val) {
			$taxTotals2=array_merge($taxTotals2, [['taxType'=>$key, 'amount'=>$val]]);
		}
		//add missing attributes
		$this->merge(["documentType"			=> "I",
						 "documentTypeVersion"	=> "1.0",
						 "issuer"				=> array_merge(["id" => $data['issuer']['issuer_id']], $data['issuer']),
						 "receiver"				=> array_merge(["id" => $data['receiver']['receiver_id']], $data['receiver']),
						 "taxTotals"			=> $taxTotals2,
		]);
	}
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		return [
			'issuer' 								=> ['required'],
			'issuer.address' 						=> ['required'],
			'issuer.address.branchID' 				=> ['required'],
			'issuer.address.country' 				=> ['required'],
			'issuer.address.governate' 				=> ['required'],
			'issuer.address.regionCity' 			=> ['required'],
			'issuer.address.street' 				=> ['required'],
			'issuer.address.buildingNumber' 		=> ['required'],
			'issuer.address.postalCode' 			=> ['nullable'],
			'issuer.address.floor' 					=> ['nullable'],
			'issuer.address.room' 					=> ['nullable'],
			'issuer.address.landmark' 				=> ['nullable'],
			'issuer.address.additionalInformation' 	=> ['nullable'],
			'issuer.type'  							=> ['required'],
			'issuer.id'  							=> ['required'],
			'issuer.name' 							=> ['required'],
			'receiver' 								=> ['required'],
			'receiver.address' 						=> ['required'],
			'receiver.address.country' 				=> ['required'],
			'receiver.address.governate' 			=> ['required'],
			'receiver.address.regionCity' 			=> ['required'],
			'receiver.address.street' 				=> ['required'],
			'receiver.address.buildingNumber' 		=> ['required'],
			'receiver.address.postalCode' 			=> ['nullable'],
			'receiver.address.floor'				=> ['nullable'],
			'receiver.address.room' 				=> ['nullable'],
			'receiver.address.landmark' 			=> ['nullable'],
			'receiver.address.additionalInformation'=> ['nullable'],
			'receiver.type' 						=> ['required'],
			'receiver.id' 							=> ['required'],
			'receiver.name' 						=> ['required'],
			'documentType' 							=> ['required', Rule::in(['I'])],
			'documentTypeVersion' 					=> ['required', Rule::in(['0.9', '1.0'])],
			'dateTimeIssued' 						=> ['required', 'date'],
			'taxpayerActivityCode' 					=> ['required'],
			'internalID' 							=> ['required'],
			'purchaseOrderReference' 				=> ['nullable'],
			'purchaseOrderDescription' 				=> ['nullable'],
			'salesOrderReference' 					=> ['nullable'],
			'salesOrderDescription' 				=> ['nullable'],
			'proformaInvoiceNumber'					=> ['nullable'],
			'invoiceLines' 							=> ['required', 'array', 'min:1'],
			'invoiceLines.*.description' 			=> ['required'],
			'invoiceLines.*.itemType'				=> ['required', Rule::in(['EGS', 'GS1'])],
			'invoiceLines.*.itemCode'				=> ['required', 'regex:/[0-9]+|EG-[0-9]+-[A-Za-z0-9_]+/'],
			'invoiceLines.*.unitType'				=> ['required', 'string'],
			'invoiceLines.*.quantity'				=> ['required', 'numeric'],
			'invoiceLines.*.internalCode' 			=> ['required', 'string'],
			'invoiceLines.*.salesTotal' 			=> ['required', 'numeric'],
			'invoiceLines.*.total' 					=> ['required', 'numeric'],
			'invoiceLines.*.valueDifference' 		=> ['required', 'numeric'],
			'invoiceLines.*.totalTaxableFees' 		=> ['required', 'numeric'],
			'invoiceLines.*.netTotal' 				=> ['required', 'numeric'],
			'invoiceLines.*.itemsDiscount'			=> ['required', 'numeric'],
			'invoiceLines.*.unitValue' 				=> ['required'],
			'invoiceLines.*.unitValue.currencySold' => ['required', Rule::in(['EGP'])],
			'invoiceLines.*.unitValue.amountEGP' 	=> ['required', 'numeric'],
			'invoiceLines.*.itemsDiscount' 			=> ['required', 'numeric'],
			'invoiceLines.*.taxableItems' 			=> ['array', 'min:0'],
			'invoiceLines.*.taxableItems.*.taxType' => ['required'],
			'invoiceLines.*.taxableItems.*.amount' 	=> ['required', 'numeric'],
			'invoiceLines.*.taxableItems.*.subType' => ['required'],
			'invoiceLines.*.taxableItems.*.rate' 	=> ['required', 'numeric'],
			'totalDiscountAmount' 					=> ['required', 'numeric'],
			'totalSalesAmount' 						=> ['required', 'numeric'],
			'netAmount' 							=> ['required', 'numeric'],
			'taxTotals' 							=> ['array'],
			'taxTotals.*.taxType' 					=> ['required', Rule::in(['T1','T2','T3','T4','T5',
																			'T6','T7','T8','T9','T10','T11',
																			'T12','T13','T14','T15','T16','T17',
																			'T18', 'T19', 'T20'])],
			'taxTotals.*.amount' 					=> ['required', 'numeric'],
			'totalAmount' 							=> ['required', 'numeric'],
			'extraDiscountAmount' 					=> ['required', 'numeric'],
			'totalItemsDiscountAmount' 				=> ['required', 'numeric']
        ];
    }
}
