<?php
include_once("config.php");

if (!$conn) {
    echo json_encode(array("error" => "Connection Error"));
} else {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $product = $_POST['product'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];
            $category = $_POST['category'];

            $product = str_replace("'", "\'", $product);
            $desc = str_replace("'", "\'", $desc);
            $price = str_replace("'", "\'", $price);
            $category = str_replace("'", "\'", $category);

            if (!empty($product) && !empty($desc) && !empty($price) && !empty($category)) {
                if (isset($_FILES["file_img"]) && $_FILES["file_img"]["name"]) {
                    $sql = "";
                    $img = $_FILES["file_img"];
                    $img = str_replace("'", "\'", $img);
                    $extn = pathinfo($img["name"], PATHINFO_EXTENSION);
                    $filename = 'images/'.rand(0,44) .".". $extn;
                    if(move_uploaded_file( $img["tmp_name"], $filename )) {
                        $sql = "INSERT INTO Products ( p_name, p_desc, p_price, p_category, p_img ) VALUES ('$product', '$desc', '$price', '$category', '/$filename')";
                    }else{
                        echo json_encode(array("status" => "500", "message" => "Product Failed to Added, Check IMAGE"));
                    }
                } else {
                    $sql = "INSERT INTO Products ( p_name, p_desc, p_price, p_category ) VALUES ('$product', '$desc', '$price', '$category')";
                }
                $request = $conn->query($sql);
                switch ($request) {
                    case true:
                        echo json_encode(array("status" => "200", "message" => "Product Added Successiful"));
                        break;
                    case false:
                        echo json_encode(array("status" => "500", "message" => "Product Failed to Added"));
                        break;
                    default:
                        echo json_encode(array("status" => "300", "mesage" => "Fill the Blanks"));
                        break;
                }
            } else {
                echo json_encode(array("status" => "300", "mesage" => "Fill the Blanks"));
            }
            $conn -> close();
            break;
        case 'GET':
            $cty = $_GET['cty'];
            $cty = str_replace("'","\'", $cty);
            $sql = "";

            if(empty($cty)){
                $sql = "SELECT * FROM Products ORDER BY p_id DESC";
            }else{
                $sql = "SELECT * FROM Products WHERE category='$cty' ORDER BY p_id DESC";
            }
            $request = $conn -> query($sql);
            if($request){
                $receiver =  array();

                while($row = $request -> fetch_assoc()){
                    $receiver[] = $row;
                }
                echo json_encode(array(
                    "status" => "200", 
                    "message" => "Fetched Successiful",
                    "products"=> $receiver
                ));

            }
            break;
        default:
            echo json_encode(array("status" => "404", "method" => "No Method Found"));
            break;
    }
}
?>