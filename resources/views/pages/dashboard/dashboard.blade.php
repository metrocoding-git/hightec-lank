@extends('layouts.app')
@section('mainContent')
 <section class="mb-40">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">Welcome   
                            @if (Auth::user())
                                
                            {{Auth::user()->name}} {{Auth::user()->last_name}} 
                            @else
                            Test User
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <a href="#" class="d-block">
                        <div class="white-box single-summery cyan">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>Students</h3>
                                    <p class="mb-0">Total Students count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($studentsCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="#" class="d-block">
                        <div class="white-box single-summery violet">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>Teachers</h3>
                                    <p class="mb-0">Total Teachers count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($teachersCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mt-30-md">
                    <a href="#" class="d-block">
                        <div class="white-box single-summery blue">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>Labour</h3>
                                    <p class="mb-0">Total Labour count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($labourCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mt-30-md">
                    <a href="#" class="d-block">
                        <div class="white-box single-summery fuchsia">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>Admin</h3>
                                    <p class="mb-0">Total Admin count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($adminCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-40">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">Branches, Intakes, and Courses</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $colors = ['lightcyan', 'lightgreen', 'lightyellow', 'lightpink', 'lightblue', 'lightgray'];
                @endphp
                @forelse($branches as $index => $branch)
                    <div class="col-lg-4 col-md-6 mb-30">
                        <div class="white-box d-flex flex-column" style="background-color: {{ $colors[$index % count($colors)] }}; min-height: 400px;">
                            <h4 class="mb-20">{{ $branch->name }}</h4>
                            @if($branch->intakes->count() > 0)
                                @foreach($branch->intakes as $intake)
                                    <div class="mb-15">
                                        <h5 class="mb-10">Intake: {{ $intake->name }}</h5>
                                        @if($intake->courses->count() > 0)
                                            <ul class="list-unstyled">
                                                @foreach($intake->courses as $course)
                                                    <li class="d-flex justify-content-between py-2 border-bottom">
                                                        <span>{{ $course->name }}</span>
                                                        <strong>${{ number_format($course->price, 2) }}</strong>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">No courses assigned</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No intakes available</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12">
                        <div class="white-box text-center">
                            <p>No branches found</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="" id="incomeExpenseDiv">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-9">
                    <div class="main-title">
                        <h3 class="mb-30">Income and Expenses for November 2018</h3>
                    </div>
                </div>
                <div class="offset-lg-2 col-lg-2 text-right col-md-3">
                    <button type="button" class="primary-btn small tr-bg icon-only" id="barChartBtn">
                        <span class="pr ti-move"></span>
                    </button>

                    <button type="button" class="primary-btn small fix-gr-bg icon-only ml-10" id="barChartBtnRemovetn">
                        <span class="pr ti-close"></span>
                    </button>
                </div>
                <div class="col-lg-12">
                    <div class="white-box" id="barChartDiv">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <h1>${{ number_format($totalIncome) }}</h1>
                                    <p>Total Income</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <h1>${{ number_format($totalExpenses) }}</h1>
                                    <p>Total Expenses</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <h1>${{ number_format($totalProfit) }}</h1>
                                    <p>Total Profit</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center">
                                    <h1>${{ number_format($totalRevenue) }}</h1>
                                    <p>Total Revenue</p>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div id="commonBarChart" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
@endsection
