<?php include_once('admin.php'); ?>

<?php
//ALTER TABLE `login_users` ADD `first_name` VARCHAR(100) NOT NULL AFTER `sms_time`, ADD `last_name` VARCHAR(100) NOT NULL AFTER `first_name`, ADD `company_name` VARCHAR(200) NOT NULL AFTER `last_name`, ADD `tel1` VARCHAR(20) NOT NULL AFTER `company_name`, ADD `tel2` VARCHAR(20) NOT NULL AFTER `tel1`, ADD `social_insurance` VARCHAR(100) NOT NULL AFTER `tel2`, ADD `tech_code` VARCHAR(100) NOT NULL AFTER `social_insurance`;
    global $generic;
    $query = $generic->query('SELECT * FROM login_users ORDER BY timestamp DESC');
    $allLevels = get_user_levels();

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2 class="text-center text-uppercase text-"><strong>Liste des utilisateur</strong></h2>
                <br>
                <p><a class="btn btn-default" href="/admin/user.php">Ajouter un utilisateur</a></p>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="user_lists_dt" class="table table-striped table-bordered dt-responsive" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Nom</th>
                                <th>Compagnie</th>
                                <th></th>
                            </tr>
                        </thead>
                        <?php if ( $query->rowCount() ): ?>
                        <tbody>
                        <?php
                            while($row = $query->fetch(PDO::FETCH_ASSOC)):
                                $user_level = unserialize($row['user_level']);
                                $ulvl = null;
                        ?>
                            <tr>
                                <td><?php echo $row['username']; ?></td>
                                <td>
                                    <?php foreach ( $user_level as $level ): $ulvl = $level; ?>
                                        <?php echo $allLevels[$level]; break; ?>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['company_name']; ?></td>
                                <td>
                                    <a href="/admin/user.php?user_id=<?php echo $row['user_id']; ?>" ><i class="glyphicon glyphicon-edit"></i></a> <?php if( $ulvl > 1 ): ?> | <a onclick="return confirm('Are you really want to delete?');" href="/admin/delete_user.php?user_id=<?php echo base64_encode($row['user_id']); ?>"><i class="glyphicon glyphicon-trash"></i></a> <?php endif; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>