
@if(isset($uservalue) && isset($finalmonth) && $finalmonth != "" && $uservalue != '')


<div class="col-lg-12 grid-margin stretch-card bar-chart" id="bar-chart">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Bar chart</h4>
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $("#monthdata").show();
        $("#submit").show();
        GetchartData();
    });

function GetchartData() { 
    'use strict';

    var data = {
        @if(isset($finalmonth) && $finalmonth != "[]")
            labels: [<?php echo '"'.implode('","',  $finalmonth ).'"' ?>],
        @else 
            labels: [],
        @endif
        datasets: [{
        label: '# of Votes',
        @if(isset($uservalue) && $uservalue != "")
            data: [<?php echo '"'.implode('","',  $uservalue ).'"' ?>],
        @else 
            data: [<?php echo '"'.implode('","',  $uservalue ).'"' ?>],
        @endif
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
        getMonthdata();
        $(".bar-chart").hide();
        $(".pie-chart").show();
        getPieChart();
    }
}

</script>

@else 

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Bar chart</h4>
            <h2> No record Found !.. </h2>
        </div>
    </div>
</div>

@endif

