<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

$db = DB::getInstance();

$photos = getAppointmentPhotosByToken($_POST['token']);

foreach ( $photos as $photo ) { ?>

    <div style="display: inline-block;" id="photo-div-<?php echo $photo['id']; ?>">
                        <span>
                            <a href="/<?php echo $photo['photo_url']; ?>" data-lightbox="image-<?php echo $photo['id']; ?>">
                            <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>" src="/<?php echo $photo['photo_url']; ?>" width="100px">
                        </a>
                        </span>
        <br>
        <?php $a = explode('/', $photo['photo_url']); ?>
        <span style="color: red; font-size: 12px; text-align: center !important; text-decoration: underline; cursor: pointer;" class="delete-app-photo" data-alt="<?php echo end($a); ?>" data-id="<?php echo $photo['id']; ?>">Delete</span>
    </div>

    <?php
}
