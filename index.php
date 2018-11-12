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
   <h2 align="center">Cool or not?</h2>
      <br>
<table style="width:90%; text-align:center">
<tbody>
<tr>
<td valign="middle">
   <form method="post" id="like_form">
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-info" onClick="submit_vote(this.id);" id="btn_yes" value="Yes" />
     </div>
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-info" onClick="submit_vote(this.id);" id="btn_no" value="No" />
     </div>
     <div class="radio">
      <input type="button" name="button" style="font-size : 50px; width: 90%; height: 100px;" class="btn btn-info" onClick="submit_vote(this.id);" id="btn_maybe" value="Maybe" />
     </div>
   </form>
  <img src='https://chart.googleapis.com/chart?cht=qr&chl=https%3A%2F%2F<?php echo $_SERVER['HTTP_HOST'] ?>%2F&chs=180x180&choe=UTF-8&chld=L|2' alt=''>
   </td>
   <td valign="middle">
   <div id="chart"></div>
   </td>
</tr>
</tbody>
</table>
  </div>
 </body>
</html>

<script>

 var donut_chart = Morris.Donut({
     element: 'chart',
     data: <?php echo $data; ?>
    });
 
 var previous_data;
 
 function arraysEqual(a, b) {
  if (a === b) return true;
  if (a == null || b == null) return false;
  if (a.length != b.length) return false;

  // If you don't care about the order of the elements inside
  // the array, you should sort both arrays here.
  // Please note that calling sort on an array will modify that array.
  // you might want to clone your array first.

  for (var i = 0; i < a.length; ++i) {
    if (a[i] !== b[i]) return false;
  }
  return true;
}
   
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
   });
 }
 
 updatePoll = function(){
     $.ajax({
      url:"action.php",
      method:"GET",
      dataType:"json",
      success:function(data)
      {
        if (data.length && arraysEqual(previous_data,data)) {
          donut_chart.setData(data);
        }
        previous_data=data;
      }
     });
   }
 setInterval(updatePoll, 2000);

</script>
