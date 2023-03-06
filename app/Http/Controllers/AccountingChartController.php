<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Accounting\AccountingChart;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use App\Http\Traits\ExcelWrapper;

class AccountingChartController extends Controller
{
    use ExcelWrapper;

    public function index(Request $request)
	{
		$globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                $query->where('id', 'LIKE', "%{$value}%")
					->orWhere('name', 'LIKE', "%{$value}%")
					->orWhere('description', 'LIKE', "%{$value}%");
            });
        });

        $items = QueryBuilder::for(AccountingChart::class)
			->defaultSort('id')
            ->allowedSorts(['id', 'name','description', 'status'])
            ->allowedFilters(['id', 'name', 'parent_id', 'description', $globalSearch])
            ->paginate(2000)
            ->withQueryString();

        return Inertia::render('Accounting/Chart/Index', [
            'items' => $items,
        ])->table(function (InertiaTable $table) {
            $table->column([
                'id' 	        => __('Code'),
                'name' 	        => __('name'),
                'parent_id'     => __('Parent'),
				'description'   => __('Description')
            ]);
        });
	}

    public function index_json(Request $request)
    {
        return json_encode(AccountingChart::all());
    }

    public function upload(Request $request)
    {
        $temp = [];
		$extension = $request->file->extension();
		if ($extension == 'xlsx' || $extension == 'xls')
			$temp = $this->xlsxToArray($request->file, $extension);
		else if ($extension == 'csv')
			$temp = $this->csvToArray($request->file);
		else
			return json_encode(["Error" => true, "Message" => __("Unsupported File Type!")]);
        
        foreach	($temp as $chart)
        {
            AccountingChart::updateOrCreate(
                ['id' => $chart['id']],
                [
                    'name' => $chart['name'], 
                    'parent_id' => $chart['parent_id'],
                    'description' => $chart['description'],
                    'status' => $chart['status']
                ]
            );
        }
        return json_encode(["Error" => false, "Message" => __("Data Imported Successfully!")]);
    }

    public function download(Request $request)
    {
        $data = AccountingChart::all();
        
        //render excel file now
		$reader = IOFactory::createReader('Xlsx');
		$file = $reader->load('./ExcelTemplates/AccountingChart.xlsx');
		$rowIdx = 3;
		foreach($data as $row){
			$file->getActiveSheet()->setCellValue($this->index2(1, $rowIdx), $row->id);
            $file->getActiveSheet()->setCellValue($this->index2(2, $rowIdx), $row->name);
			$file->getActiveSheet()->setCellValue($this->index2(3, $rowIdx), $row->parent_id);
            $file->getActiveSheet()->setCellValue($this->index2(4, $rowIdx), $row->description);
			$file->getActiveSheet()->setCellValue($this->index2(5, $rowIdx), $row->status);
			
			$rowIdx++;
		}
		$writer = IOFactory::createWriter($file, 'Xlsx');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="accounting_chart.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'parent_id' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $item = AccountingChart::create($data);

        return json_encode(["Error" => false, "Message" => __("Data Saved Successfully!")]);
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        $item = AccountingChart::where('id', $data['id'])->first();
        $item->status = $data['status'];
        $item->save();

        return json_encode(["Error" => false, "Message" => __("Data Saved Successfully!")]);
    }
}
