<?php require_once(__DIR__. "/partials/nav.php");
if (!has_role("Admin")) {
    flash("Study Hours page is currently under construction.");
    die(header("Location: home.php"));
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
