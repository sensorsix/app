<table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
  <thead>
    <tr>
      <th>Alternative</th><th>Criteria</th><th>Comment</th><th>User</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($comments as $comment) : ?>
      <tr>
        <td><?php echo $comment->relatedExists('Alternative') ? $comment->Alternative->__toString() : '' ?></td>
        <td><?php echo $comment->Criterion->__toString() ?></td>
        <td><?php echo $comment->getRawValue()->text ?></td>
        <td><?php echo $comment->relatedExists('User') ? $comment->User->email_address : $comment->email ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>


 