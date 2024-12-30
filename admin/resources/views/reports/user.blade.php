@extends('layouts.app')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-6">
                    <h4 class="card-title">User Information</h4>
                </div>
                <div class="row">
                    <div class="col-lg-6 grid-margin stretch-card">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="template-demo mt-3">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h4 class="card-title">Select Time</h4>
                                            <select class="form-select" aria-label="Default select example" name="" id="year" onchange="getdata()">
                                                <option value="1" selected>Year</option>
                                                <option value="2">Month</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6" id="monthdata">
                                            <h4 class="card-title">Select Month</h4>
                                            <select class="form-select col-lg-6" aria-label="Default select example" id="month">
                                                @foreach($finalyear as $year)
                                                    @if(isset($year) && $year != "")
                                                        <option value={{ str_replace('Year', '', $year) }}>{{ $year }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-center mt-2" id="submit"><button onclick="getMonthdata()" class="btn btn-info" type="submit"> Submit </button></div>
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
                <div class="row" id="monthwisedata">
                    <div class="col-lg-12 grid-margin stretch-card bar-chart">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Bar chart</h4>
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- //Pie chart -->
                    <div class="col-lg-12 grid-margin stretch-card pie-chart">
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
    

// A $( document ).ready() block.
$( document ).ready(function() {
    $("#monthdata").hide();
    $("#submit").hide();
    $(".pie-chart").hide();
    GetchartData();
});


function GetchartData() { 
    'use strict';

    var data = {
            labels: [<?php echo '"'.implode('","',  $finalyear ).'"' ?>],
        datasets: [{
            label: '# of user',
            data: [<?php echo '"'.implode('","',  $uservalue ).'"' ?>],
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

function getdata(params) {
    var year = $("#year").val();
    if(year == 1){
        $("#monthdata").hide(); 
        $("#submit").hide();
        var chart = $("#chart").val();
        if(chart == 1){
          location.reload();
        }else{
            getPieChart();
          location.reload();
        }   

    }else{
        $("#monthdata").show(); 
        $("#submit").show();
    }
}

function getMonthdata(){
    $("#monthwisedata").html('');
    var year = $("#month").val();
    var chart = $("#chart").val();
    $.ajax({  
        type: 'POST',
        url: '{{ route('users.month.data') }}', 
        data: { 
            year: year ,
            chart:chart,
            "_token": "{{ csrf_token() }}",
        },
        success: function(response) {
            $("#monthwisedata").html(response);
        }
    });
}

function getChart(params) {
    var chart = $("#chart").val();
    if(chart == 1){
        $(".pie-chart").hide();
        $(".bar-chart").show();
        GetchartData();
    }else{
        $(".bar-chart").hide();
        $(".pie-chart").show();
        getPieChart();
    }
}

function getPieChart(params) {
    var doughnutPieData = {
        datasets: [{
        data: [<?php echo '"'.implode('","',  $uservalue ).'"' ?>],
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
        labels:  [<?php echo '"'.implode('","',  $finalyear ).'"' ?>]
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

</script>
@endsection