@extends('layouts.app', ['activePage' => 'table', 'title' => 'UPRISE SACCO Deposit', 'navName' => 'This Months Deposits', 'activeButton' => 'laravel'])

@section('content')
    <div class="content" style="font-family: Arial, sans-serif;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover" style="border: 1px solid #e0e0e0; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background-color: #f5f5f5;">
                            <div class="container mt-5">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Add CSV File</button>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#searchModal">Search</button>
                                <!-- Upload Modal -->
  <!-- Upload Modal -->                             <!-- Upload Modal -->
                                <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="uploadModalLabel">Upload CSV File</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('/upload') }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="upload-file">Upload</label>
                                                        <input type="file" name="upload-file" class="form-control">
                                                    </div>
                                                    <input class="btn btn-success" type="submit" value="Upload File" name="submit">
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                
                                      <!-- Search Modal -->
                                <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="searchModalLabel">Search Modal</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="/searchDeposit" method="GET" class="form-inline">
                                                    <div class="input-group">
                                                        <input type="text" name="query" class="form-control" placeholder="Search...">
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="nc-icon nc-zoom-split"></i> Search
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- ... rest of the code ... -->
                                <p style="font-weight: bold; color:rgb(143, 49, 49); text-align: right;"> Total:UGX,{{number_format($totalAmount)}}</p>
                                <div style="margin-bottom: 20px; text-align: left;">
                                    <a href="{{ route('current-month-deposits') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Current Month Deposits</a>
                                    <a href="{{ route('loan-payments') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Loan Payments</a>
                                    <a href="{{ route('deposited-deposits') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Contribution Deposits</a>
                                    <a href="{{ route('pending-deposits') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Pending Deposits</a>
                                    <a href="{{ route('all-deposits') }}" style="color: #007bff; text-decoration: none; font-weight: bold;">All Deposits</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-full-width table-responsive" style="padding: 20px;">
                            
                            <table class="table table-hover table-striped" style="border-collapse: collapse; width: 100%;">
                                <thead style="background-color: #c9c1c1;">
                                    <th style="color: black; font-weight: bold; ">Receipt No</th>
                                    <th style="color: black; font-weight: bold;">Amount(UGX)</th>
                                    <th style="color: black; font-weight: bold;">Date</th>
                                    <th style="color: black; font-weight: bold;">Member ID</th>
                                    <th style="color: black; font-weight: bold;">Status</th>
                                </thead>
                                <tbody>
                                    @foreach($thisdeposits as $deposit)
                    <tr>
                        <td>{{ $deposit->receiptNo }}</td>
                        <td> {{ number_format($deposit->amount, 2) }}</td>
                        <td>{{ $deposit->date }}</td>
                        <td>{{ $deposit->memberId }}</td>
                        <td>{{ $deposit->status }}</td>
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