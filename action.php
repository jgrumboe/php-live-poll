<?php
//action.php
$db = new SQLite3('mysqlitedb.db');

if(isset($_POST["vote"]))
{
 $query = "
  INSERT INTO votes_table(vote) VALUES('".$_POST["vote"]."')
 ";
 $db->query($query);
 }

 $sub_query = "
   SELECT vote, count(*) as no_of_vote FROM votes_table 
   GROUP BY vote 
   ORDER BY id ASC";
 $result = $db->query($sub_query);
 $data = array();
 while($row = $result->fetchArray())
 {
  $data[] = array(
   'label'  => $row["vote"],
   'value'  => $row["no_of_vote"]
  );
 }
 $data = json_encode($data);
 echo $data;
?>
