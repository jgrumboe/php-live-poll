<?php 
//index.php

require_once "libs/Mobile_Detect.php";
$detect = new Mobile_Detect;

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
   <h2 align="center">Cool or not?</h2>
      <br>
<table style="width:100%; text-align:center">
<tbody>
<tr>

<?php
if( $detect->isMobile() ){
?> 

<td valign="middle">
   <form method="post" id="like_form">
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-primary" onClick="submit_vote(this.id);" id="btn_yes" value="Yes" />
     </div>
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-primary" onClick="submit_vote(this.id);" id="btn_no" value="No" />
     </div>
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-primary" onClick="submit_vote(this.id);" id="btn_maybe" value="Maybe" />
     </div>
    </form>
  </td>
<?php
} else {
?>
   <td>
    <img src='https://chart.googleapis.com/chart?cht=qr&chl=https%3A%2F%2F<?php echo $_SERVER['HTTP_HOST'] ?>%2F&chs=250x250&choe=UTF-8&chld=L|2' alt=''>
   </td>
   <td valign="middle">
   <div id="chart"></div>
   </td>
<?php
}
?>
  
</tr>
</tbody>
</table>
  </div>
 </body>
</html>

<script>

 function submit_vote(id){
   var value = document.getElementById(id).getAttribute('value');
   $.ajax({
    url:"action.php",
    method:"POST",
    data:"vote=" + value,
    dataType:"json"
   });
 }
 
<?php
if( !$detect->isMobile() ){
?> 
 var donut_chart = Morris.Donut({
     element: 'chart',
     data: <?php echo $data; ?>
    });
 
 var previous_data;
 var recent_data;
   

  updatePoll = function(){
     $.ajax({
      url:"action.php",
      method:"GET",
      dataType:"json",
      success:function(data)
      {
        recent_data=data;
        if (recent_data.length && recent_data != previous_data) {
          donut_chart.setData(recent_data);
          previous_data=recent_data;
        }
      }
     });
   }
 setInterval(updatePoll, 2000);
<?php
}
?> 

</script>
