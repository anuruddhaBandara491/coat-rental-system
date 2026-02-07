<!DOCTYPE html>

<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ url('assets') . '/' }}"
    data-template="vertical-menu-template"
>

@include('layouts.auth.header')

<body>
<!-- Content -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- / Content -->
    </div>
</div>
@include('layouts.auth.footer')
</body>
</html>
