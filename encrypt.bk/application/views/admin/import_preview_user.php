<style>
.duplicate_data{ color:#e91e63;}
.duplicate_data input[type="text"] {
    color: #e91e63;
}
.invalid_row{ color:#ff5722;}
.invalid_row input[type="text"] {
    color: #ff5722;
}

</style>
Color for <b class="invalid_row">Invalid Row</b><br />
Color for <b class="duplicate_data">Duplicate Data</b><br />
<form action="<?php echo $action ?>" method="POST">
<table>
<thead>
    <tr>
            <th>SL.Number</th>
        <?php foreach($excell['headers'] as $headers): ?>
            <th><?php echo $headers ?></th>
        <?php endforeach; ?>
    </tr>
</thead>
<tbody>
    <?php foreach($excell['content'] as $key => $content): ?>
        <tr class="<?php echo $content['type'] ?>">
            <td><?php echo $content['row_number'] ?></td>
            <?php foreach($content['row'] as $row_key => $value): ?>
                <td><input type="text" name="content[<?php echo $key ?>][row][<?php echo $row_key ?>]" value="<?php echo $value ?>"></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<input type="submit" name="import" value="IMPORT">
</form>
