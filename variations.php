<?php
# load db credentials
require_once("../data/db_info0.php");
include("navv.html");

# connect to db
$con = mysqli_connect($SERV, $USER, $PASS, $DBNM) or die("Connection failed!");
print "Connected succesfully<br>";

# get item_id
$item_id = $_GET["item_id"];

# validate $item_id
if(preg_match("/\D/", $item_id)){
  print <<<eot1
    Invalid input<br>
    <a href="index.php">Return to thread list</a>
  eot1;
}

$variation_id = isset($_GET["variation_id"])? htmlspecialchars($_GET["variation_id"]) : null;
$bm_col_name = isset($_GET["bm_col_name"])? htmlspecialchars($_GET["bm_col_name"]) : null;
$bm_col_fami = isset($_GET["bm_col_fami"])? htmlspecialchars($_GET["bm_col_fami"]) : null;

if(!empty($bm_col_name)){
  $sql = "UPDATE Variations SET bm_col_name = '$bm_col_name', bm_col_family = $bm_col_fami where id = $variation_id";
  mysqli_query($con, $sql);
  header("Location: variations.php?item_id=$item_id"); /* Redirect browser */
  exit();
}

# fetch record that matches item_id
$re = mysqli_query($con, "select * from items where item_id = $item_id");
$row = mysqli_fetch_assoc($re);

$serial = $row["serial"];
$brand_id = $row["brand_id"];
$item_name = $row["item_name"];
$brand = $brand_id ==1 ? "CharlesKeith" : "Pedro";

# print out item header
print <<<eot2
<html>
<head>
  <meta charset="utf-8">
  <title>Variations $item_id</title>
  <link rel="stylesheet" type="text/css"href="mystyle.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <div>
      <h1>Variations of $serial</h1>
      <h2>Item ID: $item_id</h2>
      <a href="items.php?item_id=$item_id">Items $item_id</a><br>
      <a href="pricing.php?item_id=$item_id">Pricing $item_id</a><br>
    </div>
    <div>
      <span id="img-folder">ShopImages/$brand/$serial</span>
      <button class="btn btn-primary" id="open-img">Open Image Folder</button>
    </div>
eot2;

# show records from Variations
$re = mysqli_query($con, "select * from variations where item_id = $item_id order by color_code");

print <<<eot4
<h3>Variations</h3>
<table class="table table-bordered table-sm">
<tr>
  <th>id</th>
  <th>sku</th>
  <th>url</th>
  <th>color code</th>
  <th>size</th>
  <th>has stock</th>
  <th>availability</th>
  <th>BUYMA 並び順</th>
  <th>BUYMA 検索用サイズ</th>
  <th>BUYMA_色名称</th>
  <th>BUYMA 色系統</th>
  <th>BUYMA 登録済み</th>
</tr>
eot4;
while($row = mysqli_fetch_array($re)){
  # fetch brand name
  $has_stock = $row[6];
  $check = $has_stock == 1 ? 'checked' : null;

  print <<<eot5
  <tr>
    <td>$row[0]</td>
    <td>$row[2]</td>
    <td><a href=$row[3] target='_blank'>$row[3]</a><br></td>
    <td>$row[4]</td>
    <td>$row[5]</td>
    <td>
    <input type='checkbox' name='availability' $check/>
    </td>
    <td>$row[7]</td>
    <td>$row[8]</td>
    <td>$row[9]</td>
    <td>$row[10]</td>
    <td>$row[11]</td>
    <td>$row[12]</td>
  </tr>
  eot5;
}
print "</table>";

print <<<eot4
<h3>Form</h3>
<table class="table table-bordered table-sm">
<tr>
  <th>id</th>
  <th>BUYMA 並び順</th>
  <th>Suggestion</th>
  <th>CK color code</th>
  <th>CK color name</th>
  <th>BUYMA_色名称</th>
  <th>CK => BM suggestion</th>
  <th>BUYMA 色系統</th>
</tr>
eot4;

$sql = "select Variations.*, ck_colors.*
from variations, Ck_colors
where item_id = $item_id and Variations.color_code = Ck_colors.color_code
order by Variations.color_code";
$re = mysqli_query($con, $sql);
$counter = 0;
$variations = array();
while($row = mysqli_fetch_assoc($re)){
  $row_var_id = $row['id'];
  $counter++;
  $variations[] = $row_var_id;
  print <<<eot5
  <tr>
  <form method="get" action="variations.php">
    <td>$row[id]</td>
    <td>$row[bm_order]</td>
    <td>$row[color_code]</td>
    <td>$row[color_name] ($row[color_j])</td>
    <td><input class="form-control" type="text" name="bm_col_name" value=$row[bm_col_name] /></td>
    <td class="prediction">$row[bm_color_family]</td>
    <td>
      <select name="bm_col_fami" class='form-control'>
  eot5;
  $row_col_family = $row['bm_col_family'];
  $re2 = mysqli_query($con, "select * from bm_colors");
  while($colrow = mysqli_fetch_assoc($re2)){
    print "<option value=$colrow[ID] ";
    if ($row_col_family == $colrow['ID'])
      print "selected='selected'";
    print ">$colrow[ID]:$colrow[color_name]</option>";
  }
  print <<<eot6
      </select>
    </td>
    <td>
      <input type="hidden" name="variation_id" value=$row_var_id>
      <input type="hidden" name="item_id" value=$item_id>
      <input class="btn btn-primary btn-sm" type="submit" value="Update">
    </td>
    </form>
  </tr>
  eot6;
}
print "</table>";

# number of variations
$arrlength = count($variations);

print <<<eot
<table class="table table-bordered table-sm">
eot;

for($x = 0; $x < $arrlength; $x++) {
  $sql = "SELECT * FROM Images
  where variation_id = {$variations[$x]}
  order by img_name";
  $re = mysqli_query($con, $sql);

  print <<<eot
  <tr>
    <td>$variations[$x]</td>
  eot;
  while($row = mysqli_fetch_assoc($re)){
    // print $row['img_urls'];
    print <<<eot
      <td>
        $row[img_name] <a href='$row[img_url]' target="_blank"><img src=$row[img_url] width='100'></img></a>
      </td>
    eot;
  }
  print "</td>";
  print "</tr>";
}

print "</table>";

# fetch listed items for the item_id
$re2 = mysqli_query($con, "select * from listed_items where item_id = $item_id");
$listed_row = mysqli_fetch_array($re2);
print "<tr>";
if (mysqli_num_rows($re2) > 0) {
  if (strlen($listed_row[13]) > 0){

    print <<<eot3
    <td>
    <a href="https://www.buyma.com/item/$listed_row[13]" target="_blank">$listed_row[13]</a>
    <a href="https://www.buyma.com/my/sell/$listed_row[13]/edit?tab=b" target="_blank">Edit</a>
    </td>
    <td>$listed_row[14]</td>
    eot3;
  }
}
print "</tr>";



# close db
mysqli_close($con);

print <<<eot10
</div>
<a href="notlisted.php">Back to Not Listed Items</a>
</div>
<script src="jquery-3.1.0.min.js"></script>
<script type="text/javascript" src="./shop.js"></script>
<script type="text/javascript" src="./listing.js"></script>
</body>
</html>
eot10;

?>
