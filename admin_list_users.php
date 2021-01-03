<?php require_once(__DIR__. "/partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}

$query = "";
$results = [];
if (isset($_POST["query"])){
    $query = $_POST["query"];
    $_SESSION["query"] = $query;
}
elseif (isset($_SESSION["query"])){
    $query =  $_SESSION["query"];
}
?>
<?php
$per_page = 10;
if (!empty($query)){
    $db = getDB();
    $q = "SELECT count(*) as total FROM KXKUsers Where (username like :q)";
    $params = [":q" => "%$query%"];
    paginate($q, $params, $per_page);

    $stmt = $db->prepare("SELECT * FROM KXKUsers Where (username like :q) LIMIT :offset, :count");
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $stmt->bindValue(":q", "%$query%");
    $r = $stmt->execute();
    $e = $stmt->errorInfo();
    if($e[0] != "00000"){
        flash(var_export($e, true), "alert");
    }
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }
    else{
        flash("There was a problem fetching the results");
    }
}
else {
    $db = getDB();
    $q = "SELECT count(*) as total FROM KXKUsers";
    $params = [];
    paginate($q, $params, $per_page);

    $stmt = $db->prepare("SELECT * FROM KXKUsers LIMIT :offset, :count");
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $r = $stmt->execute();
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        flash(var_export($e, true), "alert");
    }
    if ($r) {
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);
    } else {
        flash("There was a problem fetching the results");
    }
}
//echo "<pre>" . var_export($results, true) . "</pre>";
?>
    <div class="container-fluid">
        <h3>List Surveys</h3>
        <form method="POST" class="form-inline">
            <input class="form-control" name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
            <input class="btn btn-warning" type="submit" value="Search" name="search"/>
        </form>

        <div class="results">
            <?php if (count($results) > 0): ?>
                <div class="list-group">
                    <?php foreach ($results as $r): ?>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col">
                                    <div>User:</div>
                                    <div><?php safer_echo($r["username"]); ?></div>
                                </div>
                                <div class="col">
                                    <a class="btn btn-dark" type="button" href="admin_view_grades.php?id=<?php safer_echo($r['id']); ?>&query=<?php echo $query ?>&username=<?php echo safer_echo($r["username"]); ?>">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No results</p>
            <?php endif; ?>
        </div>
        <?php include(__DIR__."/partials/pagination.php");?>
    </div>
<?php require(__DIR__ . "/partials/flash.php"); ?>