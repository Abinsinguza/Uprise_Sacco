<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Deposit;
use Illuminate\Http\Request;


class DepositController extends Controller

{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function showForm()
    {
        return view('deposit');
    }
    

    public function upload(Request $request)
    {
        // Get file
        $upload = $request->file('upload-file');
        $filePath = $upload->getRealPath();

        // Open and read the file
        $file = fopen($filePath, 'r');

        $header = fgetcsv($file);
        // dd($header);

        $escapedHeader = [];


        // Validate and sanitize headers
        foreach ($header as $key => $value) {
            $lheader = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }

        // Loop through the columns
        while ($columns = fgetcsv($file)) {
            if ($columns[0] == "") {
                continue;
            }

            // Trim data
            // foreach ($columns as $key => &$value) {
            //     $value = preg_replace('/\D/', '', $value);
            // }

            $data = array_combine($escapedHeader, $columns);

            // Set data types
            foreach ($data as $key => &$value) {
                $value = ($key == "receiptNo" || $key == "amount") ? (float)$value : $value;
            }

            // Create deposit record
            $deposit = new Deposit();
            $deposit->receiptNo = $data['receiptno'];
            $deposit->amount = $data['amount'];
            $deposit->date = $data['date'];
            $deposit->memberId = $data['memberid'];
            //$deposit->status = $data['status'];
            $deposit->save();
        }

        fclose($file);

        // Redirect or display a success message
        return redirect()->back()->with('success', 'Deposits uploaded successfully.');
    }
    public function searchDeposit()
    {
        $search_text = $_GET['query'];

        // Check if the search text is a valid integer (for receipt number) or a string (for member ID)
        if (is_numeric($search_text) ) {
            $searchdeposits = Deposit::where('receiptNo', 'LIKE', $search_text . '%')->get();
        } else  {
            $searchdeposits = Deposit::where(function ($query) use ($search_text) {
                $query->where('memberId', 'LIKE', "%{$search_text}%")
                    ->orWhereDate('date', $search_text); // Assuming date is a column in your 'Deposit' table.
            })->get();

        }
        $totalAmount = $searchdeposits->sum('amount');

        $deposits = Deposit::all();

        return view('pages.deposits.searchdeposit', ['deposits' => $deposits, 'searchdeposits' => $searchdeposits, 'totalAmount' => $totalAmount,]);

       // return view('pages/searchdeposit', compact('searchdeposits'));
    
        // Now you can use $deposits to access the search results
    }

 

public function totalAmountByMonth()
{
    $currentMonth = Carbon::now()->month;

    $totalAmountByMonth = Deposit::selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_amount')
        ->whereMonth('date', $currentMonth) // Filter for the current month
        ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    $formattedAmounts = [];

    foreach ($totalAmountByMonth as $amount) {
        $monthName = Carbon::createFromDate($amount->year, $amount->month)->format('F');
        $formattedAmounts[] = [
            'month' => $monthName,
            'year' => $amount->year,
            'total_amount' => $amount->total_amount,
        ];
    }

    return view('pages.total', ['amountsByMonth' => $formattedAmounts]);
}

public function depositsInCurrentMonth()
{
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    $thisdeposits = Deposit::whereYear('date', $currentYear)
                        ->whereMonth('date', $currentMonth)
                        ->get();
         $totalAmount = $thisdeposits->sum('amount');

    return view('pages.deposits.thismonth', ['thisdeposits' => $thisdeposits,'totalAmount' => $totalAmount]);
}

public function loanPayments()
{
    $loanPayments = Deposit::where('status', 'loan payment')->get();

    $totalLoanPayments = $loanPayments->sum('amount');

    return view('pages.deposits.loan_payments', ['loanPayments' => $loanPayments, 'totalLoanPayments' => $totalLoanPayments]);
}
public function depositedDeposits()
{
    $depositedDeposits = Deposit::where('status', 'deposited')->get();

    $totalDepositedAmount = $depositedDeposits->sum('amount');

    return view('pages.deposits.deposited', ['depositedDeposits' => $depositedDeposits, 'totalDepositedAmount' => $totalDepositedAmount]);
}

public function pendingDeposits()
{
    $pendingDeposits = Deposit::where('status', 'pending')->get();

    $totalPendingAmount = $pendingDeposits->sum('amount');

    return view('pages.deposits.pending', ['pendingDeposits' => $pendingDeposits, 'totalPendingAmount' => $totalPendingAmount]);
}
public function allDeposits()
{
    $allDeposits = Deposit::all();

    $totalAllAmount = $allDeposits->sum('amount');

    return view('pages.deposits.alldeposits', ['allDeposits' => $allDeposits, 'totalAllAmount' => $totalAllAmount]);
}

public function loanPaymentsAndDepositedDeposits()
{
    $loanPaymentsAndDepositedDeposits = Deposit::whereIn('status', ['loan payment', 'deposited'])->get();

    $totalAmount = $loanPaymentsAndDepositedDeposits->sum('amount');

    return view('pages.deposits.trans', ['loanPaymentsAndDepositedDeposits' => $loanPaymentsAndDepositedDeposits, 'totalAmount' => $totalAmount]);
}


public function loanPaymentsAndDepositedDepositsOnCurrentDate()
{
    $currentDate = Carbon::now()->toDateString();

    $loanPaymentsAndDepositedDeposits = Deposit::whereDate('updated_at', $currentDate)
                                                ->whereIn('status', ['loan payment', 'deposited'])
                                                ->get();

    $totalAmount = $loanPaymentsAndDepositedDeposits->sum('amount');

    return view('pages.deposits.current_date', ['loanPaymentsAndDepositedDeposits' => $loanPaymentsAndDepositedDeposits, 'totalAmount' => $totalAmount]);
}
public function depositsInCurrentWeek()
{
    $currentWeekStart = Carbon::now()->startOfWeek();
    $currentWeekEnd = Carbon::now()->endOfWeek();

    $deposits = Deposit::whereBetween('updated_at', [$currentWeekStart, $currentWeekEnd])->get();

    $totalAmount = $deposits->sum('amount');

    return view('pages.deposits.current_week', ['deposits' => $deposits, 'totalAmount' => $totalAmount]);
}
public function largeLoanPaymentsAndDepositedDeposits()
{
    $largeLoanPaymentsAndDepositedDeposits = Deposit::whereIn('status', ['loan payment', 'deposited'])
                                                      ->where('amount', '>', 450000)
                                                      ->get();

    $totalAmount = $largeLoanPaymentsAndDepositedDeposits->sum('amount');

    return view('pages.deposits.large', ['largeLoanPaymentsAndDepositedDeposits' => $largeLoanPaymentsAndDepositedDeposits, 'totalAmount' => $totalAmount]);
}


}
