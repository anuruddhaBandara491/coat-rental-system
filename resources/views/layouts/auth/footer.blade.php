<!-- Include JavaScript files -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>


<?php /* if ($page == 'view-location-movement' || $page == 'view-sos-alert') { ?>
    <!-- Google Maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAasaVcaRWLtVjpF7z7DY0ZOlZojv4vbiM&callback=initMap"
        async></script>
<?php } if ($page == 'view-location-movement') { ?>
<script src="{{asset('assets/js/maps/location-movement.js')}}"></script>
<?php } if ($page == 'view-sos-alert') { ?>
<script src="{{asset('assets/js/maps/sos-alert.js')}}"></script>
<?php } ?>

    <!-- Theme JS -->
<script src="{{asset('assets/js/theme.js')}}"></script>*/
?>
{{--remove above php tag when uncomment below lines--}}

@stack('script')
