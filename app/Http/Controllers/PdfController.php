<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use Illuminate\Http\Request;

class PdfController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function getLoans()
    {
        $all_loans = Loan::all();
        
        return view('pdf.loans', ['all_loans' => $all_loans]);
    }
     // public function pdfPage()
    // {
    //     return view('pdf.report'); // Assuming your Blade file is named 'blade.blade.php'
    // }
   
    public function pdfPage() {
        $totalAmount = Deposit::sum('amount');
        $totalMembers = Member::count();
        $totalContribution = Member::sum('balance');
        $totalLoanBalance = Loan::sum('loanBalance');
        $availableFunds = ($totalAmount - $totalLoanBalance);
    
        $membersAveragePerformance = Member::avg('performance');
        $loansAveragePerformance = Loan::avg('performance');
    
        $overallPerformance = ($membersAveragePerformance + $loansAveragePerformance) / 2;
    
        $currentDate = now();
        $sixMonthsAgo = $currentDate->copy()->subMonths(6); // Use copy() to avoid modifying the original object
    
        $activeMembers = Member::where('contributionStart', '<=', $sixMonthsAgo)
                                ->where('performance', '>', 100)
                                ->get();
        $activeMembers_count = count($activeMembers);
    
        $newMembers = Member::where('contributionStart', '>', $sixMonthsAgo)->get();
        $newMembers_count = count($newMembers);
    
        $member = Member::all();
        $dem_count = count($member) - ($newMembers_count + $activeMembers_count);
    
        // You have a syntax error here. Change 'veiw' to 'view' and add a comma after 'compact'
        return view('pdf.report', compact('totalAmount', 'totalMembers', 'totalContribution', 'totalLoanBalance', 'availableFunds', 'membersAveragePerformance', 'loansAveragePerformance', 'overallPerformance', 'activeMembers_count', 'newMembers_count', 'dem_count'));
    }
    
}
