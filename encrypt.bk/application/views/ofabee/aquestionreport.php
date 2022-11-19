<!DOCTYPE html>
<html>
<head>
  <title></title>
  <style type="text/css">
     .col-md-6{
       display: inline-block;
       width: 49%;
       box-sizing: border-box;
     }
     .card-data{
         text-align: center;
     font-size: 50px;
     font-family: arial;
     padding: 4%;
     color: #b3159e;

     }
     h1, h2{
       font-family: arial;
       text-align: center;
       color: #666;
     }
     .card-data span{
       font-size: 20px;
       color: #888;
     }
     .google-visualization-tooltip {
       max-width: 35%;

  }
  body{
   margin: 0px;
  }
  #chart_div{
   box-sizing: border-box;
   width: 95%;
   padding: 0px;
  }
  .button-holder{
   position: fixed;
   right: 0px;
   z-index: 2000;
  }
  .button {
     display: inline-block;
     background: #ccc;
     float: right;
     width: 35px;
     margin-right: 10px;
     height: 35px;
     border-radius: 50%;
     padding: 3px;
     box-sizing: border-box;
     line-height: 26px;
     cursor: pointer;
  }
  .button img {
     width: 18px;
     height: 18px;
  }
  </style>
  <script src="https://sdpk.ofabee.com/assets/themes/ofabee/js/jquery.min.js"></script>
  <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/loader.js" type="text/javascript">
  </script>
</head>
<body>
  <h1 id="test_name">Test Name</h1>
  <div class="col-md-6 card-data">
    <span>Total Attendees :</span> <label id="total_attendies"></label>
  </div>
  <div class="col-md-6 card-data">
    <span>Avg Success :</span> <label id="total_percentage"></label>
  </div>
  <h2>Question Wise Correct Answered</h2>
  <div id="chart_div"></div>
  <script type="text/javascript">
    var __inputData = '<?php echo json_encode($data) ?>';
    var __options = [];
    var __total_success = 0;
    $( document ).ready(function() {
        var optionValues = ['Questions','Percentage', {type: 'string', role: 'annotation'},{type: 'string', role: 'tooltip'}];
        __inputData = $.parseJSON(__inputData);
        __options.push(optionValues);
        //console.log(__inputData);
        $('#test_name').html(__inputData.name);
        $('#total_attendies').html(__inputData.total_users_attended['total_attempts']);
        var questions = __inputData.correct_questions;
        $.each(questions,function(q_key,question){
          var percentage = (question['total_correct']/__inputData.total_users_attended['total_attempts'])*100;
          percentage = Math.round(percentage);
          var data = [];
          data[0] = "Q"+q_key;
          data[1] = percentage;
          data[2] = percentage+'%';
          data[3] = $(atob(question['question'])).text();
          __options.push(data);
          __total_success+=percentage;
        });
        var avg_perc = __total_success/(__options.length-1);
        avg_perc = Math.ceil(avg_perc);
        $('#total_percentage').html(avg_perc+' %');

    });
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawAnnotations);

    function put_data(){
      var options = [];
      var optionValues = ['Questions','Percentage', {type: 'string', role: 'annotation'},{type: 'string', role: 'tooltip'}];

      options.push(optionValues);
      var data1 = ['Q1', 60,'60%',"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s "];
      options.push(data1);
      options.push(data1);options.push(data1);options.push(data1);options.push(data1);
          /*optionValues[0] = 'Questions';
          optionValues[1] = 'Percentage';
          optionValues[2] = [];
          optionValues[2]['type'] = "string";
          optionValues[2]['role'] = "annotation";
          optionValues[3] = [];
          optionValues[3]['type'] = "string";
          optionValues[3]['role'] = "tooltip";
          options[0] = optionValues;
          
          optionValues = new Array();
          optionValues[0] = 'Q1';
          optionValues[1] = '50';
          optionValues[2] = "60%";
          optionValues[3] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
          options[1] = optionValues;
*/
          return options;
    }

    function drawAnnotations() {
        //console.log(__options);
        var data = google.visualization.arrayToDataTable(__options);
        var chartAreaHeight = (__options.length-1) * 50;
        var chartHeight = chartAreaHeight + 70;
         var options = {
       //    title: 'Online Test Name',
           chartArea: {width: '80%', height:'90%'},
           height: chartAreaHeight,
           annotations: {
             alwaysOutside: false,
             textStyle: {
               fontSize: 12,
               auraColor: 'none',
               color: '#555'
             }
           },
           tooltip: { isHtml: true },
            legend: { position: 'none' },
            colors: ['#FFD700', '#C0C0C0', '#8C7853'],
             bar: {groupWidth: "60%"},
           hAxis: {
             title: 'Correct Answered',
             minValue: 0,
           
              viewWindow: {
                 max:100,
                 min:0
               }
           },
           vAxis: {
             title: 'Questions'
           }
         };
         var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
         chart.draw(data, options);
       }

  </script>
</body>
</html>