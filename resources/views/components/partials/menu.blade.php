

<style>
    .fas.fa-robot.menu-only {
        font-size: 20px;
        color: #828bb2;
        margin-right: 5px;
    }

    a.pulse.theme_color.bell_notification_clicker {
        margin-right: 15px !important;
    }

    @media (min-width: 1350px) {
        .header_middle {
            display: block !important;
        }
    }
    button#mobileSelectorDropdown {
        background: transparent;
        border: 0;
    }
    button#mobileSelectorDropdown svg {
        color: #2f2f3bad;
    }
    button#mobileSelectorDropdown + .dropdown-menu{
        background: var(--text_white);
        box-shadow: 0 9px 20px rgba(46, 35, 94, .07);
        border: 0;
        padding: 16px 0!important;
    }
    .dropdown.open .dropdown-menu {
        display: block;
    }

    .infix_session.open .nice_Select, 
    .languageChange.open .nice_Select {
        display: block;
    }
    button#mobileSelectorDropdown + .dropdown-menu .list,
    button#mobileSelectorDropdown + .dropdown-menu .nice-select-search-box,
    button#mobileSelectorDropdown + .dropdown-menu .nice-select{
        min-width: 100%;
        width: 100%!important;
    }

    button#mobileSelectorDropdown + .dropdown-menu .infix_session,
    button#mobileSelectorDropdown + .dropdown-menu .languageChange {
        padding: 0 16px;
    }

    button#mobileSelectorDropdown + .dropdown-menu .infix_session:hover,
    button#mobileSelectorDropdown + .dropdown-menu .languageChange:hover {
        background: #f6f6f6;
    }
</style>
<div class="container-fluid no-gutters" id="main-nav-for-chat">
    <div class="row">
        <div class="col-lg-12 p-0">
            <div class="header_iner d-flex justify-content-between align-items-center">
                <div class="small_logo_crm d-lg-none">
                    <a href="#">
                        
                            <img src="{{ asset('public/uploads/settings/logo.png') }}" alt="logo">
                    </a>
                </div>
                <div id="sidebarCollapse" class="sidebar_icon  d-lg-none">
                    <i class="ti-menu"></i>
                </div>
                <div class="collaspe_icon open_miniSide">
                    <i class="ti-menu"></i>
                </div>

                <div class="serach_field-area ml-40">
                    <div class="search_inner">
                        <form action="#">
                            <div class="search_field">
                                <input type="text" class="form-control primary_input_field input-left-icon"
                                    placeholder="Search" id="search" onkeyup="showResult(this.value)">
                            </div>
                            <button type="submit" style="padding-top: 3px"><i
                                    style="font-size: 13px; padding-left: 13px;" class="ti-search"></i></button>
                        </form>
                    </div>
                    <div id="livesearch" style="display: none;"></div>
                </div>
                <div class="header_right d-flex justify-content-between align-items-center">
                    <div class="serach_field-area mr-10">
                        <div class="search_inner">
                            <div class="search_field">
                                <input type="text" class="form-control primary_input_field input-left-icon"
                                    placeholder="Name/Admission No." id="searchStudent"
                                    onkeyup="showStudent(this.value)">
                            </div>
                            <button type="submit" style="padding-top: 3px"><i
                                    style="font-size: 13px; padding-left: 13px;" class="ti-search"></i></button>
                        </div>
                        <div id="liveStudentSearch" style="display: none;"></div>
                    </div>
                    <select name="#" class="nice_Select bgLess mb-0 infix_session" id="infix_session">
                       
                                <option value="1" selected>
                                  eervfe
                                </option>
                        
                    </select>

                    {{-- @if (@$styles && Auth::user()->role_id == 1)
                        @if (generalSetting()->style_btn == 1)
                            <select class="nice_Select bgLess mb-0 infix_theme_style" id="infix_theme_style">
                                <option data-display="@lang('common.select_style')"
                                    value="0">@lang('common.select_style')
                                </option>
                                @foreach ($styles as $style)
                                    <option value="{{ $style->id }}"
                                        {{ color_theme()->id == $style->id ? 'selected' : '' }}>
                                        {{ $style->title }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    @endif --}}
                        <select class="nice_Select bgLess mb-0 languageChange" id="languageChange">
                            <option data-display="@lang('common.select_language')"
                                value="0">@lang('common.select_language')
                            </option>
                           
                                <option data-display="ee" value="ee" selected>
                                    En</option>
                           
                        </select>

                  

                    <!-- Mobile view Dropdown for selection Academic Year and Language : Start -->

                    <div class="dropdown d-sm-none">
                        <button class="" type="button" id="mobileSelectorDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="5" r="2"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                            <circle cx="12" cy="19" r="2"></circle>
                        </svg>

                        </button>
                        <div class="dropdown-menu p-3" aria-labelledby="dropdownMenuButton">
                        
                    
                        </div>
                    </div>

                    <!-- Mobile view Dropdown for selection Academic Year and Language : END -->

                    <ul class="header_notification_warp d-flex align-items-center">

           
                        {{-- Start Notification --}}
                        <li class="scroll_notification_list">
                            <a class="pulse theme_color bell_notification_clicker show_notifications" href="#">
                                <!-- bell   -->
                                <img src="{{asset('public/backEnd/assets/img/icons/notification.svg')}}" alt="">

                                <!--/ bell   -->
                                <span
                                    class="notificationCount notification_count">66</span>
                                <span class="pulse-ring notification_count_pulse"></span>
                            </a>
                            <!-- Menu_NOtification_Wrap  -->
                            <div class="Menu_NOtification_Wrap notifications_wrap">
                                <div class="notification_Header">
                                    <h4>{{ __('common.no_unread_notification') }}</h4>
                                </div>
                                
                                <div class="nofity_footer">
                                    <div class="submit_button text-center pt_20">
                                        <a href="="
                                            class="primary-btn radius_30px text_white  fix-gr-bg mark-all-as-read">{{ __('common.mark_all_as_read') }}</a>
                                        <a href="{{ route('all-notification') }}"
                                            class="primary-btn radius_30px text_white  fix-gr-bg see_all_notification">{{ __('common.see_more') }}</a>
                                    </div>
                                </div>
                            </div>
                            <!--/ Menu_NOtification_Wrap  -->
                        </li>
                        {{-- End Notification --}}


                    </ul>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@if (moduleStatusCheck('AiContent'))
    @include('aicontent::content_generator_modal')
@endif

@if (moduleStatusCheck('WhatsappSupport'))
    @include('whatsappsupport::partials._popup')
@endif

<style>
    .messengerContainer:hover {
        cursor: pointer;
    }
</style>
@if($messenger_position == 'right') 
    <style>
        .messengerContainer {
            position: fixed;
            bottom: 85px;
            right: 22px;
            width: 48px;
            height: 45px;
            border-radius: 50%;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            z-index: 3;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 50px;
        }
    </style>
@elseif($messenger_position == 'left') 
    <style>
        .messengerContainer {
            position: fixed;
            bottom: 85px;
            width: 48px;
            height: 45px;
            left: 22px;
            border-radius: 50%;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            z-index: 3;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 50px;
        }
    </style>
@endif

@php
    $school_id = @Auth::user()->school_id;
    $tawk_is_enable = @App\Models\Plugin::where('school_id', $school_id)->where('name', 'tawk')->first()->is_enable;
    $messenger_is_enable = @App\Models\Plugin::where('school_id', $school_id)->where('name', 'messenger')->first()->is_enable;
@endphp

@if ($tawk_is_enable == 1)
    <div class="tawk-min-container tawk-test">
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{},
            Tawk_LoadStart=new Date();
            (function(){ var s1=document.createElement("script"),
            s0=document.getElementsByTagName("script")[0];
            s1.async=true; s1.src=`https://embed.tawk.to/@include('plugins.tawk_to')`;
            s1.charset='UTF-8'; s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0); })();
        </script>
    </div>  
