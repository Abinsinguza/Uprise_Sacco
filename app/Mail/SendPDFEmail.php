<?php

namespace App\Mail;

use Dompdf\Dompdf;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use App\Models\Loanrequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPDFEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $emailContent;
     
    

    /**
     * Create a new message instance.
     */
    public function __construct($member, $emailContent)
    {
        $this->member = $member;
        $this->emailContent = $emailContent; // Pass email content to the view
    }


    

    public function build()
    {
        $currentTime = now();
        $totalAmount = Deposit::sum('amount');
        $pdf = $this->generatePDF(); // Generate the PDF content

        return $this->subject('Your Uprise Sacco Report')
            ->view('mails.report',['totalAmount' => $totalAmount,'currentTime' => $currentTime]) // Use the email view
            ->attachData($pdf, 'UpriseSaccoReport.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    private function generatePDF()

    {
        $totalMembers = Member::count();
        $totalContribution = Member::sum('balance');
        $totalLoanBalance = Loan::sum('loanBalance');
        $totalAmount = Deposit::sum('amount');
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
        $currentTime = now();

        $bestMembers = Member::where('performance', '>', 100)
        ->orderByDesc('balance')
        ->limit(10)
        ->get();
        $poorMembers = Member::orderBy('performance')
            ->limit(10)
            ->get();

            $recentApprovedLoanRequests = Loanrequest::where('approval', 'Grant')
             ->orderBy('created_at', 'desc')
             ->limit(10)
            ->get();

            $recentLoan = Loan::orderBy('created_at', 'desc')
              ->limit(10)
               ->get();
               
               $loans = Loan::where('performance', '<', 100)
               ->where('performance', '!=', 0.00)
               ->get();

               $completeLoans = Loan::where('status', 'complete')
              ->orderBy('created_at', 'desc')
              ->limit(10)
              ->get();
        
        $pdf = new Dompdf();
        $html = view('pdf.report', [
            'totalMembers' => $totalMembers,
        'totalContribution' => $totalContribution,
        'totalLoanBalance' => $totalLoanBalance,
        'totalAmount' => $totalAmount,
        'availableFunds' => $availableFunds,
        'membersAveragePerformance' => $membersAveragePerformance,
        'loansAveragePerformance' => $loansAveragePerformance,
        'overallPerformance' => $overallPerformance,
        'currentDate' => $currentDate,
        'sixMonthsAgo' => $sixMonthsAgo,
        'activeMembers' => $activeMembers,
        'activeMembers_count' => $activeMembers_count,
        'newMembers' => $newMembers,
        'newMembers_count' => $newMembers_count,
        'dem_count' => $dem_count,
        'currentTime' => $currentTime,
        'bestMembers'=> $bestMembers,
        'poorMembers'=> $poorMembers,
        'recentApprovedLoanRequests'=>$recentApprovedLoanRequests,
        'recentLoan'=>$recentLoan,
        'loans'=>$loans,
        'completeLoans'=>$completeLoans,
        ])->render();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->output();
    }
}
