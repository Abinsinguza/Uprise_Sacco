<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function getLoans()
    {
        $all_loans = Loan::all();
        
        return view('pdf.loans', ['all_loans' => $all_loans]);
    }
    
}
