<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");

//fetching cumulative gpa info
$cum_gpa = 0;
$db = getDB();
$user_id = get_user_id();
$stmt = $db->prepare("SELECT * FROM Grades where Grades.user_id = :user_id");
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
    //$cum_gpa = 2.3;
}
?>

<!--<link rel="stylesheet" href="<?php //echo getURLkxk("static/css/styles.css"); ?>"> -->
<!-- CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<!-- jQuery and JS bundle w/ Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>

<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <ul class="navbar-nav mr-auto">
            <?php if (!is_logged_in()): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("login.php"); ?>">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("register.php"); ?>">Register</a></li>
            <?php endif; ?>
            <?php if (has_role("Admin")): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Admin
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="nav-link text-dark" href="<?php echo getURLkxk("add_semester.php"); ?>">Add Semester</a>
                        <a class="nav-link text-dark" href="<?php echo getURLkxk("admin_list_users.php"); ?>">View All Users</a>
                    </div>
                </li>
            <?php endif; ?>
            <?php if (is_logged_in()): ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("home.php"); ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("input_grades.php"); ?>">Add Grades</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("profile.php"); ?>">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("user_view_grades.php"); ?>">View Grades</a></li>
                <?php if ($cum_gpa < 2.5): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("study_hours.php"); ?>">Study Hours</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo getURLkxk("logout.php"); ?>">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>