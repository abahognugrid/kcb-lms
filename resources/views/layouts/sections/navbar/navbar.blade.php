<style>
    .notification-badge {
        position: absolute;
        top: -5px;
        /* Adjust position as needed */
        right: -5px;
        /* Adjust position as needed */
        background-color: red;
        /* Badge background color */
        color: white;
        /* Text color */
        border-radius: 50%;
        /* Make it circular */
        padding: 5px;
        /* Padding for the badge */
        font-size: 8px;
        /* Font size */
        font-weight: bold;
        /* Bold text */
        min-width: 15px;
        /* Minimum width */
        text-align: center;
        /* Center the text */
        line-height: 1;
        /* Adjust line height */
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        /* Optional: Add a shadow */
    }
</style>
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
    $containerNav = $containerNav ?? 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
    $user = Auth::user();
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{ $containerNav }} navbar navbar-expand-xl {{ $navbarDetached }} align-items-center bg-navbar-theme"
        id="layout-navbar" style="z-index: 1">
@endif
@if (isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme navbar-dark bg-dark"
        id="layout-navbar" style="z-index: 1">
        <div class="{{ $containerNav }}">
@endif

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{ url('/') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])</span>
            <span
                class="app-brand-text demo menu-text fw-bold text-heading">{{ config('variables.templateName') }}</span>
        </a>
    </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
            {{-- <i class="@yield('icon')"></i> --}}
            {{-- &nbsp; --}}
            <span class="display-6 mb-0">@yield('title')</span>
        </div>
    </div>
    <!-- /Search -->
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- User -->
        <livewire:top-bar-user-notification />
        <!--/ User -->
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown ms-3">
            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ asset('assets/img/avatars/generic-user-image.jpg') }}" alt
                        class="w-px-40 h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('assets/img/avatars/generic-user-image.jpg') }}" alt
                                        class="w-px-40 h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">
                                    @auth
                                        {{ Auth::user()->name }}
                                    @endauth
                                </h6>
                                @auth
                                    <small class="text-muted">{{ data_get(Auth::user()->getRoleNames(), 0) }}</small>
                                @endauth
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="/users/{{ Auth::user()->id }}">
                        <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('downloads.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-arrow-down-to-line-icon lucide-arrow-down-to-line">
                            <path d="M12 17V3" />
                            <path d="m6 11 6 6 6-6" />
                            <path d="M19 21H5" />
                        </svg>
                        <span>Downloads</span>
                    </a>
                </li>
                @if (auth()->user()->partner_id)
                    <li>
                        <a class="dropdown-item" href="/partners/{{ Auth::user()->partner_id }}/show">
                            <i class="bx bx-cog bx-md me-3"></i><span>Manage Partner</span>
                        </a>
                    </li>
                @endif

                {{-- <li>
                    <a class="dropdown-item" href="javascript:void(0);">
                        <span class="d-flex align-items-center align-middle">
                            <i class="flex-shrink-0 bx bx-credit-card bx-md me-3"></i><span
                                class="flex-grow-1 align-middle">Billing Plan</span>
                            <span class="flex-shrink-0 badge rounded-pill bg-danger">4</span>
                        </span>
                    </a>
                </li> --}}
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                        <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>

@if (!isset($navbarDetached))
    </div>
@endif
</nav>

<div class="container">
    @if (session()->has('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-4">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="alert alert-primary mt-4">
            {{ session('info') }}
        </div>
    @endif
    @if (count($errors) > 0)
        <div class = "alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
<!-- / Navbar -->
