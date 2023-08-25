@extends('layouts.app', ['activePage' => 'table', 'title' => 'UPRISE SACCO', 'navName' => 'UPRISE SACCO members', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header">
                           
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            @if (session('success'))
                            <div class="alert alert-success">
                                  {{ session ('success')}}
                            </div>
                            @endif
                          
                        
                        </div>      
                        <section class="attendance">
                             <!-- Register Member Button -->
                             <button class="btn btn-primary" data-toggle="modal" data-target="#registerModal">Register a Member</button>
                             <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Add CSV File</button>
                              <!-- Button to trigger the modal -->
                              <button class="btn btn-primary" data-toggle="modal" data-target="#searchMemberModal">Search</button>
                            <div class="attendance-list">
                                <p style="font-weight: bold; color:rgb(143, 49, 49); text-align: right;"> Total Contributions:UGX,{{number_format($totalAmount)}}</p>
                                <div style="margin-bottom: 20px; text-align: left;"> 
                                    <a href="{{ route('active.members') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Active Members</a>
                                    <a href="{{ route('new.members') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">New Members</a>
                                    <a href="{{ route('large-loan-payments-deposited') }}" style="margin-right: 15px; color: #007bff; text-decoration: none; font-weight: bold;">Large Transactions</a>
                                    <a href="{{ route('loan-payments-deposited') }}" style="color: #007bff; text-decoration: none; font-weight: bold;">All Transactions</a>
                                </div>

                              <table class="tablek">
                                <thead>
                                  <tr>  
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Phone Number</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                    
                                  </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                    <tr>
                                        <td>{{$member->memberId}}</td>
                                        <td>{{$member->name}}</td>
                                        <td>{{$member->username}}</td>
                                        <td>{{$member->password}}</td>
                                        <td>{{$member->phoneNumber}}</td>
                                        <td>{{number_format ($member->balance, 2)}}</td>
                                        <td> 
                                            {{-- <button onclick="window.location='{{url('edit',$member->id)}}'" class="btn btn-success">Edit</button>
                                            
                                            <a href="{{url('edit',$member->id)}}" class="btn btn-success">Edit</a> --}}
                                            {{-- <button wire.click="edit({{$member->id}})" class="btn btn-success edit-button" data-toggle="modal" data-target="#editModal" >Edit</button></button> --}}
                                            <a href="{{url('member',$member->id)}}" class="btn btn-success" data-toggle="modal" data-target="#editModal{{ $member->memberId }}">Edit</a>
                                            <a href="" class="btn btn-success"data-toggle="modal" data-target="#viewModal{{ $member->memberId }}"><i class="bi bi-eye">view</i></a>
                                        </td>
                                    </tr>
                                  
                                                                                 <!-- edit modal -->

                                                                                 <div class="modal fade" id="editModal{{ $member->memberId }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                                                    <div class="modal-dialog" role="document">
                                                                                        <div class="modal-content">
                                                                                            <!-- Your form goes here -->
                                                                                            <form action="{{ route('updateMember') }}" method="POST">
                                                                                                @csrf
                                                                                                <!-- Add a hidden input to send the member ID -->
                                                                                                <input type="hidden" name="id" value="{{ $member->id }}">
                                                                                                <h2>"{{ $member->memberId }}"</h2>
                                                                                                <!-- Other form fields -->
                                                                                                <!-- ... -->
                                                                                                <div class="form-group">
                                                                                                    <input name="email" type="email" value="{{ $member->email }}" class="form-control" placeholder="Email">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <input name="name" type="text" value="{{ $member->name }}" class="form-control" placeholder="Name">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <input name="username" type="text" value="{{ $member->username }}" class="form-control" placeholder="Username">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <input name="password" type="password" value="{{ $member->password }}" class="form-control" placeholder="Password">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <input name="phoneNumber" type="text" value="{{ $member->phoneNumber }}" class="form-control" placeholder="Phone Number">
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <input name="contributionPerMonth" type="text" value="{{ $member->contributionPerMonth }}" class="form-control" placeholder="Contribution Per Month">
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="submit" class="btn btn-primary">UPDATE</button>
                                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                  </div>
                                                                                                
                                                                                
                                                                                               
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                              
                                    
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal{{ $member->memberId }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h3 style="color: rgb(90, 90, 245)" class="modal-title" id="modalViewLabel{{ $member->memberId }}">Member Information</h3>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                            </button>
                                          </div>
                                          <div class="modal-body">
                                            <p><strong>Member ID:</strong> {{ $member->memberId }}</p>
                                            <p><strong>Email:</strong> {{ $member->email }}</p>
                                            <p><strong>Name:</strong> {{ $member->name }}</p>
                                            <p><strong>Username:</strong> {{ $member->username }}</p>
                                            <p><strong>Password:</strong> {{ $member->password }}</p>
                                            <p><strong>Phone Number:</strong> {{ $member->phoneNumber }}</p>
                                            <p><strong>Balance: UGX,</strong> {{  number_format ($member->balance, 2) }}</p>
                                            <p><strong>Contribution Start:</strong> {{ $member->contributionStart }}</p>
                                            <p><strong>Contribution Per Month:UGX,</strong> {{  number_format($member->contributionPerMonth , 2)}}</p>
                                            <p><strong>Months Cleared:</strong> {{ $member->monthsCleared }}</p>
                                            <p><strong>Performance:</strong> {{ $member->performance }}%</p>
                                            <p><strong>Created At:</strong> {{ $member->created_at }}</p>
                                            <p><strong>Updated At:</strong> {{ $member->updated_at }}</p>
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
                          </section>
                        

                    </div>
                    
                </div>
                <div class="col-md-12">
                    <div class="card card-plain table-plain-bg">
                        <div class="card-header">
                      
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <div class="container mt-5">
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
                                                                                <form action="{{ url('/uploadMember') }}" method="post" enctype="multipart/form-data">
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


                                <!-- Register Member Modal -->
                                <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="registerModalLabel">Register Member</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <form action="/registerMember" method="POST">
                                                    @csrf
                                                    <div class="form-group">
                                                        <input name="email" type="email" class="form-control" placeholder="Email">
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="name" type="text" class="form-control" placeholder="Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="username" type="text" class="form-control" placeholder="Username">
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="password" type="password" class="form-control" placeholder="Password">
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="phoneNumber" type="text" class="form-control" placeholder="Phone Number">
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="contributionStart" type="date" class="form-control" placeholder="Select Date">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contributionPerMonth">Select Contribution Per Month</label>
                                                        <select name="contributionPerMonth" id="contributionPerMonth" class="form-control">
                                                            <option value="50000">50,000</option>
                                                            <option value="100000">100,000</option>
                                                            <option value="150000">150,000</option>
                                                            <option value="200000">200,000</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary">Register</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                                
                                
                                
                                              <!-- Search Modal -->
                                <div class="modal fade" id="searchMemberModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="searchModalLabel">Search Member</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('search.member') }}" method="GET" class="form-inline">
                                                    @csrf
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
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
                 
                 

                        </div>
                    </div>
                </div>

@endsection
