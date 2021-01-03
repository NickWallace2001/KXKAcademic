
<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//end flash

function getVisibility($n){
    switch ($n){
        case 0:
            echo "Draft";
            break;
        case 1:
            echo "Private";
            break;
        case 2:
            echo "Public";
            break;
        case 3:
            echo "Disabled";
            break;
        default:
            echo "Unsupported visibility: " . safer_echo($n);
            break;
    }
}

function getURL($path){
    if (substr($path, 0, 1) == "/"){
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/it202/repo/project/$path";
}
function getURLkxk($path){
    if (substr($path, 0, 1) == "/"){
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/kxk_academic/repo/$path";
}

function deleteQuestion($question){
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM Questions WHERE id='$question'");
    $r = $stmt->execute();
    if($r){
        flash("Question successfully deleted");
    }
    else{
        $e = $stmt->errorInfo();
        flash("Error updating:". var_export($e, true));
    }
}

function deleteAnswer($answer){
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM Answers WHERE id='$answer'");
    $r = $stmt->execute();
    if($r){
        flash("Answer successfully deleted");
    }
    else{
        $e = $stmt->errorInfo();
        flash("Error updating:". var_export($e, true));
    }
}

function paginate($query, $params = [], $per_page = 10) {
    global $page;
    if (isset($_GET["page"])) {
        try {
            $page = (int)$_GET["page"];
        }
        catch (Exception $e) {
            $page = 1;
        }
    }
    else {
        $page = 1;
    }
    $db = getDB();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if ($result) {
        $total = (int)$result["total"];
    }
    global $total_pages;
    $total_pages = ceil($total / $per_page);
    global $offset;
    $offset = ($page - 1) * $per_page;
}

function getQP($grade, $credits){
    switch ($grade){
        case "A":
            $grade = 4*$credits;
            break;
        case "B+":
            $grade = 3.5*$credits;
            break;
        case "B":
            $grade = 3*$credits;
            break;
        case "C+":
            $grade = 2.5*$credits;
            break;
        case "C":
            $grade = 2*$credits;
            break;
        case "D+":
            $grade = 1.5*$credits;
            break;
        case "D":
            $grade = 1*$credits;
            break;
        case "F":
            $grade = 0*$credits;
            break;
        default:
            echo "Unsupported grade: " . safer_echo($grade);
            break;
    }

    return $grade;
}

?>