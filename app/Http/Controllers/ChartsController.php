<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartsController extends Controller
{
	function topReceivers(){
		$data = DB::select('select t1.name as name, sum(t2.netAmount) as value
				from Receiver t1 inner join Invoice t2 on t1.id = t2.receiver_id
				group by t1.name
				order by 2 desc
				limit 10');
		return $data;
	}

	function topItems(){
		$data = DB::select('select t2.codeNamePrimaryLang as name, sum(t1.salesTotal) as value
				from InvoiceLine t1 inner join ETAItems t2 on t1.itemCode = t2.itemCode
				group by t2.codeNamePrimaryLang
				order by 2 desc
				limit 10');
		return $data;
	}

}
