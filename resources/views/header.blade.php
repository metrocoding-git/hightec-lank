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
    <link rel="stylesheet" href="{{asset('backEnd/assets/css/croppie.css')}}" />
     
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/backend_static_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/css/infix.css') }}" />
    
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/vendors_static_style.css') }}" />
    <link rel="stylesheet" href="{{asset('backEnd/assets/css/preloader.css')}}" />
    <link rel="stylesheet" href="{{asset('backEnd/assets/css/solid_style.css')}}" />
    <link rel="stylesheet" href="{{asset('backEnd/multiselect/css/jquery.multiselect.css')}}" />
    <link rel="stylesheet" href="{{asset('backEnd/multiselect/css/custom_style.css')}}" />
    <link rel="stylesheet" href="{{asset('backEnd/assets/css/radio_checkbox.css')}}" />
    
    <link rel="stylesheet" href="{{asset('css/backend_design_v2.css')}}">   
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('backEnd/vendors/js/jquery-3.2.1.min.js') }}" />
    <link rel="stylesheet" href="{{ asset('backEnd/assets/vendors/js/jquery-3.6.0.min.js') }}" />

    <link rel="stylesheet" href="{{ asset('css/backend_design_v2.css') }}">
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
    </style>
    <style>
        :root {
            --background: #FAFAFA;
            --base_color: #415094;
            --sidebar_bg: #0d0e12;
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

</head>



<body class="admin">
    {{-- @include('backEnd.preloader') --}}

    <input type="hidden" id="demoMode" value="{{ config('app.app_sync') }}">

    <input type="text" hidden value="sss" id="logo_img">
    <input type="text" hidden value="abx" id="logo_title">

    <div class="main-wrapper" style="min-height: 600px">
        <input type="hidden" id="nodata" value="@lang('common.no_data_available_in_table')">
        <!-- Sidebar  -->

        <!-- sidebar part here -->
        <nav id="sidebar" class="sidebar">

            <div class="sidebar-header update_sidebar">

                <a href="{{ url('/') }}" id="admin-dashboard">

                    <a href="{{ url('/') }}" id="admin-dashboard">

                        <img src="{{ asset('uploads/settings/logo.png') }}" alt="logo">

                    </a>
                </a>

                <a id="close_sidebar" class="d-lg-none">
                    <i class="ti-close"></i>
                </a>

            </div>
            <ul class="sidebar_menu list-unstyled" id="sidebar_menu">


                <span class="menu_seperator" id="seperator_administration_section" data-section="administration_section">Dashboard
                    Section
                </span>



                <li class="administration_section mm-active">
                    <a href="javascript:void(0)" class="has-arrow active" aria-expanded="true">
                        <div class="nav_icon_small">
                            <span class="fas fa-th"></span>
                        </div>
                        <div class="nav_title">
                            <span>Dashboard</span>
                        </div>
                    </a>
                    <ul class="mm-collapse mm-show">
                        <li>
                            <a href="Dashboard1" class="active a">
                                Dashboard 1
                            </a>
                        </li>
                        <li>
                            <a href="Dashboard2" class="">
                                Dashboard 2
                            </a>
                        </li>
                        <li>
                            <a href="Dashboard3" class="">
                                Dashboard 3
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="administration_section">
                    <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                        <div class="nav_icon_small">
                            <span class="fas fa-bars"></span>
                        </div>
                        <div class="nav_title">
                            <span>Dashboard</span>
                        </div>
                    </a>
                    <ul class="mm-collapse">
                        <li>
                            <a href="dashboard1" class="">
                                Dashboard 1
                            </a>
                        </li>
                        <li>
                            <a href="Dashboard2" class="">
                                Dashboard 2
                            </a>
                        </li>
                        <li>
                            <a href="Dashboard3" class="">
                                Dashboard 3
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- <span class="menu_seperator">ijiiik</span>

                <li class="mm-active">

                    @if (($item->subModule->count() > 0 && $item->permissionInfo->route != 'dashboard') || $item->permissionInfo->relate_to_child == 1)
                        <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                        @else
                            <a href="{{ validRouteUrl($item->permissionInfo->route) }}">
                    @endif
                    <div class="nav_icon_small">
                        <span class="{{ $item->permissionInfo->icon }}"></span>
                    </div>
                    <div class="nav_title">
                        <span>{{ __($item->permissionInfo->lang_name ?? $item->permissionInfo->name) }}</span>
                        @if (config('app.app_sync') && $item->permissionInfo->module && in_array($item->permissionInfo->module, $paid_modules))
                            @if (config('app.app_sync'))
                                <span class="demo_addons">Addon</span>
                            @endif
                        @endif
                    </div>
                    </a>
                    <ul class="mm-collapse">
                        @if (@$item->subModule)
                            @foreach (@$item->subModule as $key => $sub)
                                @if ($sub->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && sidebarPermission($sub->permissionInfo))
                                    @foreach ($childrens as $children)
                                        @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                            @continue
                                        @endif

                                        <li>
                                            <a href="{{ validRouteUrl($sub->permissionInfo->route, $children->id) }}"
                                                class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">

                                                {{ __($sub->permissionInfo->lang_name) }} -
                                                {{ $children->full_name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    @if (sidebarPermission($sub->permissionInfo))
                                        <li>
                                            <a href="{{ validRouteUrl($sub->permissionInfo->route) }}"
                                                class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">

                                                {{ __($sub->permissionInfo->lang_name ?? $sub->permissionInfo->name) }}
                                            </a>

                                        </li>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                        @if ($item->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && count($item->subModule) == 0 && sidebarPermission($item->permissionInfo))
                            @foreach ($childrens as $children)
                                @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                    @continue
                                @endif

                                <li>
                                    <a href="{{ validRouteUrl($item->permissionInfo->route, $children->id) }}"
                                        class="{{ spn_active_link(subModuleRoute($item), 'active') }}">

                                        {{ __($item->permissionInfo->lang_name) }} -
                                        {{ $children->full_name }} </a>

                                </li>
                            @endforeach
                        @endif

                    </ul>
                </li> --}}


                {{-- @if (auth()->user()->role_id == 3)
                    @isset($sidebar_menus)
                        @foreach ($sidebar_menus as $sidebar_menu)
                            @if (sidebarPermission($sidebar_menu->permissionInfo) == true)
                                @if ($sidebar_menu->permissionInfo->lang_name)
                                    <span class="menu_seperator">{{ __($sidebar_menu->permissionInfo->lang_name) }}</span>
                                @endif
                                @foreach ($sidebar_menu->subModule as $item)
                                    @if (sidebarPermission($item->permissionInfo) == true)
                                        <li class="{{ spn_active_link(subModuleRoute($item), 'mm-active') }}">

                                            @if (($item->subModule->count() > 0 && $item->permissionInfo->route != 'dashboard') || $item->permissionInfo->relate_to_child == 1)
                                                <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                                                @else
                                                    <a href="{{ validRouteUrl($item->permissionInfo->route) }}">
                                            @endif
                                            <div class="nav_icon_small">
                                                <span class="{{ $item->permissionInfo->icon }}"></span>
                                            </div>
                                            <div class="nav_title">
                                                <span>{{ __($item->permissionInfo->lang_name ?? $item->permissionInfo->name) }}</span>
                                                @if (config('app.app_sync') && $item->permissionInfo->module && in_array($item->permissionInfo->module, $paid_modules))
                                                    @if (config('app.app_sync'))
                                                        <span class="demo_addons">Addon</span>
                                                    @endif
                                                @endif
                                            </div>
                                            </a>
                                            <ul class="mm-collapse">
                                                @if (@$item->subModule)
                                                    @foreach (@$item->subModule as $key => $sub)
                                                        @if ($sub->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && sidebarPermission($sub->permissionInfo))
                                                            @foreach ($childrens as $children)
                                                                @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                                                    @continue
                                                                @endif

                                                                <li>
                                                                    <a href="{{ validRouteUrl($sub->permissionInfo->route, $children->id) }}"
                                                                        class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">

                                                                        {{ __($sub->permissionInfo->lang_name) }} -
                                                                        {{ $children->full_name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            @if (sidebarPermission($sub->permissionInfo))
                                                                <li>
                                                                    <a href="{{ validRouteUrl($sub->permissionInfo->route) }}"
                                                                        class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">
                                                                        {{ __($sub->permissionInfo->lang_name ?? $sub->permissionInfo->name) }}
                                                                    </a>

                                                                </li>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if ($item->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && count($item->subModule) == 0 && sidebarPermission($item->permissionInfo))
                                                    @foreach ($childrens as $children)
                                                        @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                                            @continue
                                                        @endif

                                                        <li>
                                                            <a href="{{ validRouteUrl($item->permissionInfo->route, $children->id) }}"
                                                                class="{{ spn_active_link(subModuleRoute($item), 'active') }}">

                                                                {{ __($item->permissionInfo->lang_name) }} -
                                                                {{ $children->full_name }}
                                                                {{ $item->permissionInfo->route }}</a>

                                                        </li>
                                                    @endforeach
                                                @endif

                                            </ul>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endisset
                @endif --}}

                {{-- @if (moduleStatusCheck('CustomMenu'))
                        @if (auth()->user()->role_id != 1)
                            @include('custom_menu::menu')
                        @endif
                    @endif --}}

            </ul>
        </nav>
        <!-- sidebar part end -->
        {{-- ------------------ --}}



        <!-- Page Content  -->
        <div id="main-content">
            <input type="hidden" name="url" id="url" value="{{ url('/') }}">
            @include('menu')

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






        $(document).ready(function(){
            var sections=[];
            $('.menu_seperator').each(function() { sections.push($(this).data('section')); });

            jQuery.each(sections, function(index, section) {
                if($('.'+section).length == 0) {
                    $('#seperator_'+section).addClass('d-none');
                }else{
                    $('#seperator_'+section).removeClass('d-none');
                }
            });
        })
    </script>

</body>

</html>
