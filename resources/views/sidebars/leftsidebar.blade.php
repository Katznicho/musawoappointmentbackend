<style>
    .sideBar-color{
        background-color: #28A745 !important;
    }
</style>
<!-- Main Sidebar Container -->
<div class="sideBar-color">
    <aside class="main-sidebar sidebar-dark-primary elevation-4 sideBar-color">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Adfa Medicare Services</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">
                    {{ Auth::user()->name }}
                    </a>
                </div>
            </div>


            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                   with font-awesome or any other icon font library -->
                    <!-- @role('Super Admin') -->
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('healthworkers') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manage Health Workers</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('clients') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manage Users</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('Requests') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manage Requests</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ url('labServices') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laboratory Services</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('labRequest') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lab Service Requests</p>
                                </a>
                            </li>
                            {{-- payments --}} <li class="nav-item">
                                <a href="{{ route('payments.index') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payments</p>
                                </a>
                            </li>
                            {{-- payments --}}

                            <li class="nav-item">
                                <a href="{{ url('activityLogs') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Activity Logs</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- @endrole -->



                    <!--third party-->

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

</div>


