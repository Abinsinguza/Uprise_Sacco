<div class="modal-body">
    <form action="{{ route('updateMember') }}" method="POST">
        @csrf
        <!-- Add a hidden input to send the member ID -->
        <input type="hidden" name="id" value="{{ $member->id }}">
            <h2>"{{$member->memberId}}"</h2>
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

        <button type="submit" class="btn btn-primary">UPDATE</button>
    </form>
</div>


@extends('layouts.app', ['activePage' => 'table', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
                                <!-- Edit Member Modal -->
                                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit</h5>
                                                {{-- <a href="{{url('edit',$member->id)}}" data-dismiss="modal" class="close btn btn-success" aria-label="Close" >
                                                     <span aria-hidden="true">&times;</span>
                                                    </a> --}}
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('updateMember') }}" method="POST">
                                                    @csrf
                                                    <!-- Add a hidden input to send the member ID -->
                                                    <input type="hidden" name="id" value="{{ $member->id }}">
                                                        <h2>"{{$member->memberId}}"</h2>
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
                                            
                                                    <button type="submit" class="btn btn-primary">UPDATE</button>
                                                </form>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endsection


