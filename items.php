<?php
# load db credentials
require_once("../data/db_info0.php");
include("navv.html");

# connect to db
$con = mysqli_connect($SERV, $USER, $PASS, $DBNM) or die("Connection failed!");
print "Connected succesfully<br>";

# get group
$item_id = $_GET["item_id"];

# validate $item_id
if(preg_match("/\D/", $item_id)){
  print <<<eot1
    Invalid input<br>
    <a href="index.php">Return to thread list</a>
  eot1;
}

# get ls_listed check from post
$listed_new = isset($_GET["is_listed"])? htmlspecialchars($_GET["is_listed"]) : null;
print $listed_new;
if(isset($listed_new)){
  mysqli_query($con, "update items set listed = $listed_new where item_id = $item_id");
  header("Location: items.php?item_id=$item_id"); /* Redirect browser */
  exit();
}

# fetch record that matches group id
$re = mysqli_query($con, "select * from items where item_id = $item_id");
$row = mysqli_fetch_assoc($re);

$serial = $row["serial"];
$url = $row["url"];
$brand_id = $row["brand_id"];
$variations_page = $brand_id == 3? "variations3" :  "variations";

# fetch brand name
$re = mysqli_query($con, "select brand from Brands where id = $brand_id");
$brand = mysqli_fetch_array($re)[0];

# print out thread titless
print <<<eot2
  <html>
  <head>
    <meta charset="utf-8">
    <title>Item $item_id</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
    <div class="row">
      <font size="7" color="indigo">
        Item: $serial
      </font>
    </div>
    <a href="$url" target="_blank">$url</a><br>
    <a href="pricing.php?item_id=$item_id">Pricing $item_id</a><br>
    <a href="$variations_page.php?item_id=$item_id">Variations $item_id</a><br>

eot2;
print "<table class='table'>";
print "<tr><th>Item ID: </th><td>".$row["item_id"]."</td></tr>";
print "<tr><th>Brand: </th><td>".$brand."</td></tr>";
print "<tr><th>item_name: </th><td>".$row["item_name"]."</tr>";
print "<tr><th>Price: </th><td>".$row["price"]."</tr>";
print "<tr><th>original_price: </th><td>".$row["original_price"]."</tr>";
print "<tr><th>sale_info: </th><td>".$row["sale_info"]."</tr>";
print "<tr><th>description: </th><td>".nl2br($row["description"])."</tr>";
print "<tr><th>details: </th><td>".nl2br($row["details"])."</tr>";
print "<tr><th>season: </th><td>".$row["season"]."</tr>";
print "<tr><th>reference: </th><td>".$row["reference"]."</tr>";
print "<tr><th>postage: </th><td>".$row["postage"]."</tr>";
print "<tr><th>packing: </th><td>".$row["packing"]."</tr>";
print "<tr><th>listed: </th><td>".$row["listed"];

print <<<eot3
<form method="get" action="items.php?item_id=$item_id">
eot3;

print '<input type="radio" id="listed-0" name="is_listed" value=0 ';
if ($row["listed"] == 0) print 'checked="checked" ';
print '>';
print '<label for="listed-0"> 0: Not going to be listed</label>';

print '<input type="radio" id="listed-1" name="is_listed" value=1 ';
if ($row["listed"] == 1) print 'checked="checked" ';
print '>';
print '<label for="listed-1"> 1: Listed</label>';

print '<input type="radio" id="listed-2" name="is_listed" value=2 ';
if ($row["listed"] == 2) print 'checked="checked" ';
print '>';
print '<label for="listed-2"> 2: Retired</label>';

print '<input type="radio" id="listed-3" name="is_listed" value=3 ';
if ($row["listed"] == 3) print 'checked="checked" ';
print '>';
print '<label for="listed-3"> 3: New</label>';

print '<input type="radio" id="listed-4" name="is_listed" value=4 ';
if ($row["listed"] == 4) print 'checked="checked" ';
print '>';
print '<label for="listed-4"> 4: Need Update</label>';

print <<<eot4
<input type="hidden" name="item_id" value=$item_id>
<input type="submit" value="Update" class="btn btn-primary btn-sm" />
</form>
</td></tr>
eot4;

$is_dutiable = $row["is_dutiable"] == 1 ? 'Yes' : 'No';
print "<tr><th>Dutiable: </th><td>$is_dutiable</td></tr>";
print "</table>";

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


print "<h3>Listed</h3>";

# show messages from tbj1
$re = mysqli_query($con, "select * from listed_items where item_id = $item_id");
if (mysqli_num_rows($re) > 0) {
  $row = mysqli_fetch_array($re);
  $bm_item_url = "https://www.buyma.com/item/$row[13]";
  $bm_edit_url = "https://www.buyma.com/my/sell/$row[13]/edit?tab=b";
  print "<table class='table'>";
  print "<tr><th>Listed ID: </th><td>".$row[0]."</td></tr>";
  print "<tr><th>listed_name: </th><td>".$row[2]."</td></tr>";
  print "<tr><th>comment: </th><td>".nl2br($row[3])."</td></tr>";
  print "<tr><th>category: </th><td>".$row[4]."</td></tr>";
  print "<tr><th>season: </th><td>".$row[5]."</td></tr>";
  print "<tr><th>theme: </th><td>".$row[6]."</td></tr>";
  print "<tr><th>tags: </th><td>".$row[7]."</td></tr>";
  print "<tr><th>valid_till: </th><td>".$row[8]."</td></tr>";
  print "<tr><th>Sale Price: : </th><td>".$row[9]."</td></tr>";
  print "<tr><th>supplier: </th><td>".$row[10]."</td></tr>";
  print "<tr><th>reference: </th><td>".nl2br($row[11])."</td></tr>";
  print "<tr><th>duties: </th><td>".$row[12]."</td></tr>";
  print "<tr><th>buyma_id: </th><td><a href=$bm_item_url target='_blank'>".$row[13]."</td></tr>";
  print "<tr><th>Edit: </th><td><a href=$bm_edit_url target='_blank'>".$row[13]."</td></tr>";

  print "<tr><th>bm_control: </th><td>".$row[14]."</td></tr>";
  print "<tr><th>bm_brand_id: </th><td>".$row[15]."</td></tr>";
  print "<tr><th>bm_pcs: </th><td>".$row[16]."</td></tr>";
  print "<tr><th>bm_has_refprice: </th><td>".$row[17]."</td></tr>";
  print "</table>";
}else{
  print "0 results.";
}

print "<h3>Other Buyers</h3>";
# show messages from buyer_price
$re = mysqli_query($con, "select * from buyer_price where item_id = $item_id order by price");
if (mysqli_num_rows($re) > 0) {
  print "<table class='table'>";
  while($row = mysqli_fetch_array($re)){
    print "<tr>";
    print "<td>$row[2]</td>";
    print "<td>$row[3]</td>";
    $url = "https://www.buyma.com/".$row[4];
    print "<td><a href=$url target='_blank'>$row[4]</a></td>";
    print "<td>$row[5]</td>";
    print "</tr>";
  }
  print "</table>";
}else{
  print "0 results.";
}

# close db
mysqli_close($con);

print <<<eot6
</div>
<a href="index.php">Back to top</a>
</body>
</html>
eot6;

?>
