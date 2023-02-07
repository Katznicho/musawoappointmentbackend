
 <style>
   .navbar-fixed{
     position: sticky !important;
     top:0 !important;
     left: 0 !important;
     z-index: 99;
   }
 </style>
<!-- Navbar -->
<div class="navbar-fixed">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('admin') }}" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Help</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->


      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-sign-out-alt"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('logout') }}"onclick="event.preventDefault();
             document.getElementById('logout-form').submit();"> {{ __('Logout') }}</a>

         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
             @csrf
          </form>
            <!-- Message End -->
          </a>



        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">
            {{$pending_requests_total ?? '' ?? ''}}
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{$pending_requests_total ?? '' }} Notification(s)</span>

          {{-- <div class="dropdown-divider"></div>

          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> New Requests
            <span class="float-right text-muted text-sm">3 mins</span>
          </a> --}}

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> {{$pending_requests_total ?? '' }} client requests
            <span class="float-right text-muted text-sm">10 minutes</span>
          </a>

          {{-- <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a> --}}

          <div class="dropdown-divider"></div>
          <a href="{{route('Requests')}}" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>

    </ul>
  </nav>
  <!-- /.navbar -->


</div>
