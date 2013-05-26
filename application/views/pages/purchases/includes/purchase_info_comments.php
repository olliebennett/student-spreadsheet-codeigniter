    <table class="purchase-comments" class="table table-bordered">
      
      <thead>
        <tr>
          <th>Author</th>
          <th>Comment</th>
          <th>Date</th>
        </tr>
      </thead>
      
      <tbody>
        
        <?php foreach ($purchase['comments'] as $comment) : ?>
        <?php $t = strtotime($comment['added_time']); ?>
        
        <tr>
          <td><?php echo $housemates[$comment['added_by']]['user_name']; ?></td>
          <td><?php echo $comment['text']; ?></td>
          <td>
            <time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', $t); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', $t); ?></time>
          </td>
        </tr>
        
        <?php endforeach; ?>
        
      </tbody>
      
    </table>