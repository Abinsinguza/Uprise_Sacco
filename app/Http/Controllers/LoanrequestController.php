<?php

namespace App\Http\Controllers;

use App\Models\Bankrate;
use App\Models\Constant;
use App\Models\Loanrequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanrequestController extends Controller
{


    public function getloanreq(){
       
        $loanrequests= Loanrequest::all(); 
        $rates = Bankrate::where('rate', 'loan')->get();

          // Step 1: Retrieve the 10 latest pending requests from the database
               $pendingRequests = Loanrequest::where('approval', 'pending')
               ->orderBy('created_at', 'desc')
               ->take(10)
               ->get();
       
           // Step 2: Rank the pending requests based on specified criteria
           $rankedRequests = $pendingRequests->sortByDesc(function ($request) {
               return $request->avPerformance + $request->totalContribution + $request->monthlyContribution;
           });
       
           // Step 3: Highlight the top and bottom three requests
           $highlightedRequests = $rankedRequests->take(3)->concat($rankedRequests->take(-3))->unique();


        return view('pages/loanrequests/loanrequest',['loanrequests'=>$loanrequests,
        'rates'=>$rates,'highlightedRequests' => $highlightedRequests]);



    }
    
    public function getPendingLoanRequests() {
      

        $pendingloanreqs = Loanrequest::where('approval', '=', 'pending')->get();
        

        
        return view('pages/loanrequests/pendingloanreq', ['pendingloanreqs' => $pendingloanreqs]);
        
      }

      public function showPendingLoanRequests()
      {
          // Step 1: Retrieve the 10 latest pending requests from the database
          $pendingRequests = Loanrequest::where('approval', 'pending')
              ->orderBy('created_at', 'desc')
              ->take(10)
              ->get();
      
          // Step 2: Rank the pending requests based on specified criteria
          $rankedRequests = $pendingRequests->sortByDesc(function ($request) {
              return $request->avPerformance + $request->totalContribution + $request->monthlyContribution;
          });
      
          // Step 3: Highlight the top and bottom three requests
          $highlightedRequests = $rankedRequests->take(3)->concat($rankedRequests->take(-3))->unique();
      
          // Step 4: Pass the ranked and updated requests to the view
          return view('pages.loanrequests.rank', ['highlightedRequests' => $highlightedRequests]);
      }


      //Bank rate
    //   public function showData($id)
    //   {
    //       // Fetch the record from the database based on the given ID
    //       $rate= Bankrate::find($id);
    //       return view('pages/loanrequests/edit',compact('rates'));
  
    //   }
     public function updateRate (Request $request){
        $rate=Bankrate::find($request -> id);
        $rate-> value = $request->input('value');
        $rate->Update();

        return redirect()->back()->with('status', 'Rate updated successfully');

     }

     public function approval(Request $request)
     {
         // Fetch the record from the database based on the given ID
         $loanreq=Loanrequest::find($request -> id);
         $loanreq-> approval = $request->input('approval');
         $loanreq->Update();
         return redirect()->back()->with('status', 'Member apprroved successfully');
         
 
     }

     public function updateApprovals(Request $request)
           {
    $approvals = $request->input('approvals');
    
    foreach ($approvals as $index => $approval) {
        $referenceNumber = $request->input('referenceNumber_id_' . $index); // Assuming you have input fields named like 'referenceNumber_id_0', 'referenceNumber_id_1', etc.
        
        // Fetch the record from the database based on the given referenceNumber
        $loanRequest = Loanrequest::where('referenceNumber', $referenceNumber)->first();

        if ($loanRequest) {
            $loanRequest->approval = $approval;
            $loanRequest->update();
        }
    }

    return redirect()->back()->with('status', 'Approvals updated successfully');
}


    

    }