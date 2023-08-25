<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;


class MemberController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function registerMember (Request $request){
        $memberInf=$request->validate([
            'email'=> 'required',
            'name'=> 'required',
            'username'=> 'required',
            'password'=> 'required',
            'phoneNumber'=> 'required',
            'contributionStart'=>'required',
            'contributionPerMonth' => 'required|in:50000,100000,150000,200000'
        ]);

      $memberInf['email']=strip_tags($memberInf['email']);
      $memberInf['name']=strip_tags($memberInf['name']);
      $memberInf['username']=strip_tags($memberInf['username']);
      $memberInf['password']=strip_tags($memberInf['password']);
      $memberInf['phoneNumber']=strip_tags($memberInf['phoneNumber']);
      $memberInf['contributionStart']=strip_tags($memberInf['contributionStart']);
      $memberInf['contributionPerMonth']=strip_tags($memberInf['contributionPerMonth']);
      Member::create($memberInf);
        //return'member registered successfully .................';
        return redirect()->back()->with('success', 'member registered successfully.');
        
        
    }
    public function showData($id)
    {
        // Fetch the record from the database based on the given ID
        $members= Member::find($id);
        return view('admin/member/edit',compact('members'));

    }
    public function Update(Request $request)
    {
        // Fetch the record from the database based on the given ID
        $member=Member::find($request -> id);
        $member-> email = $request->input('email');
        $member-> username = $request->input('username');
        $member-> name = $request->input('name');
        $member-> phoneNumber = $request->input('phoneNumber');
        $member-> password = $request->input('password');
        $member-> contributionPerMonth = $request->input('contributionPerMonth');
        $member->Update();
        return redirect()->back()->with('success', 'Member updated successfully');
        

    }
    public function upload_member(Request $request)
{
    // Get file
    $upload = $request->file('upload-file');
    $filePath = $upload->getRealPath();

    // Open and read the file
    $file = fopen($filePath, 'r');

    $header = fgetcsv($file);

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

        $data = array_combine($escapedHeader, $columns);

        // Set data types (if needed)
        // $data['memberId'] = $data['memberid'];
        // $data['balance'] = (float) $data['balance'];
        // ... add any other data type conversions ...

        // Create member record
        $member = new Member();
        //$member->memberId = $data['memberid'];
        $member->email = $data['email'];
        $member->name = $data['name'];
        $member->username = $data['username'];
        $member->password = $data['password'];
        $member->phoneNumber = $data['phonenumber'];
       // $member->balance = $data['balance'];
        $member->contributionStart = $data['contributionstart'];
        $member->contributionPerMonth = $data['contributionpermonth'];
        //$member->monthsCleared = $data['monthsCleared'];
        //$member->performance = $data['performance'];
        //$member->created_at = $data['created_at'];
        //$member->updated_at = $data['updated_at'];
        $member->save();
    }

    fclose($file);

    // Redirect or display a success message
    return redirect()->back()->with('success', 'Members uploaded successfully.');
}


public function searchMember(Request $request)
{
    $search_text = $request->input('query');

    if (!empty($search_text)) {
        // Assuming $search_text is properly sanitized and validated before this point
        $searchmembers = Member::where('name', 'LIKE', $search_text . '%')
            ->orWhere('memberId', 'LIKE', "%{$search_text}%")
            ->orWhere('username', $search_text) // Adjust this based on column name
            ->get();
    } else {
        $searchmembers = [];
    }

    // You might not need to retrieve all members if you're showing search results and all members together
    // $members = Member::all();

    return view('admin.member.searchmember', ['searchmembers' => $searchmembers]);
}

public function getActiveMembers()
{
    $sixMonthsAgo = now()->subMonths(6); // Calculate the date six months ago

    $activeMembers = Member::where('contributionStart', '<=', $sixMonthsAgo)
        ->where('performance', '>', 100)
        ->get();

        $totalAmount = $activeMembers->sum('balance');

    return view('admin.member.activemember', ['activeMembers' => $activeMembers,'totalAmount' => $totalAmount]);
}

public function getNewMembers()
{
    $sixMonthsAgo = now()->subMonths(6);

    $newMembers = Member::where('contributionStart', '>', $sixMonthsAgo)->get();

    $totalAmount = $newMembers->sum('balance');
    
    return view('admin.member.newmember', ['newMembers' => $newMembers,'totalAmount' => $totalAmount ]);
}

public function largeContribution()
{
    $largeLoanPaymentsAndDepositedDeposits = Deposit::whereIn('status', ['loan payment', 'deposited'])
                                                      ->where('amount', '>', 450000)
                                                      ->get();

    $totalAmount = $largeLoanPaymentsAndDepositedDeposits->sum('amount');

    return view('pages.deposits.large', ['largeLoanPaymentsAndDepositedDeposits' => $largeLoanPaymentsAndDepositedDeposits, 'totalAmount' => $totalAmount]);
}


}
