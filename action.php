<?php
//action.php
$db = new SQLite3('mysqlitedb.db');

if(isset($_POST["framework"]))
{
 $query = "
  INSERT INTO like_table(framework) VALUES('".$_POST["framework"]."')
 ";
 $db->query($query);
 }

 $sub_query = "
   SELECT framework, count(*) as no_of_like FROM like_table 
   GROUP BY framework 
   ORDER BY id ASC";
 $result = $db->query($sub_query);
 $data = array();
 while($row = $result->fetchArray())
 {
  $data[] = array(
   'label'  => $row["framework"],
   'value'  => $row["no_of_like"]
  );
 }
 $data = json_encode($data);
 echo $data;
?>
