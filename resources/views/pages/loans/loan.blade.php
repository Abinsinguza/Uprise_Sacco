@extends('layouts.app', ['activePage' => 'table', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <h3 class="loan">All Loans</h3>

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

                                
                                {{-- <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Add CSV File</button> --}}
 
                               
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
                               
                                

                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="tableloan">
                                <thead>
                                    <th>Loan ID</th>
                                    {{-- <th>Member ID</th> --}}
                                    <th>Repayment Amount(UGX)</th>
                                    <th>Installment Amount(UGX)</th>
                                    {{-- <th>Period(months)</th> --}}
                                    <th>Payment Start Date</th>
                                    <th>PROGRESS</th>
                                    <th>Cleared Amount(UGX)</th>
                                    <th>Loan Balance(UGX)</th>
                                    <th>Action</th>
                                  
                                </thead>
                                <tbody>
                                    @foreach( $loans as $data)
                    <tr>
                        <td>{{ $data->loanId }}</td>
                        {{-- <td>{{ $data->memberId }}</td> --}}
                        <td> {{number_format ($data->amountToPay, 2) }}</td>
                        <td> {{ number_format($data->amountPerInstallment, 2) }}</td>
                        {{-- <td>{{ $data->installments }}</td> --}}
                        <td>{{ $data->paymentStart }}</td>
                        <td>{{ $data->loanProgress }}</td>
                        <td>{{ $data->amountCleared }}</td>
                        <td>{{ $data->loanBalance }}</td>
                        <td>
                            <a href="" class="btn btn-success"data-toggle="modal" data-target="#viewModalloan{{ $data->memberId }}"><i class="bi bi-eye">view</i></a> 
                            
                        </td>
                              
                    </tr>
                      <!-- View Modal -->
<div class="modal fade" id="viewModalloan{{ $data->memberId }}" tabindex="-1" role="dialog" aria-labelledby="viewModalloanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 style="color: brown"   class="modal-title" id="modalViewLabel{{ $data->memberId }}">Loan Details</h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p><strong>Member ID:</strong> {{ $data->memberId }}</p>
          <p><strong>Loan ID:</strong> {{ $data->loanId }}</p>
          <p><strong>Application Num:</strong> {{ $data->referenceNumber }}</p>
          <p><strong>Amount Given:</strong> {{ number_format($data->amount_given, 2 )}}</p>
          <p><strong>Repayment:</strong> {{number_format ($data->amountToPay, 2) }}</p>
          <p><strong>Payment Start Date:</strong> {{ $data->paymentStart }}</p>
          <p><strong>Loan Balance: UGX,</strong> {{  number_format ($data->loanBalance, 2) }}</p>
          <p><strong>Period:</strong> {{ $data->installments }}months</p>
          <p><strong>Installment Per Month:UGX,</strong> {{  number_format($data->amountPerInstallment , 2)}}</p>
          <p><strong>installments Cleared:</strong> {{ $data->installmentCleared }}</p>
          <p><strong>loan Progress:</strong> {{ $data->loanProgress }}</p>
          <p><strong>loan Fine:</strong> {{ number_format($data->loanFine , 2) }}</p>
          <p><strong>Delayed Installment:</strong> {{ $data->delayedInstallments }}</p>
          <p><strong>Performance:</strong> <span style="color: {{ $data->performance >= 100 ? 'green' : 'red' }}">{{ $data->performance }}%</span></p>

          <p><strong>Status:</strong> <span style="color: {{ $data->status === 'complete' ? 'green' : 'brown' }}">{{ $data->status }}</span></p>
          <p><strong>Created At:</strong> {{ $data->created_at }}</p>
          <p><strong>Updated At:</strong> {{ $data->updated_at }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
                    @endforeach
                                </tbody>
                            </table>
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
                            <table class="table table-hover">
                                <thead>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Salary</th>
                                    <th>Country</th>
                                    <th>City</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Dakota Rice</td>
                                        <td>$36,738</td>
                                        <td>Niger</td>
                                        <td>Oud-Turnhout</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Minerva Hooper</td>
                                        <td>$23,789</td>
                                        <td>Curaçao</td>
                                        <td>Sinaai-Waas</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Sage Rodriguez</td>
                                        <td>$56,142</td>
                                        <td>Netherlands</td>
                                        <td>Baileux</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Philip Chaney</td>
                                        <td>$38,735</td>
                                        <td>Korea, South</td>
                                        <td>Overland Park</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Doris Greene</td>
                                        <td>$63,542</td>
                                        <td>Malawi</td>
                                        <td>Feldkirchen in Kärnten</td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Mason Porter</td>
                                        <td>$78,615</td>
                                        <td>Chile</td>
                                        <td>Gloucester</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection