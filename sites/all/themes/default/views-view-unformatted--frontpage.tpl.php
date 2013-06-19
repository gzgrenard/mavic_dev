views-view-table--frontpage.tpl.php<br>
<table class="<?php print $class; ?>">
  <tbody>
    <?php foreach ($rows as $count => $row): ?>
      <tr>
          <?php print $row; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>fin views-view-table--frontpage.tpl.php