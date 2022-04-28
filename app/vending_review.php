<?php
require_once("db_process.php");
if(isset($_POST["sbm_form"])){
    $form_data = [];
    // foreach($_POST as $key => $value){
    //     $key;
    //     $value;
    // }
    update_prodct_properties($_POST);
}

if(isset($_POST["cash"])){
    cash_out();
}
$balance = show_balance();
$table = product_table();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vending machine's review</title>
    <style>
        #add_product{
            float:right;
        }
    </style>
</head>
<body>
    <a href="index.php">Buy some drink</a>
    <a href="vending_review.php">Edit Product</a>
    <a id="add_product" href="vending_new_product.php">Add new product</a>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method = "POST">
        <?= $table ?><br>
        <input type="submit" name="sbm_form" value="Edit products properties">
    </form>
    
    <p style="float:left">Your balance: <?= $balance ?></p><br>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
        <input type="submit" name="cash" value="Get the cash">
    </form>
</body>
</html>