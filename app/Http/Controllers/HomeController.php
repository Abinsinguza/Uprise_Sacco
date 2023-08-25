<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $availableFunds = ($totalAmount) - ($totalLoanBalance)/1000000; // Calculate available funds

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
        
        $recentDeposits = Deposit::orderBy('updated_at', 'desc')
          ->limit(7)
            ->get();
        
        $notifications = Notification::whereNull('read_at')->get();

        $totalAmountByMonth = Deposit::selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_amount')
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

        


        return view('dashboard', ['amountsByMonth' => $formattedAmounts], compact(
            'totalMembers', 'totalAmount', 'totalContribution', 'availableFunds',
            'activeMembers_count', 'newMembers_count', 'dem_count',
            'membersAveragePerformance', 'loansAveragePerformance', 'overallPerformance',
            'notifications','recentDeposits'
        ));
        
    }
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }


    public function markAllNotificationsAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    public function notification(){
        $unread = Notification::whereNull('read_at')->get();
        $read = Notification ::whereNotNull('read_at')->get();
       return view('pages.notifications', compact('unread', 'read'));
    }

    public function totalAmountByMonth()
    {
        $totalAmountByMonth = Deposit::selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_amount')
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
    

}



