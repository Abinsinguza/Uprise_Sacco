<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalAmount = Deposit::sum('amount')/1000000;
        $totalMembers = Member::count();
        $totalContribution = Member::sum('balance')/1000000; // Calculate the total contribution from 'balance'
        $totalLoanBalance = Loan::sum('loanBalance'); // Calculate the total loan balance
        $availableFunds = ($totalAmount - $totalLoanBalance)/1000000; // Calculate available funds

        $membersAveragePerformance = Member::avg('performance'); // Calculate average performance from 'members' table
        $loansAveragePerformance = Loan::avg('performance'); // Calculate average performance from 'loans' table
    
        // Calculate overall performance as the average of both averages
        $overallPerformance = ($membersAveragePerformance + $loansAveragePerformance) / 2;


        $currentDate = now(); // Get the current date and time
        $sixMonthsAgo = $currentDate->subMonths(6); // Subtract 5 months from the current date

        $activeMembers = Member::where('contributionStart', '<=', $sixMonthsAgo)
                                  ->where('performance', '>', 100)
                                  ->get();
                                  $activeMembers_count = count($activeMembers); 
        $newMembers = Member::where('contributionStart', '>', $sixMonthsAgo)->get() ;
        $newMembers_count = count($newMembers);   
        $member = Member::all();
        $dem_count = count($member)-(count($newMembers) + count($activeMembers));                                         

        return view('dashboard', compact(
            'totalMembers', 'totalAmount', 'totalContribution', 'availableFunds',
            'activeMembers_count', 'newMembers_count', 'dem_count',
            'membersAveragePerformance', 'loansAveragePerformance', 'overallPerformance'
        ));
        
    }

}
