<?php
# load db credentials
require_once("../data/db_info0.php");
include("navv.html");

# connect to db
$con = mysqli_connect($SERV, $USER, $PASS, $DBNM) or die("Connection failed!");
// print "Connected succesfully<br>";

$unlist_variation_id = isset($_POST["unlist_variation_id"])? htmlspecialchars($_POST["unlist_variation_id"]) : null;
if(!empty($unlist_variation_id)){
  mysqli_query($con, "update variations set has_stock = 0 where id = $unlist_variation_id");
}

print <<<eot1
<html>
<head>
  <meta charset="utf-8">
  <title>Check Stock</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h2>Out of Stock Items</h2>
  <br/>
  <table class="table table-sm">
  <tr>
    <th>BUYMA ID</th>
    <th>Listed Name</th>
    <th>BUYMA Control</th>
    <th>ID</th>
    <th>Item Name</th>
    <th>Listed</th>
    <th>sku</th>
    <th>Size</th>
    <th>Color Code</th>
    <th>Buyma Color Name</th>
    <th>Buyma stock</th>
    <th>Avalilability</th>
    <th>URL</th>
  </tr>
eot1;

# fetch variations that are out of stock
$sql = "SELECT `Variations`.`id`, `Variations`.`item_id`, `Items`.`item_name`, `Items`.`listed`, `Variations`.`sku`, `Variations`.`size_name`, `variations`.`color_code`, `variations`.`bm_col_name`, `Variations`.`has_stock`, `Variations`.`availability`, `Variations`.`url` FROM `Shop`.`Variations`, `Shop`.`Items` WHERE `Variations`.`item_id` = `Items`.`item_id` AND `Variations`.`has_stock` = 1 AND `Variations`.`availability` NOT IN ( 'In Stock', 'Low in Stock' ) ORDER BY `Variations`.`item_id` ASC, `variations`.`color_code` ASC";
$re = mysqli_query($con, $sql);

while($row = mysqli_fetch_array($re)){
  $variation_id = $row[0];
  $row_item_id = $row[1];
  $re2 = mysqli_query($con, "select * from listed_items where item_id = $row_item_id");
  $listed_row = mysqli_fetch_array($re2);
  print "<tr>";
  if (mysqli_num_rows($re2) > 0) {
    print <<<eot3
      <td>
        <a href="https://www.buyma.com/item/$listed_row[13]" target="_blank">$listed_row[13]</a><br>
        <a href="https://www.buyma.com/my/sell/$listed_row[13]/edit?tab=b" target="_blank">Edit</a>
      </td>
      <td>$listed_row[2]</td>
      <td>$listed_row[14]</td>
    eot3;
  }
  print <<<eot2
    <td><a href="items.php?item_id=$row_item_id" target="_blank">$row_item_id</a><br>$variation_id
    </td>
    <td>$row[2]</td>
    <td>$row[3]</td>
    <td>$row[4]</td>
    <td>$row[5]</td>
    <td>$row[6]</td>
    <td>$row[7]</td>
    <td>
      $row[8]
      <form method="post" action="checkstock.php">
      <input type="hidden" name="unlist_variation_id" value=$variation_id>
      <input type="submit" value="Unlisted" class="btn btn-danger"/>
      </form>
    </td>
    <td>$row[9]</td>
    <td><a href="$row[10]" target="_blank">$row[10]</a></td>
  eot2;

  print "</tr>";
}

# disconect db
mysqli_close($con);

print <<<eot3
  </table>
  <a href="index.php">Return to Top</a>
  </div>
  <script src="jquery-3.1.0.min.js"></script>
  </body>
  </html>
eot3;
?>
