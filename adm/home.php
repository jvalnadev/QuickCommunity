<?php if(!defined('QCOM1'))exit() ?>
<?php

if (!$this->sess->isAdmin()) {
    header('location:./');
    exit();
}

?>
<a class="link" href="./"><b>Back To Website >></b></a>
<br><br>
Welcome to your Admin interface.<br>
Here you can make changes.<br>
Enjoy!
<br>
You Can:
<br>
<form class="link" method="post">
    <input class="link" type="submit" value="Manage Members">
    <input type="hidden" name="act" value="members">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Add New Forum">
    <input type="hidden" name="act" value="forumadd">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Set Forum Order">
    <input type="hidden" name="act" value="forumorder">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Rename Forum">
    <input type="hidden" name="act" value="forumrename">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Delete Forum">
    <input type="hidden" name="act" value="forumdelete">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Delete Post">
    <input type="hidden" name="act" value="postdelete">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Delete Topic">
    <input type="hidden" name="act" value="topicdelete">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Change Style">
    <input type="hidden" name="act" value="styles">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Change Language">
    <input type="hidden" name="act" value="languages">
</form><br>
<form class="link" method="post">
    <input class="link" type="submit" value="Change Timezone">
    <input type="hidden" name="act" value="timezones">
</form><br>
<br>
