<tr class="tr_part_number_<?php echo $itemId; ?>">
    <td>
        <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-success btn-xs part_name_cb">
            <input type="checkbox" class="part_name form-control" value="<?php echo $groupItem; ?>" name="part[name][<?php echo $itemId ?>]" data-part-number="<?php echo $itemId; ?>" autocomplete="off">
            <?php echo $groupItem; ?>
        </label>
        </div>
    </td>
    <td>
        <input type="text" disabled class="part_description part_number_<?php echo $itemId; ?>" style="width: 300px; border: 1px solid #333; padding: 3px;" name="part[description][<?php echo $itemId ?>]">
    </td>
    <td>
        <input type="number" disabled class="part_hour part_number_<?php echo $itemId; ?>" style="width: 100px; border: 1px solid #333; padding: 3px;" value="0" name="part[hour][<?php echo $itemId ?>]">
    </td>
    <td>
        <input type="number" disabled class="part_price part_number_<?php echo $itemId; ?>" style="width: 100px; border: 1px solid #333; padding: 3px;" value="0" name="part[price][<?php echo $itemId ?>]">
    </td>
</tr>