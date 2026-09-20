<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name', 'Bontrouver') }} || @yield('title', 'Admin Panel')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta data -->
    <meta name="description" content="Bontrouver Administration Dashboard" />

    @include('admin.includes.favicon')
    @include('admin.includes.styles')
    @stack('custom-style')
</head>

<body>
    <div id="main-wrapper">
        @include('admin.includes.sidebar')
        <div class="content scrollbar d-flex flex-column" id="fullpage"
            style="background-color: #f0f1f7; min-height: 100vh;">
            @include('admin.includes.header')
            <div class="content-body flex-grow-1">
                @yield('content')
            </div>
            @include('admin.includes.footer')
        </div>
    </div>

    @include('admin.includes.scripts')
    @stack('custom-script')
</body>

</html>