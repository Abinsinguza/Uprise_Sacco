<footer class="footer">
    <div class="container @auth-fluid @endauth">
        <nav>
            <ul class="footer-menu">
                <li>
                    <a href="{{route('depositdashboard')}}" class="nav-link" target="_blank">{{ __('Deposit') }}</a>
                </li>
                <li>
                    <a href="{{route('loandashboard')}}" class="nav-link" target="_blank">{{ __('Accepted loans') }}</a>
                </li>
                <li>
                    <a href="{{route('page.index', 'notifications')}}" class="nav-link" target="_blank">{{ __('Notifications') }}</a>
                </li>
                <li>
                    <a href="{{route('profile.edit') }}" class="nav-link" target="_blank">{{ __('Settings') }}</a>
                </li>
            </ul>
            <p class="copyright text-center">
                © <script>document.write(new Date().getFullYear())</script> UPRISE SACCO. All rights reserved.
                {{-- ©
                <script>
                    document.write(new Date().getFullYear())
                </script>
                <a href="http://www.creative-tim.com">{{ __('Creative Tim') }}</a> &amp; <a href="https://www.updivision.com">{{ __('Updivision') }}</a> {{ __(', made with love for a better web') }} --}}
            </p>
        </nav>
    </div>
</footer>