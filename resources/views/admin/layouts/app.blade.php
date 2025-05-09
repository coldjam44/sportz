<!DOCTYPE html>
<html
  lang="{{ config()->get('app.locale') }}"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="{{ config()->get('app.locale') == 'ar' ? 'rtl' : 'ltr' }}"
  data-theme="theme-default"
  data-assets-path="{{ asset('admin/assets') }}/"
  data-template="vertical-menu-template-starter">
  <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @stack('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MQ6DdL+gkOIvyg4ezOJS+r6g7g6UpCq5siCw

    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Sportsz</title>

    <meta name="description" content="" />

    @include('admin.includes.styles')

  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @include('admin.includes.aside')

        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

            @include('admin.includes.header')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
                @yield('content')
            </div>
            <!-- / Content -->


            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <form action="{{ route('admin.logout') }}" method="post" id="logout_form">
        @csrf
    </form>
    @include('admin.includes.scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-c1kX9RXD4y5Rbj6SUgo5xB9dzGCuI9QqSnCtkcu0F0lF5CqtK4Vb9mT6AwDgk6p3" crossorigin="anonymous"></script>

  </body>
</html>
