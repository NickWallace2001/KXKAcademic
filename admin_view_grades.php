<?php require_once(__DIR__. "/partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}

if (isset($_GET["id"])){
    $id = $_GET["id"];
}
if (isset($_GET["username"])){
    $username = $_GET["username"];
}
?>
<?php
//variables
$db = getDB();
$term_gpa = 0;
$cum_gpa = 0;
$user_id = $id;
$bothgpas = [];
$changeInGpa = 0;

if (isset($_POST["semester"])){
    //pulling selected semester classes
    $semester = $_POST["semester"];
    $stmt = $db->prepare("SELECT Grades.id, Grades.class, Grades.grade, Grades.gpa_hours, Grades.quality_points, Grades.semester_id, Grades.user_id, Semesters.id as semesterid FROM Grades JOIN Semesters on Grades.semester_id = Semesters.id where Semesters.id = :semester_id and Grades.user_id = :user_id");
    $r = $stmt->execute([
        ":semester_id" => $semester,
        ":user_id" => $user_id
    ]);
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

    //finding term gpa
    $termqp = 0;
    $termcredits = 0;
    foreach ($result as $index){
        $termqp += floatval($index["quality_points"]);
        $termcredits += floatval($index["gpa_hours"]);
    }
    if ($termqp > 0) {
        $term_gpa = $termqp / $termcredits;
    }

    //pulling quality points and credits from selected and previous semester
    $subid = $semester - 1;
    $stmt = $db->prepare("SELECT semester_id, id, class, grade, gpa_hours, quality_points, user_id FROM Grades where (semester_id = :id or semester_id = :subid) and user_id = :user_id ORDER BY semester_id asc");
    $r = $stmt->execute([
        ":id" => $semester,
        ":subid" => $subid,
        ":user_id" => $user_id
    ]);
    $gpaChange = $stmt->fetchAll(PDO::FETCH_GROUP);

    //finding the quality points and credits for each semester
    $totals = [[], []];
    if ($gpaChange){
        $i = 0;
        foreach ($gpaChange as $index => $sem){
            $qp = 0;
            $gh = 0;
            foreach ($sem as $details){
                $qp += floatval($details["quality_points"]);
                $gh += floatval($details["gpa_hours"]);
            }
            array_push($totals[$i], $qp);
            array_push($totals[$i], $gh);
            $i+= 1;
        }
    }

    //calculating both gpas
    if (count($totals) > 0) {
        foreach ($totals as $index) {
            if (!empty($index[0]) && !empty($index[1])) {
                $gpa = $index[0] / $index[1];
                array_push($bothgpas, round($gpa, 3));
            }
        }
    }

    //calculating change in gpa
    if (count($bothgpas) > 1) {
        $changeInGpa = $bothgpas[1] - $bothgpas[0];
    }

}
//to delete a class
elseif (isset($_POST["deleteclass"])){
    $itemID = $_POST["deleteclass"];
    deleteClass($itemID);
}
//fetching cumulative gpa info
$stmt = $db->prepare("SELECT * FROM Grades where Grades.user_id = :user_id ORDER BY semester_id asc");
$r = $stmt->execute([":user_id" => $user_id]);
$cumulative = $stmt->fetchALL(PDO::FETCH_ASSOC);

//finding cumulative gpa
$totalqp = 0;
$totalcredits = 0;
foreach ($cumulative as $index){
    $totalqp += floatval($index["quality_points"]);
    $totalcredits += floatval($index["gpa_hours"]);
}
if ($totalqp > 0) {
    $cum_gpa = $totalqp / $totalcredits;
}

//fetching semesters for dropdown
$stmt = $db->prepare("SELECT * from Semesters ORDER BY id DESC LIMIT 10");
$r = $stmt->execute();
$semesters = $stmt->fetchALL(PDO::FETCH_ASSOC);


//echo "<pre>" . var_export($result, true) . "</pre>";
//echo "<pre>" . var_export($gpaChange, true) . "</pre>";
//echo "<pre>" . var_export($totals, true) . "</pre>";
//echo "<pre>" . var_export($bothgpas, true) . "</pre>";
//echo "<pre>" . var_export($cumulative, true) . "</pre>";
?>

<div class="container-fluid">
    <h3>View Grade Information for <?php echo $username; ?></h3>
    <form method= "POST">
        <div class="form-group">
            <label>Semester</label>
            <div class="col-sm-2">
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
        <div class="col-sm-1">
            <input class="btn btn-warning" type="submit" name="search" value="Search"/>
        </div>
        <?php if ($term_gpa > 0): ?>
            <h4>Term GPA: <?php echo round($term_gpa, 3) ?></h4>
        <?php endif; ?>
        <h4>Term GPA Change From Previous Semester: <?php echo $changeInGpa ?></h4>
        <?php if ($cum_gpa > 0): ?>
            <h4>Cumulative GPA: <?php echo round($cum_gpa, 3) ?></h4>
        <?php endif; ?>
        <?php if ($cum_gpa < 2.5): ?>
            <h4 style="color:red">ACADEMIC PROBATION</h4>
        <?php endif; ?>
    </form>
<?php if (isset($result)): ?>
    <div class="results">
        <?php if (count($result) > 0): ?>
            <div class="list-group">
                <?php foreach ($result as $index => $class): ?>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">
                                <div>Course ID:</div>
                                <div><?php safer_echo($class["class"]) ?></div>
                            </div>
                            <div class="col">
                                <div>Grade Received:</div>
                                <div><?php safer_echo($class["grade"]) ?></div>
                            </div>
                            <div class="col">
                                <div>Credits:</div>
                                <div><?php safer_echo($class["gpa_hours"]) ?></div>
                            </div>
                            <div class="col">
                                <div>Quality Points:</div>
                                <div><?php safer_echo($class["quality_points"]) ?></div>
                            </div>
                            <form method="POST">
                                <div class ="col">
                                    <input type="hidden" name="deleteclass" value="<?php echo($class["id"]); ?>"/>
                                    <input class="btn btn-danger " type="submit" value="X"/>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results for chosen semester</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
