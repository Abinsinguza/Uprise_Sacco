<div class="sidebar" data-color="black"  data-image="{{ asset ('img/best.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->

    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="http://www.creative-tim.com" class="simple-text">
                <img src="{{ asset ('img/log.png') }}" alt="UPRISE SACCO Logo"  style="height: 40px;  margin-right: 2px; border-radius: 10px; ">
                {{ __("UPRISE SACCO") }}
            </a>
        </div>
        <ul class="nav">
            <li class="nav-item @if($activePage == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="nc-icon nc-bank"></i>
                    <p>{{ __("Dashboard") }}</p>
                </a>
            </li>
           
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#laravelExamples" @if($activeButton =='laravel') aria-expanded="true" @endif>
                    <i>
                        <img src="{{ asset('light-bootstrap/img/laravel.svg') }}" style="width:25px">
                    </i>
                    <p>
                        {{ __('LOAN MANAGEMENT') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse @if($activeButton =='laravel') show @endif" id="laravelExamples">
                    <ul class="nav">
                        <li class="nav-item @if($activePage == 'loanrequest') active @endif">
                            <a class="nav-link" href="{{route('loanreqdashboard')}}">
                                <i class="nc-icon nc-single-02"></i>
                                <p>{{ __("LOAN REQUESTS") }}</p>
                            </a>
                        </li>
                        <li class="nav-item @if($activePage == 'loan') active @endif">
                            <a class="nav-link" href="{{route('loandashboard')}}">
                                <i class="nc-icon nc-chart-bar-32"></i>
                                <p>{{ __("LOANS IN PROGRESS") }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item @if($activePage == 'deposit') active @endif">
                <a class="nav-link" href="{{route('depositdashboard')}}">
                    <i class="nc-icon nc-money-coins"></i>
                    <p>{{ __("DEPOSITS") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'member') active @endif">
                <a class="nav-link" href="{{route('admindashboard')}}">
                    <i class="nc-icon nc-paper-2"></i>
                    <p>{{ __("Member") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'icons') active @endif">
                <a class="nav-link" href="{{route('page.index', 'icons')}}">
                    <i class="nc-icon nc-atom"></i>
                    <p>{{ __("Icons") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'maps') active @endif">
                <a class="nav-link" href="{{route('page.index', 'maps')}}">
                    <i class="nc-icon nc-pin-3"></i>
                    <p>{{ __("Maps") }}</p>
                </a>
            </li>
            <li class="nav-item @if($activePage == 'notifications') active @endif">
                <a class="nav-link" href="{{route('page.index', 'notifications')}}">
                    <i class="nc-icon nc-bell-55"></i>
                    <p>{{ __("Notifications") }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active bg-danger" href="{{route('page.index', 'upgrade')}}">
                    <i class="nc-icon nc-alien-33"></i>
                    <p>{{ __("Upgrade to PRO") }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
