@extends('layouts.app')
@section('mainContent')
 <section class="mb-40">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">Welcome   
                            @if (Auth::user())
                                
                            {{Auth::user()->name}} {{Auth::user()->last_name}} role->id {{ Auth::user()->role_id}}
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
                                    <h3>Parents</h3>
                                    <p class="mb-0">Total Parents count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($parentsCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mt-30-md">
                    <a href="#" class="d-block">
                        <div class="white-box single-summery fuchsia">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3>Staffs</h3>
                                    <p class="mb-0">Total Staffs count</p>
                                </div>
                                <h1 class="gradient-color2">{{ number_format($staffsCount) }}</h1>
                            </div>
                        </div>
                    </a>
                </div>
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

    <section class="mt-50" id="incomeExpenseSessionDiv">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-9">
                    <div class="main-title">
                        <h3 class="mb-30">Income and Expenses for the Session 2018/19</h3>
                    </div>
                </div>
                <div class="offset-lg-2 col-lg-2 text-right col-md-3">
                    <button type="button" class="primary-btn small tr-bg icon-only" id="areaChartBtn">
                        <span class="pr ti-move"></span>
                    </button>

                    <button type="button" class="primary-btn small fix-gr-bg icon-only ml-10" id="areaChartBtnRemovetn">
                        <span class="pr ti-close"></span>
                    </button>
                </div>
                <div class="col-lg-12">
                    <div class="white-box" id="areaChartDiv">
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
                                <div id="commonAreaChart" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-50">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">Notice Board</h3>
                    </div>
                </div>

                <div class="col-lg-12">
                    <table class="school-table-style w-100">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($notices as $notice)
                        <tr>
                            <td>{{ $notice->file_name ?? 'Notice' }}</td>
                            <td>{{ Str::limit('Content uploaded', 100) }}</td>
                            <td>
                                <a href="#" class="primary-btn small tr-bg">View</a>
                                <a href="#" class="primary-btn small fix-gr-bg">Edit</a>
                                <a href="#" class="primary-btn small danger-btn">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-50">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">Calendar</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
                                <div class='common-calendar'></div>
                                <div class="mt-20">
                                    <h4>Upcoming Events</h4>
                                    @if($events->isEmpty())
                                        <p>No events available.</p>
                                    @else
                                        <ul>
                                            @foreach($events as $event)
                                            <li>{{ $event->name }} - Course available</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 mt-50-md">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">To Do List</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box school-table">
                                @if(empty($todos))
                                    <p>No todos available.</p>
                                @else
                                    @foreach($todos as $todo)
                                    <div class="single-to-do d-flex justify-content-between">
                                        <div>
                                            <input type="checkbox" id="todo{{ $todo->id }}" class="common-checkbox" name="todo{{ $todo->id }}">
                                            <label for="todo{{ $todo->id }}">
                                                <h5 class="d-inline">{{ $todo->title ?? 'Todo' }}</h5>
                                                <p class="ml-35">{{ $todo->date ?? 'No date' }}</p>
                                            </label>
                                        </div>

                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                Edit
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#">view</a>
                                                <a class="dropdown-item" href="#">edit</a>
                                                <a class="dropdown-item" href="#">delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
