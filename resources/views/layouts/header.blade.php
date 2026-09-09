<!DOCTYPE html>
<html>

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="icon" href="{{ asset('uploads/settings/favicon.png') }}" type="image/png" />

    <meta name="_token" content="{!! csrf_token() !!}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/bootstrap-datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/themefy_icon/themify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/flaticon.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/fnt.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/toastr.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/select2/css/select2.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/css/jquery-ui.css') }}" />
    {{-- metsimenu --}}
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/metisMenu.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/loade.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/croppie.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/backend_static_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/infix.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/vendors_static_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/preloader.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/solid_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/multiselect/css/jquery.multiselect.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/multiselect/css/custom_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/radio_checkbox.css') }}" />
    <script src="{{ asset('backEnd/vendors/js/jquery-3.6.0.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/backend_design_v2.css') }}">
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/metisMenu.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/loade.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/croppie.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/backend_static_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/infix.css') }}" />

    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/vendors_static_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/preloader.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/solid_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/multiselect/css/jquery.multiselect.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/multiselect/css/custom_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/radio_checkbox.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/backend_design_v2.css') }}">

    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/bootstrap-datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/font_awesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/themefy_icon/themify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/flaticon.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/fnt.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/css/toastr.min.css') }}" />


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">




    @include('components.partials.css')
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* for toastr dynamic start*/
        .toast-success {
            background-color: #4BCF90 !important;
        }

        .toast-message {
            color: #FFFFFF;
        }

        .toast-title {
            color: #FFFFFF;

        }

        .toast {
            color: #FFFFFF;
        }

        .toast-error {
            background-color: #FF6D68 !important;
        }

        .toast-warning {
            background-color: #E09079 !important;
        }

        /* Toast container */
        #toast-container>div {
            width: 450px !important;
            min-height: 80px !important;
            padding: 20px 20px 20px 60px !important;
            font-size: 16px !important;
            border-radius: 8px !important;
        }

        /* Title */
        .toast-title {
            font-size: 18px !important;
            font-weight: 600 !important;
        }

        /* Message */
        .toast-message {
            font-size: 20px !important;
            line-height: 1.8 !important;
        }

        /* Icon */
        #toast-container>.toast {
            background-size: 28px !important;
        }

        /* ---- Modern refresh: top banner / breadcrumb (Section 23) ---- */
        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }


        .modern-row-actions .btn-row-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border_color);
            background: #fff;
            color: #8a90a2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .modern-row-actions .btn-row-action:hover,
        .modern-row-actions .btn-row-action[aria-expanded="true"] {
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            border-color: transparent;
        }

        .modern-row-actions .dropdown-menu {
            border-radius: 10px;
            border: 1px solid var(--border_color);
            box-shadow: 0 8px 24px rgba(20, 20, 43, 0.12);
            padding: 6px;
            min-width: 210px;
        }

        .modern-row-actions .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color .12s ease;
        }

        .modern-row-actions .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.045);
        }

        .modern-row-actions .dropdown-item.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.08);
        }


        .main-title.search-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .main-title.search-card-header .title-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
        }

        .main-title.search-card-header h3 {
            margin: 0;
            padding-bottom: 0;
        }

        .main-title.search-card-header h3::after {
            display: none;
        }

        .search-fields-row {
            row-gap: 18px;
        }

        .primary_input_label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #6c7280;
            margin-bottom: 6px;
        }

        .primary_input_label i {
            color: var(--gradient_1);
            margin-right: 5px;
            width: 14px;
            text-align: center;
        }

        .primary_input_field:focus,
        select.primary_select:focus {
            border-color: var(--gradient_1) !important;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.06) !important;
            outline: none;
        }



        /* new */
        #table_id {
            width: 100%;
            border-radius: 12px;
        }

        .rp-table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }

        .rp-table-wrapper table {
            min-width: 1800px;
            margin-bottom: 0;
        }

        .rp-table-wrapper::-webkit-scrollbar {
            height: 7px;
        }

        .rp-table-wrapper::-webkit-scrollbar-track {
            background: #f1f2f6;
            border-radius: 10px;
        }

        .rp-table-wrapper::-webkit-scrollbar-thumb {
            background: #c5c8d2;
            border-radius: 10px;
        }

        .rp-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #999eac;
        }

        /* Keep table header visible while scrolling vertically */
        .rp-table-wrapper thead th {
            white-space: nowrap;
        }

        /* ========================= Pagination ========================= */
        .rp-pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #eceef3;
        }

        .rp-pagination-info {
            color: #7b8191;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }

        .rp-pagination-info strong {
            color: #343846;
            font-weight: 700;
        }

        /* Laravel Bootstrap pagination */
        .rp-pagination-wrapper .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            flex-wrap: wrap;
        }

        .rp-pagination-wrapper .page-item {
            margin: 0;
        }

        .rp-pagination-wrapper .page-link {
            min-width: 36px;
            height: 36px;
            padding: 0 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7ed !important;
            border-radius: 9px !important;
            background: #fff;
            color: #555b6d;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .rp-pagination-wrapper .page-link:hover {
            background: #f6f6ff;
            color: var(--gradient_1);
            border-color: var(--gradient_1) !important;
            transform: translateY(-1px);
        }

        .rp-pagination-wrapper .page-item.active .page-link {
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2)) !important;
            border-color: transparent !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(81, 80, 233, 0.22);
        }

        .rp-pagination-wrapper .page-item.disabled .page-link {
            background: #f7f7f9;
            color: #b5b8c2;
            border-color: #eceef3 !important;
            cursor: not-allowed;
            transform: none;
        }

        /* Previous / Next */
        .rp-pagination-wrapper .page-item:first-child .page-link,
        .rp-pagination-wrapper .page-item:last-child .page-link {
            padding-left: 14px;
            padding-right: 14px;
        }

        .white-box .main-title h3 {
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .white-box .main-title h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 42px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }

        /* Mobile */
        @media (max-width: 767px) {
            .rp-pagination-wrapper {
                justify-content: center;
                flex-direction: column;
                align-items: center;
            }

            .rp-pagination-info {
                text-align: center;
            }

            .rp-pagination-wrapper .pagination {
                justify-content: center;
            }
        }

        .dataTables_scrollBody {
            min-height: 400px;
        }

        .primary_select_wrapper .nice-select .list,
        .nice-select .list {
            max-height: 450px !important;
            overflow-y: auto !important;
        }

        /* Scrollbar */
        .nice-select .list::-webkit-scrollbar {
            width: 6px;
        }

        .nice-select .list::-webkit-scrollbar-track {
            background: #f1f2f6;
            border-radius: 10px;
        }

        .nice-select .list::-webkit-scrollbar-thumb {
            background: var(--gradient_1);
            border-radius: 10px;
        }

        .nice-select .list::-webkit-scrollbar-thumb:hover {
            background: var(--gradient_2);
        }

        /* Firefox */
        ::-webkit-scrollbar {
            width: 12px;
            /* vertical scrollbar */
            height: 12px;
            /* horizontal scrollbar */
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient_1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gradient_2);
        }

        * {
            scrollbar-width: auto;
            scrollbar-color: var(--gradient_1) transparent;
        }
    </style>
    <style>
        .dt-toolbar {
            padding-bottom: 4px;
        }

        .dt-toolbar-title h3 {
            font-weight: 700;
            margin: 0;
            white-space: nowrap;
        }


        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            margin: 0;
            font-weight: 500;
            font-size: 13px;
            color: #6b7280;
        }

        .dataTables_wrapper .dataTables_length select {
            margin: 0 6px;
            border: 1px solid var(--border_color);
            border-radius: 8px;
            padding: 6px 28px 6px 12px;
            background-color: #fff;
            font-weight: 600;
            color: #34395e;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: var(--gradient_1);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--gradient_1) 15%, transparent);
        }

        .dataTables_wrapper .dataTables_filter input[type="search"] {
            margin-left: 8px;
            border: 1px solid var(--border_color);
            border-radius: 999px;
            padding: 7px 16px;
            min-width: 220px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .dataTables_wrapper .dataTables_filter input[type="search"]:focus {
            outline: none;
            border-color: var(--gradient_1);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--gradient_1) 15%, transparent);
        }

        .dt-buttons-wrap .dt-buttons {
            display: flex;
            gap: 8px;
            border: 0px
        }

        .dt-buttons-wrap .dt-button {
            border: none !important;
            border-radius: 8px !important;
            min-width: 38px;
            height: 38px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2)) !important;
            color: #fff !important;
            box-shadow: 0 2px 8px color-mix(in srgb, var(--gradient_1) 35%, transparent);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .dt-buttons-wrap .dt-button:hover,
        .dt-buttons-wrap .dt-button:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px color-mix(in srgb, var(--gradient_1) 45%, transparent);
            color: #fff !important;
        }

        .dt-buttons-wrap .dt-button span,
        .dt-buttons-wrap .dt-button i {
            color: inherit;
        }

        div.dt-button-collection {
            border-radius: 10px;
            border: 1px solid var(--border_color);
            box-shadow: 0 8px 24px rgba(20, 20, 43, 0.12);
            padding: 6px;
            background: #fff;
        }

        div.dt-button-collection .dt-button {
            width: auto;
            height: auto;
            min-width: 0;
            background: none !important;
            color: #34395e !important;
            box-shadow: none;
            justify-content: flex-start;
            padding: 8px 12px !important;
            border-radius: 6px !important;
        }

        div.dt-button-collection .dt-button:hover {
            background: color-mix(in srgb, var(--gradient_1) 10%, #fff) !important;
            transform: none;
            box-shadow: none;
        }

        div.dt-button-collection .dt-button.dt-button-active {
            background: color-mix(in srgb, var(--gradient_1) 14%, #fff) !important;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_info {
            color: #8a90a2;
            font-size: 13px;
        }


        @media (max-width: 767px) {


            .dt-toolbar-controls {
                width: 100%;
                justify-content: space-between !important;
            }

            .dataTables_wrapper .dataTables_filter input[type="search"] {
                min-width: 0;
                width: 100%;
                margin-left: 0;
                margin-top: 6px;
            }
        }

        #table_id tbody tr {
            transition: background-color .12s ease;
            1
        }

        #table_id tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #table_id tbody td {
            vertical-align: middle;
        }

        .text-muted {
            color: #9aa1ac !important;
        }

        .modern-row-actions .btn-row-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border_color);
            background: #fff;
            color: #8a90a2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3 {
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .white-box .main-title h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 42px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }

        .primary_input_field,
        select.primary_select {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #table_id tbody tr {
            transition: background-color .12s ease;
        }

        #table_id tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #table_id tbody td {
            vertical-align: middle;
        }

        .text-muted {
            color: #9aa1ac !important;
        }

        .modern-row-actions .btn-row-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border_color);
            background: #fff;
            color: #8a90a2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
    </style>
    <style>
        :root {
            --background: #FAFAFA;
            --base_color: #415094;
            --sidebar_bg: #280e55;
            --gradient_1: #7c32ff;
            --gradient_2: #7c32ff;
            --gradient_3: #7c32ff;
            --text-color: #828bb2;
            --scroll_color: #828bb2;
            --text_white: #FFFFFF;
            --bg_white: #FFFFFF;
            --text_black: #000000;
            --bg_black: #000000;
            --border_color: #EFF2F8;
            --sidebar_active: #ffffff;
            --sidebar_hover: #ffffff;
            --primary-color: #7c32ff;
            --card-gradient-cyan: linear-gradient(to right, #06b6d4, #22d3ee);
            --card-gradient-violet: linear-gradient(to right, #8b5cf6, #a78bfa);
            --card-gradient-blue: linear-gradient(to right, #3b82f6, #60a5fa);
            --card-gradient-fuchsia: linear-gradient(to right, #d946ef, #e879f9);
            --card-gradient-cyan-hover: linear-gradient(to right, #06b6d4, #22d3ee);
            --card-gradient-violet-hover: linear-gradient(to right, #8b5cf6, #a78bfa);
            --card-gradient-blue-hover: linear-gradient(to right, #3b82f6, #60a5fa);
            --card-gradient-fuchsia-hover: linear-gradient(to right, #d946ef, #e879f9);

            --sidebar-section: #636674;
            --sidebar-nav-link: #9a9cae;
            --transparent: transparent;

            --input_bg: #FFFFFF;
            --success: #4BCF90;
            --danger: #FF6D68;
            --warning: #E09079;
            --red: #d33333;
            --black: #000000;
            --link-hover: #161931;
            --notification_title: rgb(14, 23, 38);
            --notification_time: #3b3f5c99;
            --modalLink_color: #2f2f3be6;
            --profile_text_hover: #2d3253;
            --table_header: rgb(246, 248, 250);
            --box_shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.1) 0px 1px 2px -1px !important;
        }
    </style>

    @stack('css')
    <script>
        window._locale = '{{ App::getLocale() }}';
        window._translations = {!! json_encode(
            \Cache::rememberForever('translations', function () {
                $files = glob(resource_path('lang/' . App::getLocale() . '/*.php'));
                $translations = collect($files)->flatMap(function ($file) {
                        return [basename($file, '.php') => require $file];
                    })->toArray();
        
                $jsonTranslations = file_exists(resource_path('lang/' . App::getLocale() . '.json'))
                    ? file_get_contents(resource_path('lang/' . App::getLocale() . '.json'))
                    : '{}';
        
                return ['php' => $translations, 'json' => $jsonTranslations];
            }),
        ) !!};
    </script>
</head>



<body class="admin">
    {{-- @include('backEnd.preloader') --}}


    {{-- <div class="preloader" dir="ltr">
        <div class="circle circle1"></div>
    </div> --}}

    {{-- <div class="preloader" dir="ltr">
        <div class="circle circle2"></div>
    </div> --}}

    <div class="preloader" dir="ltr">
        <div class="dot dot3"></div>
        <div class="dot dot3 dot31"></div>
    </div>

    {{-- <div class="preloader" dir="ltr">
        <div class="circle circle3 c31"></div>
        <div class="circle circle3 c32"></div>
    </div> --}}
    <input type="hidden" id="demoMode" value="{{ config('app.app_sync') }}">

    <input type="text" hidden value="sss" id="logo_img">
    <input type="text" hidden value="abx" id="logo_title">

    <div class="main-wrapper" style="min-height: 600px">
        <input type="hidden" id="nodata" value="@lang('common.no_data_available_in_table')">
        <!-- Sidebar  -->

        <!-- sidebar part here -->
        @include('layouts.side-menu')
        <!-- sidebar part end -->
        {{-- ------------------ --}}

        <!-- Page Content  -->
        <div id="main-content">
            <input type="hidden" name="url" id="url" value="{{ url('/') }}">
            @include('layouts.top-menu')

            {{-- </div>
    </div> --}}

            <script>
                $(window).on('resize', function() {
                    var win = $(this);
                    if (win.width() <= 991) {
                        $('#sidebar.sidebar').removeClass('mini_sidebar');
                        $('#main-content').removeClass('mini_main_content');
                        $('.footer-area').removeClass('mini_main_content');
                    }
                });

                // slider
                $(document).ready(function() {
                    var sections = [];
                    $('.menu_seperator').each(function() {
                        sections.push($(this).data('section'));
                    });

                    jQuery.each(sections, function(index, section) {
                        if ($('.' + section).length == 0) {
                            $('#seperator_' + section).addClass('d-none');
                        } else {
                            $('#seperator_' + section).removeClass('d-none');
                        }
                    });
                })






                $(document).ready(function() {
                    var sections = [];
                    $('.menu_seperator').each(function() {
                        sections.push($(this).data('section'));
                    });

                    jQuery.each(sections, function(index, section) {
                        if ($('.' + section).length == 0) {
                            $('#seperator_' + section).addClass('d-none');
                        } else {
                            $('#seperator_' + section).removeClass('d-none');
                        }
                    });
                })
            </script>

</body>

</html>
