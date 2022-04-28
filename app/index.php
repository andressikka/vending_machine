<?php 
// echo __DIR__;
require_once("db_process.php");
$message = null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($_POST as $key => $value){
        $id = substr($key, -1, 13);
        $message = buy_product($id);
    }
}
$table = product_table_for_customer();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vending Machine</title>
</head>
<body>
    <a href="vending_new_product.php">Add product</a>
    <a href="vending_review.php">Edit Product</a>
    <?= $table ?>
    <?php if($message != null){ echo $message; } ?>
</body>
</html>