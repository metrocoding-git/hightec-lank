@push('css')
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/bootstrap-datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/bootstrap-datetimepicker.min.css') }}" />
@endpush

{{-- @push('script')
    <script src="{{ asset('backEnd/') }}/vendors/js/bootstrap_datetimepicker.min.js"></script>
    <script src="{{ asset('backEnd/') }}/vendors/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        (function($) {
            $.fn.datepicker.dates[_locale] = new Object({
                "days": {!! json_encode(__('calender.days')) !!},
                "daysShort": {!! json_encode(__('calender.daysShort')) !!},
                "daysMin": {!! json_encode(__('calender.daysMin')) !!},
                "months": {!! json_encode(__('calender.months')) !!},
                "monthsShort": {!! json_encode(__('calender.monthsShort')) !!},
                "today": {!! json_encode(__('calender.today')) !!},
                "clear": {!! json_encode(__('calender.clear')) !!}
            })
        }(jQuery));

        $("#search-icon").on("click", function() {
            $("#search").focus();
        });

        $("#start-date-icon").on("click", function() {
            $("#startDate").focus();
        });

        $("#end-date-icon").on("click", function() {
            $("#endDate").focus();
        });

        // Initialize datepicker for date_of_birth as inline
        $("#date_of_birth").datepicker({
            autoclose: true,
            format: "yyyy-mm-dd", // Use YYYY-MM-DD for consistency with HTML5 date input
            container: '#date_of_birth_container', // Specify a container for inline display
            orientation: "bottom auto",
            todayHighlight: true,
            // Remove unwanted classes by not using the dropdown functionality
            // and potentially adding custom CSS if needed for further refinement.
        }).on('changeDate', function(e) {
            // Update the input field when a date is selected from the inline calendar
            $('#date_of_birth').val(e.format('yyyy-mm-dd'));
            $('#date_of_birth').trigger('change'); // Trigger change to update age display
        });

        // For admission_date, since it's readonly, no datepicker initialization is needed.
        // The native HTML5 date input will handle its display.

        if ($)
            $(".primary_input_field.datetime").datetimepicker({
                format: "YYYY-MM-DD H:mm",
            });
    </script>
@endpush --}}
