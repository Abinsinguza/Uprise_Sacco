@extends('layouts.app', ['activePage' => 'table', 'title' => 'UPRISE SACCO Deposit', 'navName' => 'Deposits', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header ">
                            <div class="container mt-5">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Add CSV File</button>
                                <!-- Button to trigger the modal -->
                                <button class="btn btn-primary" data-toggle="modal" data-target="#searchModal">Search</button>
                                
                                <!-- Upload Modal -->
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
                                
                                
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>Receipt No</th>
                                    <th>Amount(UGX)</th>
                                    <th>Date</th>
                                    <th>Member ID</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
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