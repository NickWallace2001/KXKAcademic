<?php require_once(__DIR__. "/partials/nav.php");?>
<?php
//fetching info
if(isset($_GET["id"])){
    $id = $_GET["id"];
}
//fetching results for one semester
$db = getDB();
$user_id = get_user_id();

if (isset($_POST["save"])){
    $semester = $_POST["semester"];
    $class = $_POST["class"];
    $grade = strtoupper($_POST["grade"]);
    $credits = $_POST["credits"];
    $qp = getQP($grade, $credits);

    if(isset($id)) {
        $stmt = $db->prepare("UPDATE Grades set class=:class, grade=:grade, gpa_hours=:gpa_hours, quality_points=:quality_points, semester_id=:semester_id, user_id=:user_id where id=:id");
        $r = $stmt->execute([
            ":id" => $id,
            ":class" => $class,
            ":grade" => $grade,
            ":gpa_hours" => $credits,
            ":quality_points" => $qp,
            ":semester_id" => $semester,
            ":user_id" => $user_id
        ]);

        if ($r) {
            flash("Updated Successfully");
            die(header("Location: user_view_grades.php"));
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error creating: " . var_export($e, true));
        }
    }
    else{
        flash("ID isn't set, we need an ID in order to update");
    }
}


//fetching
$result = [];
if(isset($id)) {
    if (has_role("Admin")) {
        $stmt = $db->prepare("SELECT * FROM Grades where id = :id");
        $r = $stmt->execute([":id" => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $stmt = $db->prepare("SELECT * FROM Grades where id = :id AND user_id = :user_id");
        $r = $stmt->execute([
            ":id" => $id,
            ":user_id" => $user_id
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

//fetch semesters
$db = getDB();
$stmt = $db->prepare("SELECT * from Semesters ORDER BY id DESC LIMIT 10");
$r = $stmt->execute();
$semesters = $stmt->fetchALL(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <form method="POST">
        <?php if ($result["user_id"] == get_user_id() || has_role("Admin")): ?>
            <div class="form-group">
                <label>Semester</label>
                <h5>Ensure the correct semester is selected!</h5>
                <div class="col-sm-2">
                    <select class="form-control" name="semester" value="<?php echo $semesters["semester"];?>">
                        <option value="<?php echo intval($semesters[0]["id"]) ?>" <?php echo ($semesters[0]["semester"] == intval($semesters[0]["id"])?'selected=selected"selected"':'');?>><?php echo $semesters[0]["semester"] ?></option>
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
                <label>Course ID:</label>
                <div class="col-sm-1">
                    <input class="form-control" name="class" placeholder="e.x. IT 202" value="<?php safer_echo($result["class"]);?>"/>
                </div>
            </div>
            <div class="form-group">
                <label>Letter Grade</label>
                <div class="col-sm-1">
                    <input class="form-control" name="grade" placeholder="e.x. B+" value="<?php safer_echo($result["grade"]);?>"/>
                </div>
            </div>
            <div class="form-group">
                <label>Credits</label>
                <h5>If you are doing Pass Fail on this course, Please mark the credits as 0</h5>
                <div class="col-sm-1">
                    <input class="form-control" name="credits" placeholder="e.x. 3" value="<?php safer_echo($result["gpa_hours"]);?>"/>
                </div>
            </div>
        <?php else: ?>
            <p>You can't change someone else's grades</p>
        <?php endif; ?>
        <input class="btn btn-primary" type="submit" name="save" value="Update"/>
    </form>
</div>
