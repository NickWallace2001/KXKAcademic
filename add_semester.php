<?php require_once(__DIR__. "/partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>

<div class="container-fluid">
    <h3>Add a New Semester</h3>
    <form method= "POST">
        <div class="form-group">
            <label>Semester</label>
            <div class="col-sm-1">
                <input class="form-control" name="semester" placeholder="e.x. Fall 2020"/>
            </div>
        </div>
        <div class="col-sm-1">
            <input class="btn btn-warning" type="submit" name="save" value="Add Semester"/>
        </div>
    </form>
</div>

<?php
if (isset($_POST["save"])){
    $semester = $_POST["semester"];
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO Semesters (semester) VALUES (:semester)");
    $r = $stmt->execute([":semester" => $semester]);
    if ($r){
        flash("Created Successfully");
    }
    else{
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__. "/partials/flash.php");?>