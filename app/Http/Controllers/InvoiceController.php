<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:shift_manager');
    }

    public function index()
    {
        $invoices = Invoice::
        select('number', 'created_at')
            ->groupBy('number', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('invoice.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('invoice.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceStoreRequest $request)
    {
        for ($i = 0; $i < count($request->product_name); $i++) {
            $data[] = [
                'number' => $request->number,
                'product_name' => $request->product_name[$i],
                'quantity' => $request->quantity[$i],
                'unit' => $request->unit[$i],
                'unit_price' => $request->unit_price[$i],
                'added_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (Invoice::CheckInvoiceNumberExists($request)) {
            return back()->with('error', 'Duplicate Invoice number')->withInput();
        }

        for ($i = 0; $i < count($data); $i++) {

            if (Product::where('name', $data[$i]['product_name'])->exists()) {

                Product::where('name', $data[$i]['product_name'])
                    ->increment('quantity', $data[$i]['quantity']);

            } else {
                Product::insert([
                    'name' => $data[$i]['product_name'],
                    'quantity' => $data[$i]['quantity'],
                    'unit' => $data[$i]['unit'],
                    'sell_price' => $data[$i]['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $data_for_invoice = [];
        for ($i = 0; $i < count($data); $i++) {
            $data_for_invoice[] = [
                'number' => $data[$i]['number'],
                'product_id' => Product::where('name', $data[$i]['product_name'])->pluck('id')[0],
                'quantity' => $data[$i]['quantity'],
                'unit_price' => $data[$i]['unit_price'],
                'added_by' => $data[$i]['added_by'],
                'created_at' => $data[$i]['created_at'],
                'updated_at' => $data[$i]['updated_at'],
            ];

        }

        Invoice::insert($data_for_invoice);

        return redirect()->route('invoice.create')->with('success', 'Successful added invoice');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice
     * @return \Illuminate\Http\Response
     */
    public function show($number)
    {
        $invoice_data = Invoice::where('number', $number)->get();

        return view('invoice.show', compact('invoice_data'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * *@param  \App\Models\Invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /*Show statistics page*/

    public function statistics()
    {
        $products = DB::select('SELECT id, name FROM products ');
        $result = null;

        if (request()->all()) {

            request()->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'product' => 'required',
                'min_product_quantity' => 'nullable|integer|min:0'
            ]);

            $start_date = request()->start_date;
            $end_date = request()->end_date . ' 23:59:59';
            $product = request()->product;
            if (request()->has('min_product_quantity')) {
                $min_product_quantity = request()->min_product_quantity;
            }

            $query = "SELECT invoices.number, invoices.created_at
                        FROM invoices 
                        WHERE created_at > '$start_date'  
                        AND created_at < '$end_date'";

            if ($product != 'all') {
                $query .= " AND product_id = '$product' ";
            }
            if (request()->has('min_product_quantity') && $min_product_quantity > 0) {
                $query .= " AND quantity >= '$min_product_quantity' ";
            }
            $query .= " GROUP BY invoices.number, invoices.created_at";

            $result = DB::select($query);
        }

        return view('invoice.statistics', compact('products', 'result'));
    }
}