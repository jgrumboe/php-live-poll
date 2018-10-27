<?php 
//index.php

$db = new SQLite3('mysqlitedb.db');

$create_table ="
  CREATE TABLE IF NOT EXISTS votes_table (
    id   INTEGER PRIMARY KEY,
    vote    TEXT    NOT NULL
  );
";
$result = $db->exec($create_table);

$sub_query = "
   SELECT vote, count(*) as no_of_vote FROM votes_table 
   GROUP BY vote 
   ORDER BY id ASC";
$result = $db->query($sub_query);
$data[] = array('label' => 'No Data', 'value' => 0);
while($row = $result->fetchArray())
{
 $data[] = array(
  'label'  => $row["vote"],
  'value'  => $row["no_of_vote"]
 );
}
$data = json_encode($data);
?>


<!DOCTYPE html>
<html>
 <head>
  <title> Poll : Isn't this cool? </title>  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" /> 
     
</head>
 <body>
  <br /><br />
  <div class="container" style="width:900px;">
   <h2 align="center">Isn't this cool?</h2>
      <br>
   <form method="post" id="like_form">
     <div class="radio">
      <input type="button" name="button" class="btn btn-info" onClick="submit_vote(this.id);" value="Yes" />
     </div>
     <div class="radio">
      <input type="button" name="button" class="btn btn-info" onClick="submit_vote(this.id);" value="No" />
     </div>
     <div class="radio">
      <input type="button" name="button" class="btn btn-info" onClick="submit_vote(this.id);" value="Maybe" />
     </div>
   </form>
   <div id="chart"></div>
  </div>
 </body>
</html>

<script>

 var donut_chart = Morris.Donut({
     element: 'chart',
     data: <?php echo $data; ?>
    });
 
 function submit_vote(id){
   var value = document.getElementById(id).getAttribute('value');
   $.ajax({
    url:"action.php",
    method:"POST",
    data:"vote=" + value,
    dataType:"json",
    success:function(data)
    {
     donut_chart.setData(data);
    }
 }
 
 updatePoll = function(){
     $.ajax({
      url:"action.php",
      method:"GET",
      dataType:"json",
      success:function(data)
      {
       donut_chart.setData(data.length ? data : [ { label:"No Data", value:0 } ]);
      }
     });
   }
 setInterval(updatePoll, 2000);

</script>
