@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-6">
                    <h4 class="card-title">Transection Information</h4>
                </div>
                <div class="row">
                    <div class="col-lg-6 grid-margin stretch-card">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="template-demo mt-3">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h4 class="card-title">Select User</h4>
                                            <select class="form-select" aria-label="Default select example" id="user" onchange="getUser()">
                                                <option value="" selected>All User</option>
                                                @foreach(App\Models\User::where('user_type', 'customer')->get() as $user)
                                                    <option value="{{ $user->id }}">{{ $user->phone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <h4 class="card-title">Select Time</h4>
                                            <select class="form-select" aria-label="Default select example" id="year" onchange="MonthwiseData()">
                                                <option value="1" selected>Year</option>
                                                <option value="2">Month</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4" id="monthdata">
                                            <h4 class="card-title">Select Month</h4>
                                            <select class="form-select col-lg-6" aria-label="Default select example" id="month">
                                                @foreach($finalyear as $key=>$year)
                                                    <option value="{{ $key }}"> {{ $key }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-center mt-2" id="submit" onclick="getMonthdata()"><button  class="btn btn-info" type="submit"> Submit </button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 grid-margin stretch-card">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="template-demo mt-3">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h4 class="card-title">Select Chart</h4>
                                            <select class="form-select" id="chart" onchange="getChart();">
                                                <option value="1" selected>Bar Chart</option>
                                                <option value="2">Pie Chart</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bar Chat -->
                <div class="row" id="single-bar">
                    <div class="col-lg-12 grid-margin stretch-card bar-chart">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Bar chart</h4>
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- //Pie chart -->
                    <div class="col-lg-12 grid-margin stretch-card pie-chart" id="pie-chart">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Pie chart</h4>
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>

$( document ).ready(function() {
    $("#monthdata").hide();
    $("#pie-chart").hide();
    $("#submit").hide();
    GetBarchartData();
});

function getUser(params) {
    var user = $("#user").val();
    if(user == ""){
        $("#submit").hide();
        var year = $("#year").val();
            if(year == 1){
                GetBarchartData();
            }else{
                GetPieChart();
            }
    }else{
        $("#submit").show();
        var year = $("#year").val();
            if(year == 1){
                GetBarchartData();
            }else{
                GetPieChart();
            }
    }
    
}

function GetBarchartData() { 
    'use strict';

    var data = {
            labels: [<?php echo '"'.implode('","',  array_keys($finalyear) ).'"' ?>],
        datasets: [{
            label: '# of Amount   ',
            data: [<?php echo '"'.implode('","',  $finalyear ).'"' ?>],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1,
            fill: false
        }]
    };

    var options = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        legend: {
            display: false
        },
        elements: {
            point: {
                radius: 0
            }
        }
    };

    if ($("#barChart").length) {
        var barChartCanvas = $("#barChart").get(0).getContext("2d");
        var barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: data,
            options: options
        });
    }
}

function GetPieChart(params) {
    var doughnutPieData = {
        datasets: [{
        data: [<?php echo '"'.implode('","',  $finalyear).'"' ?>],
        backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgb(234, 127, 97, 0.83)',
            'rgb(234, 127, 162, 0.83)'
        ],
        borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgb(234, 127, 97, 1)',
            'rgb(234, 127, 162, 1)'
        ],
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels:  [<?php echo '"'.implode('","',  array_keys($finalyear) ).'"' ?>]
    };
    var doughnutPieOptions = {
        responsive: true,
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    if ($("#pieChart").length) {
    var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
    var pieChart = new Chart(pieChartCanvas, {
      type: 'pie',
      data: doughnutPieData,
      options: doughnutPieOptions
    });
  }
}

function getChart(params) {
    var chart = $("#chart").val();
    if(chart == 1){
        $(".pie-chart").hide();
        $(".bar-chart").show();
        GetBarchartData();
    }else{
        $(".bar-chart").hide();
        $(".pie-chart").show();
        GetPieChart();
    }
     getUser();
}

function MonthwiseData(params) {
    var year = $("#year").val();
    if(year == 1){
        $("#monthdata").hide();
        $("#submit").hide();
        location.reload();
    }else{
        $("#monthdata").show();
        $("#submit").show();
    }
}

function getMonthdata(params) {
    $("#single-bar").html('');
    var year_type = $("#year").val();

    if(year_type == 1){
        var year = "";
        var chart = $("#chart").val();
        var user = $("#user").val();
    }else{
        var year = $("#month").val();
        var chart = $("#chart").val();
        var user = $("#user").val();
    }

    $.ajax({  
        type: 'POST',
        url: '{{ route('transection.month.data') }}', 
        data: { 
            year: year ,
            chart:chart,
            user: user,
            "_token": "{{ csrf_token() }}",
        },
        success: function(response) {
            $("#single-bar").html(response);
            GetBarchartData();
            GetPieChart();
        }
    });
}


</script>

@endsection