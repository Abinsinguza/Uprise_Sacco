@extends('layouts.app', ['activePage' => 'table', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <div class="container mt-5">
                                <div class="loanbtn">
                                  <a href="{{ route('pendingloans') }}">Pending Requests</a>
                                  <a href="{{ route('rankloanrequests') }}">Ranked Requests</a>
    
                                   </div>

            
                        </div>
                        <div class="card-body table-full-width table-responsive">
                         
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Member ID</th>
                                    <th>Reference No</th>
                                    <th>Amount(UGX)</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th>Client Choice</th>
                                    <th>Average Performance(%)</th>
                                </thead>
                                <tbody>
                                    @foreach ($highlightedRequests as $req)
                                    <tr class="{{ $loop->index < 3 ? 'light-green' : 'light-red' }}">
                        <td>{{ $req->id }}</td>>
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