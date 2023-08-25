<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Deposit;
use App\Mail\SendPDFEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;

class EmailController extends Controller
{
    //
    public function email()
    {
    
            $loansController = new LoanController();
        
            $sixMonthsAgo = Carbon::now()->subMonths(6);

            // Generate email content
            $emailContent = $this->generateEmailContent();
        
            $activeMembers = Member::where('contributionStart', '<=', $sixMonthsAgo)
            ->where('performance', '>', 100)
            ->get();
        
            // Iterate through active members
            foreach ($activeMembers as $member) {
                // Generate the PDF for the member using the MembersController instance
                $pdf = $loansController->getPdf($member);
        
                // Send email
                Mail::to($member->email)->send(new SendPDFEmail($member, $pdf));
                
            }
            return redirect()->back()->with('status', 'Report sent to active members successfully.');
            // return('admin.member.activemember');
        }
        
        private function generateEmailContent()
        {
            $totalAmount = Deposit::sum('amount');
            $totalLoanBalance = Loan::sum('loanBalance');
            
    
            $emailContent = [
                'totalAmount' => $totalAmount,
                'totalLoanBalance' => $totalLoanBalance,
                // 'amountClearedOnLoan' => $amountClearedOnLoan,
                // ... other values ...
            ];
    
            return $emailContent;
        }
}
