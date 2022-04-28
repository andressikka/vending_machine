<?php
require_once("/var/lib/DB/config.php");

function add_new_product($p_name, $p_price, $p_quantity){
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare('INSERT INTO vending_machine (beverage, price, quantity) VALUES (?,?,?)');
    echo $conn->error;
    $stmt->bind_param("sdi", $p_name, $p_price, $p_quantity);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    return '<p style="color:green">You have successfully added a new drink!</p>';
}

function product_table(){
    $visitorsArray = "<table>
                        <th>Beverage name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Product deletion</th>
                       ";
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare('SELECT id, beverage, price, quantity FROM vending_machine');
    $conn->error;
    $stmt->bind_result($id, $beverage, $price, $quantity);
    $stmt->execute();
    while($stmt->fetch()){
        $visitorsArray .= '<tr>
                                <td>'. '<input type="text" name="beverage_name_'. $id .'" value="'. $beverage .'"/>' . '</td>'.
                                '<td>'. '<input type="text" name="beverage_price_'. $id .'" value="'. $price .'" size="1"/>' . '</td>'.
                                '<td>'. '<input type="text" name="beverage_quantity_'. $id .'" value="'. $quantity .'" size="1"/>' . '</td>'.
                                '<td><label for="beverage_deletion_' . $id. '">Remove product</label>'.
                                '<input type="checkbox" name="beverage_deletion_'. $id .'"/></td>
                            </tr>';
    }
    $visitorsArray .= '</table>';
    $stmt->close();
    $conn->close();
    return $visitorsArray;
}

function product_table_for_customer(){
    $visitorsArray = "<table>
                        <th>Beverage name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Buy</th>";
                        
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare('SELECT id, beverage, price, quantity FROM vending_machine');
    $conn->error;
    $stmt->bind_result($id, $beverage, $price, $quantity);
    $stmt->execute();
    while($stmt->fetch()){
        if($quantity == 0){
            $quantity = "Out of stock";
        }
    $visitorsArray .= '<tr><form action="" method="POST">
                <td>'. $beverage . '</td>'.
                '<td>'. $price  . '</td>'.
                '<td>'. $quantity . '</td>'.
                '<td><input name="buy_beverage_'. $id .'" type="submit" value="Buy" </td></form>'. 
        '</tr>';

    }
    $visitorsArray .= '</table>';
    $stmt->close();
    $conn->close();
return $visitorsArray;
}

function update_prodct_properties($array_to_fetch){
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    foreach($array_to_fetch as $key => $value){
        if(strpos($key, "beverage_deletion_") !== false){
            $id = substr($key, -1, 18);
            $stmt = $conn->prepare("DELETE FROM vending_machine WHERE id = ?");
            $conn->error;
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            unset($array_to_fetch["beverage_name_".$id]);
            unset($array_to_fetch["beverage_price_".$id]);
            unset($array_to_fetch["beverage_quantity_".$id]);
            unset($array_to_fetch["beverage_deletion_".$id]);
        }
    }
    foreach($array_to_fetch as $key => $value){
        if(strpos($key, "beverage_name_") !== false){
            $id = substr($key, -1, 14);
            $stmt = $conn->prepare("UPDATE vending_machine SET beverage = ? WHERE id = ?");
            $stmt->bind_param("si", $value, $id);
            $stmt->execute();
            $stmt->close();
        } else if (strpos($key, "beverage_price_") !== false){
            $id = substr($key, -1, 15);
            $stmt = $conn->prepare("UPDATE vending_machine SET price = ? WHERE id = ?");
            $stmt->bind_param("di", $value, $id);
            $stmt->execute();
            $stmt->close();
        } else if (strpos($key, "beverage_quantity_") !== false){
            $stmt = $conn->prepare("UPDATE vending_machine SET quantity = ? WHERE id = ?");
            $id = substr($key, -1, 18);
            $stmt->bind_param("ii", $value, $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    $conn->close();
}

function buy_product($id){
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $stmt = $conn->prepare("SELECT quantity, price FROM vending_machine WHERE id = ?");
    $conn->error;
    $stmt->bind_param("i", $id);
    $stmt->bind_result($quantity, $p_price);
    $stmt->execute();
    while($stmt->fetch()){
        if($quantity == 0){
            $stmt->close();
            $conn->close();
            return '<p style="color:red">Unfortunately this product is out of stock, please choose another one :(   </p>';
        } else {
            $stmt->close();
            $quantity -= 1;
            $stmt = $conn->prepare("UPDATE vending_machine SET quantity = ? WHERE id = ?");
            $conn->error;
            $stmt->bind_param("ii", $quantity, $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("SELECT money FROM vends_money WHERE id =?");
            $conn->error;
            $id_vend_money = 1;
            $stmt->bind_param("i", $id_vend_money);
            $stmt->bind_result($recieved_price);
            $stmt->execute();
            while($stmt->fetch()){
                $stmt->close();
                $stmt = $conn->prepare("UPDATE vends_money SET money = ? WHERE id =?");
                $conn->error;
                $recieved_price += $p_price;
                $stmt->bind_param("dd", $recieved_price, $id_vend_money);
                $stmt->execute();
                $stmt->close();
                $conn->close();
                return '<p style="color:green"> Thank you for the purchase! Enjoy your drink!</p>';
            }
        }
    }
}
function show_balance(){
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $stmt = $conn->prepare("SELECT money FROM vends_money WHERE id =?");
    $conn->error;
    $id = 1;
    $stmt->bind_param("i", $id);
    $stmt->bind_result($balance);
    $stmt->execute();
    while($stmt->fetch()){
        $stmt->close();
        $conn->close();
        return $balance;
    }
}

function cash_out(){
    $conn = new mysqli($GLOBALS["hostName"], $GLOBALS["userName"], $GLOBALS["password"], $GLOBALS["database"]);
    $stmt = $conn->prepare("UPDATE vends_money SET money = ? WHERE id = ?");
    $conn->error;
    $id = 1;
    $money = 0;
    $stmt->bind_param("ii", $money, $id);

    $stmt->execute();
    $stmt->close();
    $conn->close();
}

?>