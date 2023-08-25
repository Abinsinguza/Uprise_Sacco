@extends('layouts.app', ['activePage' => 'table', 'title' => 'UPRISE SACCO Loan Requests', 'navName' => 'Loan Requests', 'activeButton' => 'laravel'])

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
                          
                          
                            @foreach($rates as $rate)
                            <p style="background-color: #527993; color: #ffffff; padding: 10px 20px; border-radius: 8px; max-width: 12%; font-size: 18px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                              Rate: {{$rate->value}}
                          </p>
                            <div>
                              
                              <a href="{{url('rate',$rate->id)}}" class="btn btn-success" data-toggle="modal" data-target="#editModal{{ $rate->id }}">Edit</a>
                              <button class="btn btn-primary" data-toggle="modal" data-target="#requestModal">Approve Loans</button>
                            
                                
                                <!-- edit modal -->
                
                                <div class="modal fade" id="editModal{{ $rate->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                          <h3>Change Interest Rate</h3>
                
                                            <!-- Your form goes here -->
                                            <form method="POST" action="/updateRate">
                                                @csrf
                                                <!-- Add a hidden input to send the member ID -->
                                                <input type="hidden" name="id" value="{{ $rate->id }}">
                                                <label for="constant_value">Constant Value:</label>
                                                <input type="number" name="value" value="{{ $rate->value }}">
                                                <div class="modal-footer">
                                                  <button type="submit" class="btn btn-primary">Update</button>
                                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                                
                                            </form>
                                        </div>
                                    </div>
                                </div> 
                                @endforeach
                
                                   
                            </div>
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
                                    @foreach( $loanrequests as $req)
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
                <div class="modal fade" id="requestModal" tabindex="1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 90%; margin: 1.75rem auto;" role="document">
                        <div class="modal-content "> 
                <div class="customer">
                    <div class="tablep">
                        <section class="table_header">
                          {{-- <h1>Customer's orders</h1> --}}
                          <p>Approve pending loan requests</p>
                        </section>
                        
                              <form method="post" action="/updateApprovals">
                                @csrf
                                <section class="table_body">
                                  <table id="customerTable">
                                    <thead>
                                      <tr>
                                        <th>Member ID</th>
                                        <th>Reference No</th>
                                        <th>Amount(UGX)</th>
                                        <th>Period</th>
                                        <th>Average Performance(%)</th>
                                        <th>Status</th>
                                        <th>Client Choice</th>
                                        <th>Action</th>
                            
                                        
                                      </tr>
                                    </thead>
                                    <tbody>
                                                                    
                              
                              @foreach ($highlightedRequests as $index => $req)
                              <tr class="{{ $loop->index < 3 ? 'light-green' : 'light-red' }}">
                                <td>{{ $req->memberId }}</td>
                                <td>{{$req->referenceNumber }}</td>
                                <td>{{ number_format($req->loan_amount, 2) }}</td>
                                <td>{{ $req->periodInMonths }}</td>
                                <td>{{ $req->avPerformance }}</td>
                                
                                <td><strong>
                                  <p class="choiceText"> you</p>
                                  <input name="approvals[]" type="text" value="{{ $req->approval }}" class="form-control" placeholder="approval">
                                  <input type="hidden" name="referenceNumber_id_{{ $index }}" value="{{ $req->referenceNumber }}">
                              </strong>
                                </strong></td>
                                
                               <td>
                                 
                                  {{ $req->clientChoice }}
                                </td>
                                <td>
                                  <button type="button" class="grantButton" style="background-color: green; color: white;">Grant</button>
                                  <button type="button" class="denyButton" style="background-color: red; color: white;">Deny</button>

                                  {{-- <button type="button" class="grantButton">Grant</button>
                                  <button type="button" class="denyButton">Deny</button> --}}
                                </td>
                            
                              </tr>
                           
                              @endforeach
                            </tbody>
                          </table>
                          
                           
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                    
                          
                
                        </section>

                              </form>

                      </div> 
                </div>
                        </div>
            </div>

            </div>                            
             <script>
              const grantButtons = document.querySelectorAll(".grantButton");
              const denyButtons = document.querySelectorAll(".denyButton");
          
              grantButtons.forEach(grantButton => {
                  grantButton.addEventListener("click", function() {
                      // Find the row containing the clicked button
                      const row = grantButton.closest("tr");
                      
                      // Find the choice text element within the row
                      const choiceText = row.querySelector(".choiceText");
                      
                      // Update the choice text
                      choiceText.textContent = " Grant.";
                      
                      // Update the input value with the choice
                      const approvalInput = row.querySelector("input[name='approvals[]'");
                      approvalInput.value = "Grant";
                      
                      // You can perform additional actions here based on the user's choice.
                  });
              });
          
              denyButtons.forEach(denyButton => {
                  denyButton.addEventListener("click", function() {
                      // Find the row containing the clicked button
                      const row = denyButton.closest("tr");
                      
                      // Find the choice text element within the row
                      const choiceText = row.querySelector(".choiceText");
                      
                      // Update the choice text
                      choiceText.textContent = " Deny.";
                      
                      // Update the input value with the choice
                      const approvalInput = row.querySelector("input[name='approvals[]'");
                      approvalInput.value = "Deny";
                      
                      // You can perform additional actions here based on the user's choice.
                  });
              });
          </script>



                
                                    
                                                                                

                                                                     



            </div>
        </div>
    </div>


      
@endsection