@endif

@if ($messenger_is_enable == 1)
    <div class="messengerContainer">
        <!-- Messenger Chat Plugin Code -->
        <div id="fb-root"></div>

        <!-- Your Chat Plugin code -->
        <div id="fb-customer-chat" class="fb-customerchat">
        </div>

        <script>
        var chatbox = document.getElementById('fb-customer-chat');
        chatbox.setAttribute("page_id", "@include('plugins.messenger')");
        chatbox.setAttribute("attribution", "biz_inbox");
        </script>

        <!-- Your SDK code -->
        <script>
        window.fbAsyncInit = function() {
            FB.init({
            xfbml            : true,
            version          : 'v18.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        </script>
    </div>
@endif

<script>
    var position = '{{ $position }}';
    var tawkposition = '';

    if(position == 'left'){
        tawkposition = 'bl';
    }else{
        tawkposition = 'br';
    }
	var Tawk_API = Tawk_API || {};

	Tawk_API.customStyle = {
		visibility : {
			desktop : {
				position : tawkposition,
				xOffset : 0,
				yOffset : 20
			},
			mobile : {
				position : tawkposition,
				xOffset : 0,
				yOffset : 0
			},
			bubble : {
				rotate : '0deg',
			 	xOffset : -20,
			 	yOffset : 0
			}
		}
	};

    $(document).ready(function () {

        $('#infix_session_mbl').on('change', function () {
            const selectedSession = $(this).val();
        });

        $('#languageChange_mbl').on('change', function () {
            const selectedLanguage = $(this).val();
        });

        $('#mobileSelectorDropdown').on('click', function () {
            $(this).closest('.dropdown').toggleClass('open');
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown.open').removeClass('open');
            }
        });
    });

</script>
@section('script')
@endsection
