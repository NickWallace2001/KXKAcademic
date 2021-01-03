<?php require_once(__DIR__. "/partials/nav.php");?>
<?php
$db = getDB();
$stmt = $db->prepare("SELECT * from Semesters ORDER BY id DESC LIMIT 10");
$r = $stmt->execute();
$semesters = $stmt->fetchALL(PDO::FETCH_ASSOC);
//echo "<pre>" . var_export($semesters, true) . "</pre>";
//echo intval($semesters[0]["id"]);
?>
<div class="container-fluid">
    <h3>Add Grades for a Semester</h3>
    <form method= "POST">
        <div class="form-group">
            <label>Semester</label>
            <div class="col-sm-1">
                <select class="form-control" name="semester" value="<?php echo $semesters["semester"];?>">
                    <option value="<?php echo intval($semesters[0]["id"])?>" <?php echo ($semesters[0]["semester"] == intval($semesters[0]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[0]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[1]["id"]) ?>" <?php echo ($semesters[1]["semester"] == intval($semesters[1]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[1]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[2]["id"]) ?>" <?php echo ($semesters[2]["semester"] == intval($semesters[2]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[2]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[3]["id"]) ?>" <?php echo ($semesters[3]["semester"] == intval($semesters[3]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[3]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[4]["id"]) ?>" <?php echo ($semesters[4]["semester"] == intval($semesters[4]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[4]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[5]["id"]) ?>" <?php echo ($semesters[5]["semester"] == intval($semesters[5]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[5]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[6]["id"]) ?>" <?php echo ($semesters[6]["semester"] == intval($semesters[6]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[6]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[7]["id"]) ?>" <?php echo ($semesters[7]["semester"] == intval($semesters[7]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[7]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[8]["id"]) ?>" <?php echo ($semesters[8]["semester"] == intval($semesters[8]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[8]["semester"] ?></option>
                    <option value="<?php echo intval($semesters[9]["id"]) ?>" <?php echo ($semesters[9]["semester"] == intval($semesters[9]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[9]["semester"] ?></option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Course ID</label>
            <div class="col-sm-1">
                <input class="form-control" name="class" placeholder="e.x. IT 202"/>
            </div>
        </div>
        <div class="form-group">
            <label>Letter Grade</label>
            <div class="col-sm-1">
                <input class="form-control" name="grade" placeholder="e.x. B+"/>
            </div>
        </div>
        <div class="form-group">
            <label>Credits</label>
            <div class="col-sm-1">
                <input class="form-control" name="credits" placeholder="e.x. 3"/>
            </div>
        </div>
        <div class="col-sm-1">
            <input class="btn btn-warning" type="submit" name="save" value="Add Grade"/>
        </div>
    </form>
</div>
<?php
if (isset($_POST["save"])) {
    $semester = $_POST["semester"];
    $class = $_POST["class"];
    $grade = strtoupper($_POST["grade"]);
    $credits = $_POST["credits"];
    $qp = getQP($grade, $credits);
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Grades (class, grade, gpa_hours, quality_points, semester_id, user_id) VALUES (:class, :grade, :gpa_hours, :quality_points, :semester_id, :user_id)");
    $r = $stmt->execute([
        ":class" => $class,
        ":grade" => $grade,
        ":gpa_hours" => $credits,
        ":quality_points" => $qp,
        ":semester_id" => $semester,
        ":user_id" => $user
    ]);

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