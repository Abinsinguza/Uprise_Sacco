@extends('layouts.app', ['activePage' => 'table', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <h3 class="loan">
                            @section('loan')
                            @show
                        </h3>

                        <br> 

                        <div class="button-container">
                            <a class="btn btn-primary all-loans" href="{{ route('loandashboard') }}">All loans</a>
                            <a class="btn btn-primary active-loans" href="{{ route('activeLoans') }}">Active</a>
                            <a class="btn btn-primary delayed-loans" href="{{ route('delayedLoans') }}">Overdue</a>
                            <a class="btn btn-primary undisbursed-loans" href="{{ route('undisbursedLoans') }}">Undisbursed</a>
                            <a class="btn btn-primary completed-loans" href="{{ route('completedLoans') }}">Completed</a>
                        </div>
                        <div class="card-header ">
                            <a class="btn btn-primary pdf-button" href="{{ route('loanspdf') }}">
                                <i class="material-icons">Generate PDF</i>
                            </a>
                            <div class="container mt-5">

                        </div>
                        <div class="card-body table-full-width table-responsive">

                            @section('loancontent')
                            @show




                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-plain table-plain-bg">
                        <div class="card-header ">
                            <h4 class="card-title">Table on Plain Background</h4>
                            <p class="card-category">Here is a subtitle for this table</p>
                        </div>
                        <div class="card-body table-full-width table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection