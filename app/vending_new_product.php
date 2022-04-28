<?php
require_once("db_process.php");
$message = null;
if(isset($_POST["add_beverage"])){
   $message =  add_new_product($_POST["beverage"], $_POST["price"], $_POST["quantity"]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automaadi täitmise leht</title>
    <style>
        #edit_product{
            float:right;
        }
    </style>
</head>
<body>
    <a href="index.php">Buy some drink</a>
    <a href="vending_new_product.php">Add the new drink</a>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
    <a id="edit_product" href="vending_review.php">Edit products</a>
        <label for="beverage">Beverage name: </label>
        <input type="text" name="beverage" id="beveraga"><br>

        <label for="price">Price: </label>
        <input type="text" name="price" id="price"><br>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" min="0"><br>

        <input type="submit" name="add_beverage" value="Add beverage">
    </form>
    <?php if($message != null){ echo $message; } ?>
</body>
</html>