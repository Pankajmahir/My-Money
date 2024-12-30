
@if(isset($uservalue) && isset($finalmonth) && $finalmonth != "" && $uservalue != '')


<div class="col-lg-12 grid-margin stretch-card pie-chart">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Pie chart</h4>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $("#monthdata").show();
        $("#submit").show();
        getPieChart();
    });

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
        labels:  [<?php echo '"'.implode('","',  $finalmonth ).'"' ?>]
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
        getMonthdata();
        $(".pie-chart").hide();
        $(".bar-chart").show();
        GetchartData();
    }else{
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
            <h4 class="card-title">Pie chart</h4>
            <h2> No record Found !.. </h2>
        </div>
    </div>
</div>

@endif

