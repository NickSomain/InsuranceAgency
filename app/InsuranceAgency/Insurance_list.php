<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
header('Content-Type: application/json');
$con = new mysqli("MYSQL", "root", "", "InsuranceAgency");
$answer = array();
switch ($requestMethod) {
    case 'GET':
        if (empty(isset($_GET['insurance_id']))) {
            $result = $con->query("SELECT * FROM Insurance_list;");
            while ($row = $result->fetch_assoc()) {
                $answer[] = $row;
            }
        } else {
            $query_result = $con->query("SELECT * FROM Insurance_list WHERE insurance_id = " . $_GET['insurance_id'] . ";");
            $result = $query_result->fetch_assoc();
            $answer = $result;
        }
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($answer);
        } else {
            http_response_code(204);
        }
        break;

    case 'POST':
        $json = file_get_contents('php://input');
        $insurance = json_decode($json);
        if (!empty($insurance->{'insurance_type'}) &&
            !empty($insurance->{'insurance_price'}) &&
            !empty($insurance->{'insurance_description'})) {
            $insurance_type = $insurance->{'insurance_type'};
            $insurance_price = $insurance->{'insurance_price'};
            $insurance_description = $insurance->{'insurance_description'};
            $query_result = $con->query("SELECT * FROM InsuranceAgency WHERE insurance_type = '" . $insurance_type . "'");
            if (!empty($result)) {
                http_response_code(409);
            } else {
                $stmt = $con->prepare("INSERT INTO InsuranceAgency (insurance_type, insurance_price, insurance_description) VALUES (?, ?, ?)");
                $stmt->bind_param('sis', $insurance_type, $insurance_price, $insurance_description);
                $stmt->execute();
                http_response_code(201);
            }
        } else {
            http_response_code(422);
        }

        break;

    case 'PATCH':
        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        #break if no id
        if (empty(isset($_GET['insurance_id']))){
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        }
        else
        {
            $query_result = $con->query("SELECT * FROM Insurance_list WHERE insurance_id='".$_GET['insurance_id']."'");
            $result = $query_result->fetch_row();

            if (!empty($result)){

                if(!empty($obj->{'insurance_type'}))
                    $con->query("UPDATE InsuranceAgency SET insurance_type='".$obj->{'insurance_type'}."'WHERE insurance_id ='".$_GET['insurance_id']."'");

                if(!empty($obj->{'insurance_price'}))
                    $con->query("UPDATE InsuranceAgency SET insurance_price ='".$obj->{'insurance_price'}."'WHERE insurance_id='".$_GET['insurance_id']."'");

                if(!empty($obj->{'insurance_description'}))
                    $con->query("UPDATE InsuranceAgency SET insurance_description ='".$obj->{'insurance_description'}."' WHERE insurance_id='".$_GET['insurance_id']."'");

                $answer["status"] = "Success. User updated.";
                http_response_code(200);

            } else {
                $answer["status"] = "Error. User not found.";
                http_response_code(404);
            }
        }
        echo json_encode($answer);
        break;

    case 'DELETE':
        if (empty(isset($_GET['insurance_id']))) {
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM Insurance_list WHERE insurance_id='" . $_GET['insurance_id'] . "'");
            $result = $query_result->fetch_row();
            if (!empty($result)) {
                $query_result = $con->query("DELETE FROM Insurance_list WHERE insurance_id='" . $_GET['insurance_id'] . "'");
                http_response_code(200);
            } else {
                http_response_code(204);
            }
        }
        break;
    default:
        http_response_code(405);
        break;
}
?>
