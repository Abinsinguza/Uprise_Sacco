<?php

namespace App\Http\Controllers;

use App\Models\Bankrate;
use App\Models\Constant;
use App\Models\Loanrequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanrequestController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }


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
      

        $pendingloanreqs = Loanrequest::where('approval', 'Grant')->get();
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
    

        
        return view('pages/loanrequests/pendingloanreq', ['rates'=>$rates,'pendingloanreqs' => $pendingloanreqs,'highlightedRequests' => $highlightedRequests,]);
        
      }

      public function getdenyLoanRequests() {
      

        $deny = Loanrequest::where('approval', 'Deny')->get();
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
  

        
        return view('pages/loanrequests/denyloanreq', ['rates'=>$rates,'deny' => $deny,'highlightedRequests' => $highlightedRequests,]);
        
      }

      public function getrejectLoanRequests()
      {
          $rates = Bankrate::where('rate', 'loan')->get();
          $loanrequests = Loanrequest::all(); 
          $reject = Loanrequest::where('clientChoice', 'rejected')->get();
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
      
          return view('pages.loanrequests.rejectedloanreq', [
              'reject' => $reject,
              'rates' => $rates,
              'highlightedRequests' => $highlightedRequests,

          ]);
      }

      public function getrank(){
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

        return $highlightedRequests;
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


     // combine function
    //  public function combinedLoanRequests()
    //  {
        
    //       $loanrequests= Loanrequest::all(); 
    //        $rejectloanreqs = Loanrequest::where('clientChoice', 'rejected')->get();
        
    //      $rates = Bankrate::where('rate', 'loan')->get();
    //      $highlightedRequests = $this->getrank();
    //      return view('pages.loanrequests.rejectedloanreq', [
    //          '$rejectloanreqs' => $rejectloanreqs,
    //          'rates' => $rates,
    //          'highlightedRequests' => $highlightedRequests
    //      ]);
    //  }

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


public function searchloanreq(Request $request)
{
    $search_text = $request->input('query');

    if (!empty($search_text)) {
        // Assuming $search_text is properly sanitized and validated before this point
        $searchloan = Loanrequest::where('referenceNumber', 'LIKE', $search_text . '%')
            ->orWhere('memberId', 'LIKE', "%{$search_text}%")
            ->orWhere('clientChoice', $search_text) // Adjust this based on column name
            ->get();
    } else {
        $searchloan = [];
    }

    

    return view('pages.loanrequests.searchloanreq', ['searchloan' => $searchloan]);
}

    

    }