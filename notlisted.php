<?php
# load db credentials
require_once("../data/db_info0.php");
include("navv.html");

# connect to db
$con = mysqli_connect($SERV, $USER, $PASS, $DBNM) or die("Connection failed!");
print "Connected succesfully<br>";

# show title and images
print <<<eot1
<html>
<head>
  <meta charset="utf-8">
  <title>Not Listed</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h2>New Items</h2>
  <br>
  <table class="table">
  <tr>
  <th>Item</th>
  <th>Pricing</th>
  <th>Variations</th>
  <th></th>
  <th></th>
  </tr>
eot1;

# get item_id from get
$item_id_d = isset($_GET["row_item_id"])? htmlspecialchars($_GET["row_item_id"]) : null;
$listed_new = isset($_GET["listed"])? htmlspecialchars($_GET["listed"]) : null;

print($listed_new);

if(!empty($item_id_d)){
  if(isset($listed_new)){
    mysqli_query($con, "update items set listed = $listed_new where item_id = $item_id_d");
    // header("Location: notlisted.php?"); /* Redirect browser */
    // exit();
  }
}

# fetch items whch are not listed yet.
$re = mysqli_query($con, "select * from items where listed = 3");
print mysqli_num_rows($re)." items";
while($row = mysqli_fetch_array($re)){
  $row_item_id = $row[0];
  $variations_page = $row[1] == 3? "variations3" :  "variations";
  $re2 = mysqli_query($con, "select * from listed_items where item_id = $row_item_id");
  print <<<eot2
  <tr>
    <td><a href="items.php?item_id=$row[0]" target="_blank">$row[0]</a></td>
    <td><a href="pricing.php?item_id=$row[0]">Pricing</a></td>
    <td><a href="$variations_page.php?item_id=$row[0]" target="_blank">Variations</a></td>
    <td>$row[4]</td>
    <td>$row[2]</td>
    <td>$row[14]</td>
    <td>
      <form>
        <select name="listed">
  eot2;
          print "<option value=0";
          if ($row[14] == 0) print " selected='selected'";
          print ">0: Not listed</option>";

          print "<option value=1";
          if ($row[14] == 1) print " selected='selected'";
          print ">1: Listed</option>";

          print "<option value=2";
          if ($row[14] == 2) print " selected='selected'";
          print ">2: Retired</option>";

          print "<option value=3";
          if ($row[14] == 3) print " selected='selected'";
          print ">3: New</option>";

          print "<option value=4";
          if ($row[14] == 4) print " selected='selected'";
          print ">4: Need update</option>";
          print <<< eot2
        </select>
        <input type="hidden" name="row_item_id" value=$row_item_id>
        <input type="submit" value="Update"/>
      </form>
    </td>
  eot2;

  $listed_row = mysqli_fetch_array($re2);
  if (mysqli_num_rows($re2) > 0) {
    print <<<eot3
      <td>$listed_row[0]</td>
      <td>$listed_row[2]</td>
      <td>$listed_row[9]</td>
      <td>
        <a href="https://www.buyma.com/item/$listed_row[13]" target="_blank">$listed_row[13]</a>
        <a href="https://www.buyma.com/my/sell/$listed_row[13]/edit?tab=b" target="_blank">Edit</a>
      </td>
      <td>$listed_row[14]</td>
    eot3;
  }
  print "</tr>";
}

# disconect db
mysqli_close($con);

# create a new thread
print <<<eot4
  </table>
  <a href="index.php">Back to top</a>
  <hr>
  </div>
  <script src="jquery-3.1.0.min.js"></script>
  </body>
  </html>
eot4;
?>
