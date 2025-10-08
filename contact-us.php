<?php
// contact-us page 
ob_start();
?>
<div class="contactMain">
    <div class="img  w-[80%] bg-red-200 h-20">
 <!-- <img class="" src="asetes/images/baby-3.png" alt="">
   -->
 <div class="contact-form">
    <input type="text">
    <input type="text">
    <input type="text">
    <input type="text">
    <input type="text">
 </div>
    </div>
</div>











<?php
$content = ob_get_clean();
include 'layout.php';
?>