@if(isset($finalyear) && $finalyear != "" && $finalyear != '[]' && !empty($finalyear))

        @if(isset($chart) && $chart == 1)
        <div class="col-lg-12 grid-margin stretch-card bar-chart">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Bar chart</h4>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        @else 
        <div class="col-lg-12 grid-margin stretch-card pie-chart" id="pie-chart">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pie chart</h4>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
@endif

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


<script> 

@if(isset($chart) && $chart == 1)

function GetBarchartData() { 
    'use strict';

    var data = {
            labels: [<?php echo '"'.implode('","',  array_keys($finalyear) ).'"' ?>],
        datasets: [{
            label: '# of user',
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
@else 

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
@endif

</script>
