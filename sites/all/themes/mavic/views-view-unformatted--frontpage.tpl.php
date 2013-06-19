<?php 
//make the last created background image always appears in first
/*$lastone = array_pop($rows);
shuffle($rows);

array_unshift($rows, $lastone);*/
foreach ($rows as $count => $row): ?>
    <?php print $row; ?>
<?php endforeach; ?>