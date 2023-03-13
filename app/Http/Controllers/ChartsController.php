<?php

namespace App\Http\Controllers;

use App\Models\ETA\InvoiceLine;
use App\Models\ETA\Receiver;
use Illuminate\Http\Request;

class ChartsController extends Controller
{
    public function topReceivers(Request $request, Receiver $receiver)
    {
        return $receiver->getTopReceiversStats(
            $request->has('statusList')
            ?
            (count($request->statusList) > 0
                ? true : false) : false
        );
    }

    public function topItems(Request $request, InvoiceLine $invoiceLine)
    {
        return $invoiceLine
            ->topItemsStats(
                $request->has('statusList')
                ?
                (count($request->statusList) > 0
                    ? true : false
                ) : false);
    }

}
