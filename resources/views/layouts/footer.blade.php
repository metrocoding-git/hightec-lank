<div class="has-modal modal fade" id="showDetaildModal">
    <div class="modal-dialog modal-dialog-centered" id="modalSize">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="showDetaildModalTile">@lang('system_settings.new_client_information')</h4>
                <button type="button" class="close icons" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="showDetaildModalBody">

            </div>
        </div>
    </div>
</div>
<!--  Start Modal Area -->
<div class="modal fade invoice-details" id="showDetaildModalInvoice">
    <div class="modal-dialog large-modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.add_invoice')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="showDetaildModalBodyInvoice">
            </div>
        </div>
    </div>
</div>
<!--================Footer Area ================= -->
<footer class="footer-area new-footer mt-2" >
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">

                <p>Copyright © 2026 All rights reserved | This application is made with by MetroCoding</p>

            </div>
        </div>
    </div>
</footer>

<!-- ================End Footer Area ================= -->
<style>

    .new-footer {
        position: flex;
        /* z-index: 1; */
    }
</style>
<script>
    // window.jsLang = function(key, replace) {
    //     let translation = true

    //     let json_file = $.parseJSON(window._translations[window._locale]['json'])
    //     translation = json_file[key] ?
    //         json_file[key] :
    //         key


    //     $.each(replace, (value, key) => {
    //         translation = translation.replace(':' + key, value)
    //     })

    //     return translation
    // }
    // window.trans = function(key, replace) {
    //     let translation = true

    //     let json_file = $.parseJSON(window._translations[window._locale]['json'])
    //     translation = json_file[key] ?
    //         json_file[key] :
    //         key


    //     $.each(replace, (value, key) => {
    //         translation = translation.replace(':' + key, value)
    //     })
    //     return translation
    // }
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="{{ asset('backEnd/vendors/js/jquery-3.6.0.min.js') }}"></script>

<script src="{{ asset('backEnd/') }}/vendors/js/jquery-ui.js"></script>
<script src="{{ asset('backEnd/') }}/vendors/js/popper.js"></script>

<script src="{{ asset('backEnd/assets/js/metisMenu.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('backEnd/assets/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('backEnd/') }}/vendors/js/nice-select.min.js"></script>

<script src="{{ asset('backEnd/') }}/vendors/js/jquery.magnific-popup.min.js"></script>

<script src="{{ asset('backEnd/') }}/vendors/js/raphael-min.js"></script>
<script src="{{ asset('backEnd/') }}/vendors/js/morris.min.js"></script>
<script type="text/javascript" src="{{ asset('backEnd/') }}/vendors/js/toastr.min.js"></script>
<script type="text/javascript" src="{{ asset('backEnd/') }}/vendors/js/moment.min.js"></script>


<script type="text/javascript" src="{{ asset('backEnd/') }}/js/jquery.validate.min.js"></script>


<script src="{{ asset('backEnd/') }}/js/main.js"></script>

<script src="{{ asset('backEnd/') }}/js/custom.js"></script>
@if (file_exists(public_path('js/registration_custom.js')))
    <script src="{{ asset('js/registration_custom.js') }}"></script>
@endif
<script src="{{ asset('backEnd/') }}/js/developer.js"></script>
@if (function_exists('moduleStatusCheck') && moduleStatusCheck('Wallet') && file_exists(public_path('Modules/Wallet/Resources/assets/js/wallet.js')))
    <script src="{{ url('Modules/Wallet/Resources/assets/js/wallet.js') }}"></script>
@endif


<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    @if (session()->has('message-success'))
        toastr.success("{{ session('message-success') }}");
    @endif

    @if (session()->has('message-danger'))
        toastr.error("{{ session('message-danger') }}");
    @endif

    @if (session()->has('message-success-delete'))
        toastr.success("{{ session('message-success-delete') }}");
    @endif

    @if (session()->has('message-danger-delete'))
        toastr.error("{{ session('message-danger-delete') }}");
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif


    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "500"
    };
</script>

{{-- xlsx --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>



<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $('.close_modal').on('click', function() {
        $('.custom_notification').removeClass('open_notification');
    });
    $('.notification_icon').on('click', function() {
        $('.custom_notification').addClass('open_notification');
    });
    $(document).click(function(event) {
        if (!$(event.target).closest(".custom_notification").length) {
            $("body").find(".custom_notification").removeClass("open_notification");
        }
    });

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : (event.keyCode);
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<script src="{{ asset('backEnd/') }}/js/search.js"></script>


<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('chat/js/custom.js') }}"></script>
{{-- ================= SESSION EXPIRY HANDLING =================
     1) Any AJAX / fetch call that comes back 401 or 419 -> go to login.
     2) Auto logout: after SESSION_LIFETIME minutes with no activity,
        the page goes to the login page by itself (no stale screen). --}}
<script>
    (function () {
        var LOGIN_URL = @json(route('login'));
        var LIFETIME_MS = {{ (int) config('session.lifetime', 120) }} * 60 * 1000;
        var leaving = false;

        function goToLogin() {
            if (leaving) return;
            leaving = true;
            window.location.href = LOGIN_URL;
        }

        // jQuery AJAX (DataTables, dropdowns, modals, bulk actions)
        if (window.jQuery) {
            jQuery(document).ajaxError(function (event, xhr) {
                if (xhr && (xhr.status === 401 || xhr.status === 419)) goToLogin();
            });
            jQuery(document).ajaxComplete(resetTimer);
        }

        // fetch() (React pages: mark entry, marks popup, templates)
        if (window.fetch) {
            var originalFetch = window.fetch;
            window.fetch = function () {
                return originalFetch.apply(this, arguments).then(function (res) {
                    var sentToLogin = res.redirected && res.url.split('?')[0] === LOGIN_URL;
                    if (res.status === 401 || res.status === 419 || sentToLogin) goToLogin();
                    resetTimer();
                    return res;
                });
            };
        }

        // Auto logout timer (server session ends after SESSION_LIFETIME idle minutes)
        var timer = null;
        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(goToLogin, LIFETIME_MS + 5000);
        }
        resetTimer();

        // Page restored from Back/Forward cache -> reload so the server can check the session
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) window.location.reload();
        });
    })();
</script>
@yield('script')
@stack('script')
@stack('scripts')
