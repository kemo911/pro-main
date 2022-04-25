<?php
include_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../classes/functions.php';

if (isset($_POST['action'])) {
    $conn = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass);

    $action = $_POST['action'];

    switch ( $action ) {
        case 'list_client_by_search_token':
            $token = $_POST['token'];
            $stmt = $conn->prepare('SELECT * FROM clients WHERE fname LIKE :token OR lname ORDER BY fname ASC LIMIT 10');
            $stmt->execute(array(':token' => '%' . $token . '%'));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            displayListAsResponse( 'list_client_by_search_token', $results );
            break;
        case 'get_invoice_photo':
            $photos = getInvoicePhotos( $_POST['invoice_id'] );
            $html = '';
            ini_set('display_errors', 0);
            foreach ( $photos as $photo ) { ?>

                    <div style="display: inline-block;" id="photo-div-<?php echo $photo['id']; ?>">
                        <span>
                            <a href="/<?php echo $photo['photo_url']; ?>" data-lightbox="image-<?php echo $photo['id']; ?>">
                            <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>" src="/<?php echo $photo['photo_url']; ?>" width="100px">
                        </a>
                        </span>
                        <br>
                        <span style="color: red; font-size: 12px; text-align: center !important; text-decoration: underline; cursor: pointer;" data-action="delete_invoice_photo" class="delete-photo" data-alt="<?php echo end(explode('/', $photo['photo_url'])); ?>" data-id="<?php echo $photo['id']; ?>">Delete</span>
                    </div>

            <?php
            }
            echo $html;
            break;
        case 'get_mold_photo':
            $photos = getMoldPhotos( $_POST['mold_id'] );
            $html = '';
            ini_set('display_errors', 0);
            foreach ( $photos as $photo ) {
                $pp = explode('/', $photo['photo_url']);
                ?>

                <div style="display: inline-block;" id="photo-div-<?php echo $photo['id']; ?>">
                        <span>
                            <a href="/<?php echo $photo['photo_url']; ?>" data-lightbox="image-<?php echo $photo['id']; ?>">
                            <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>" src="/<?php echo $photo['photo_url']; ?>" width="100px">
                        </a>
                        </span>
                    <br>
                    <span style="color: red; font-size: 12px; text-align: center !important; text-decoration: underline; cursor: pointer;" data-action="delete_mold_photo" class="delete-photo" data-alt="<?php echo end($pp); ?>" data-id="<?php echo $photo['id']; ?>">Delete</span>
                </div>

                <?php
            }
            echo $html;
            break;
        case 'delete_invoice_photo':
            echo json_encode( ['status' => deleteInvoicePhotos( $_POST['id'] )] );
            break;
        case 'delete_mold_photo':
            echo json_encode( ['status' => deleteMoldPhotos( $_POST['id'] )] );
            break;
    }
}


function displayListAsResponse( $listId, $data ) {
    $html = '';
    switch ( $listId ) {
        case 'list_client_by_search_token':
            foreach ( $data as $singleItem ) {
                $html .= '<a 
                    data-client-id="'.$singleItem['clientid'].'" 
                    data-client-fname="'.$singleItem['fname'].'"
                    data-client-lname="'.$singleItem['lname'].'"
                    data-client-email="'.$singleItem['email'].'"
                    data-client-tel="'.$singleItem['tel1'].'"
                    class="list-group-item selectClient" href="javascript:void(0);
                    ">'.$singleItem['fname'] .' '. $singleItem['lname'] .'</a>';
            }
            break;
    }
    echo $html;
    exit;
}
