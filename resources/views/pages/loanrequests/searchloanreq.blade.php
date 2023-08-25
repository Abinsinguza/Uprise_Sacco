@extends('layouts.app', ['activePage' => 'table', 'title' => 'UPRISE SACCO Loan Requests', 'navName' => 'Searched Loan Requests', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                          @if (session('status'))
                          <div class="alert alert-success">
                                {{ session ('status')}}
                          </div>
                          @endif
                          
                      
                       
                            <div class="container mt-5">    
                                <div class="loanbtn">
                                <a href="{{ route('loanreqdashboard') }}">All Requests</a>
                                <a href="{{ route('denyrequests') }}">Denied</a>
                                <a href="{{ route('pendingloans') }}">Granted</a>
                                <a href="{{ route('rejrequests') }}">Rejected</a>
                                
                                <div class= "searchloanreq">
                                  <form action="{{ route('search.loans') }}" method="GET">
                                    @csrf

                                    <label for="search"></label>
                                    <input type="text" id="query" name="query" name="search" placeholder="search for loan...">
                                    <button type="submit" style="background-color: #e6e5e4; color: rgb(22, 3, 3); border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;"> <i class="fas fa-search"></i>Search</button>
                                </form>
                                </div>
                           
                            
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>Member ID</th>
                                    <th>Reference No</th>
                                    <th>Amount(UGX)</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th>Client Choice</th>
                                    <th>Average Performance(%)</th>
                                </thead>
                                <tbody>
                                    @foreach($searchloan as $req)
                    <tr>
                        <td>{{ $req->memberId }}</td>
                        <td>{{ $req->referenceNumber }}</td>
                        <td> {{ number_format($req->loan_amount, 2) }}</td>
                        <td>{{ $req->periodInMonths }}</td>
                        <td>{{ $req->approval }}</td>
                        <td>{{ $req->clientChoice }}</td>
                        <td>{{ $req->avPerformance }}</td>
                            
                    </tr>
                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                                      
        


                
                                    
                                                                                

                                                                     



            </div>
        </div>
    </div>


      
@endsection