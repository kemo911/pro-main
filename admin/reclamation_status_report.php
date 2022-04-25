<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");
$db = DB::getInstance();

$page = isset($_GET['page']) && is_numeric( $_GET['page'] ) ? $_GET['page'] : 0;

if ( $page > 0 ) $page -= 1;

$reclamationNumber = !empty($_GET['reclamation']) ? $_GET['reclamation'] : null;
$estimation = isset($_GET['estimation']) && is_numeric($_GET['estimation']) ? $_GET['estimation'] : null;
$repair_appointment = isset($_GET['repair_appointment']) && is_numeric($_GET['repair_appointment']) ? $_GET['repair_appointment'] : null;
$invoice = isset($_GET['invoice']) && is_numeric($_GET['invoice'])? $_GET['invoice'] : null;
$paid = isset($_GET['paid']) && is_numeric($_GET['paid'])? $_GET['paid'] : null;

$query = '
SELECT r.id, r.reclamation, e.id estimation_id, i.id invoice_id, ad.appointment_id appointment_id, i.payment_status, a.type,
IF( a.type = \'repair\', ad.appointment_id, 0 ) as repair_appointment_id
  FROM reclamation r
  LEFT JOIN estimations e ON e.reclamation_id = r.id
  LEFT JOIN invoice i ON i.id = e.invoice_id AND i.confirm_invoice = 1
  LEFT JOIN appointment_details ad ON ad.reclamation = r.reclamation
  LEFT JOIN appointment a ON a.id = ad.appointment_id 
  ';

$where = " HAVING 1 AND r.reclamation IS NOT NULL AND r.reclamation != '' ";
$whereQuery = '';

if ( $reclamationNumber !== null ) {
    $whereQuery .= " AND r.reclamation LIKE :reclamation ";
}

if ( $estimation !== null ) {
    if ( $estimation ) {
        $whereQuery .= ' AND e.id > 0 ';
    }
    else {
        $whereQuery .= ' AND e.id <= 0 ';
    }
}

if ( $repair_appointment !== null ) {
    if ( $repair_appointment ) {
        $whereQuery .= " AND a.type = 'repair' AND ad.appointment_id > 0 ";
    } else {
        $whereQuery .= " AND repair_appointment_id <= 0 ";
    }
}

if ( $invoice !== null ) {
    if ( $invoice ) {
        $whereQuery .= ' AND i.id > 0 ';
    } else {
        $whereQuery .= ' AND i.id <= 0 ';
    }
}

if ( $paid !== null ) {
    if ($paid ) {
        $whereQuery .= ' AND i.payment_status = 1 ';
    } else {
        $whereQuery .= ' AND i.payment_status = 0 ';
    }
}

if ( $whereQuery ) {
    $query = $query . $where . $whereQuery;
} else {
    $query = $query . $where;
}

$limit = ' LIMIT ' . ($page * 50) . ', 50';

$query .= $limit;

//echo $query;die;

$reclamations = $db->query($query, [':reclamation' => '%' . filter_var($reclamationNumber, FILTER_SANITIZE_STRING) . '%'])->toArray();

$reclamationsArray = [];

foreach ($reclamations as $reclamation) {
    if ( isset($reclamationsArray[$reclamation['reclamation']]) ) {

        if ($reclamation['type'] == 'repair') {
            $reclamationsArray[$reclamation['reclamation']]['repair_appointment_id'] = $reclamation['appointment_id'];
        } else if ($reclamation['type'] == 'estimation') {
            $reclamationsArray[$reclamation['reclamation']]['repair_appointment_id'] = 0;
            $reclamationsArray[$reclamation['reclamation']]['appointment_id'] = $reclamation['appointment_id'];
        }

    } else {
        $reclamationsArray[$reclamation['reclamation']] = $reclamation;
    }

}

$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
include_once('header.php');

?>


    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-uppercase">RAPPORT DU STATUT DES RÉCLAMATIONS</h2>

                <table class="table table-responsive">
                    <tr>
                        <th>Réclamation</th>
                        <th>Estimation</th>
                        <th>Rendez-vous de réparation</th>
                        <th>Facture</th>
                        <th>Payé</th>
                    </tr>

                    <tr>
                        <td>
                            <a data-pid="<?php echo ($_GET['page'] - 1) >= 0 ? ($_GET['page'] - 1) : 0 ; ?>" class="btn btn-xs btn-primary prev" href="javascript:void(0);">Retour</a>
                            |
                            <a data-pid="<?php echo ($_GET['page'] + 1); ?>" class="btn btn-xs btn-warning next" href="javascript:void(0);">Suivant</a>
                            <select class="filterSearch" style="display: none;" id="page">
                                <option id="previousPage" value="<?php echo ($_GET['page'] - 1); ?>">Retour</option>
                                <option selected value="<?php echo $_GET['page']; ?>">Actuel</option>
                                <option id="nextPage"  value="<?php echo ($_GET['page'] + 1); ?>">Suivant</option>
                            </select>
                            <input type="text" id="reclamation" value="<?php echo $reclamationNumber; ?>" placeholder="Réclamation" name="reclamation">
                        </td>
                        <td>
                            <select class="filterSearch" id="estimation">
                                <option <?php echo $estimation == null ? 'selected' : ''; ?> value="NULL">--</option>
                                <option <?php echo $estimation == '1' ? 'selected' : ''; ?> value="1">Oui</option>
                                <option <?php echo $estimation == '0' ? 'selected' : ''; ?> value="0">Non</option>
                            </select>
                        </td>
                        <td>
                            <select class="filterSearch"  id="repair_appointment">
                                <option <?php echo $repair_appointment == null ? 'selected' : ''; ?> value="NULL">--</option>
                                <option <?php echo $repair_appointment == '1' ? 'selected' : ''; ?> value="1">Oui</option>
                                <option <?php echo $repair_appointment == '0' ? 'selected' : ''; ?> value="0">Non</option>
                            </select>
                        </td>
                        <td>
                            <select class="filterSearch"  id="invoice">
                                <option <?php echo $invoice == null ? 'selected' : ''; ?> value="NULL">--</option>
                                <option <?php echo $invoice == '1' ? 'selected' : ''; ?> value="1">Oui</option>
                                <option <?php echo $invoice == '0' ? 'selected' : ''; ?> value="0">Non</option>
                            </select>
                        </td>
                        <td>
                            <select class="filterSearch"  id="paid">
                                <option <?php echo $paid == null ? 'selected' : ''; ?> value="NULL">--</option>
                                <option <?php echo $paid == '1' ? 'selected' : ''; ?> value="1">Oui</option>
                                <option <?php echo $paid == '0' ? 'selected' : ''; ?> value="0">Non</option>
                            </select>
                        </td>
                    </tr>

                    <?php foreach ( $reclamationsArray as $reclamation ):

                        if ( $reclamation['type'] == 'estimation' ) {
                           // $reclamation['appointment_id'] = null;
                        }

                        $estimationURL = 'javascript:void(0);';
                        $estimationLabel = '';
                        $estimationBtnClass = 'btn btn-block btn-primary disabled';

                        if ( $reclamation['estimation_id'] ) {
                            $estimationURL = '/admin/estimation.php#!/estimation/' . $reclamation['estimation_id'];
                            $estimationLabel = 'Voir estimation# ' . $reclamation['estimation_id'];
                            $estimationBtnClass = 'btn btn-block btn-xs btn-warning';
                        }

                        $raURL = 'javascript:void(0);';
                        $raLabel = '';
                        $raBtnClass = 'btn btn-block btn-primary disabled';

                        if ( $reclamation['appointment_id'] ) {
                            $raURL = '/admin/appointment_view.php?appointment_id=' . $reclamation['appointment_id'];
                            $raLabel = 'Rendez-vous de réparation# ' . $reclamation['appointment_id'];
                            $raBtnClass = 'btn btn-block btn-xs btn-yellow';
                        }

                        $inURL = 'javascript:void(0);';
                        $inLabel = '';
                        $inBtnClass = 'btn btn-block btn-primary disabled';

                        //try to find directly in the invoice table (may be the invoice created separately).
                        if (!$reclamation['invoice_id']) {
                            if ( $invoiceRow = getInvoiceByReclamation($reclamation['reclamation'], true) ) {
                                if ($invoiceRow['confirm_invoice'] && $invoiceRow['invoice_type'] == 'invoice') {
                                    $reclamation['invoice_id'] = $invoiceRow['id'];
                                }
                            }
                        }

                        if ( $reclamation['invoice_id'] ) {
                            $inURL = '/admin/main.php?invoice_id=' . $reclamation['invoice_id'];
                            $inLabel = 'Facture #' . $reclamation['invoice_id'];
                            $inBtnClass = 'btn btn-block btn-xs btn-green';
                        }

                        //if ( $reclamation['appointment_id'] === null && $reclamation['invoice_id'] ) continue;

                        $paidURL = 'javascript:void(0);';
                        $paidLabel = '';
                        $paidBtnClass = 'btn btn-block btn-primary disabled';

                        if ( $reclamation['payment_status'] ) {
                            $paidLabel = 'PAYÉ';
                            $paidBtnClass = 'btn btn-block btn-xs btn-pure-green disabled';
                        }

                        ?>
                        <tr>
                            <td>
                                <a href="/admin/reclamation.php?id=<?php echo $reclamation['id']; ?>" class="btn btn-danger btn-xs btn-block"><?php echo '#' . $reclamation['reclamation']; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $estimationURL; ?>" class="<?php echo $estimationBtnClass; ?>"><?php echo $estimationLabel; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $raURL; ?>" class="<?php echo $raBtnClass; ?>"><?php echo $raLabel; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $inURL; ?>" class="<?php echo $inBtnClass; ?>"><?php echo $inLabel; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo $paidURL; ?>" class="<?php echo $paidBtnClass; ?>"><?php echo $paidLabel; ?></a>
                            </td>
                            <td>
                                <?php if ('javascript:void(0);' == $inURL && 'javascript:void(0);' == $raURL && 'javascript:void(0);' == $paidURL) : ?>
                                    <a
                                            class="delete-reclamation"
                                            href="javascript:void(0);"
                                            data-id="<?php echo $reclamation['id']; ?>">
                                        <i class="fa fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            </div>
        </div>
    </div>

    <style>
        .btn-yellow {
            background: #ffdb3b;
            color: white;
        }
        .btn-green {
            color: white;
            background: #4fff27;
        }
        .btn-pure-green {
            color: white;
            background: #98ff00;
        }
    </style>

    <?php
$pageSpecificJS = <<<EOT
<script>
        $(function () {
            $('.filterSearch').change(function () {
                var cur = $(this);
                var filterItem = cur.attr('id');
                var filterItemValue = cur.val();
                
                updateURL(filterItem, filterItemValue);
            });
            $('.prev').click(function() {
                updateURL('page', $(this).data('pid'));
            });
            $('.next').click(function() {
                updateURL('page', $(this).data('pid'));
            });
            
            $('#reclamation').keypress(function(e) {
                if(e.which === 13) {
                    var cur = $(this);
                    var filterItem = cur.attr('id');
                    var filterItemValue = cur.val();
                    updateURL(filterItem, filterItemValue); 
                }
            });
            
            $('.delete-reclamation').click(function() {
                if ( confirm('Are you sure to delete this reclamation?') ) {
                    $.ajax({
                        url: '/admin/ajax/ajax_delete_reclamation.php',
                        type: 'post',
                        data: { rid: $(this).data('id') },
                        success: function(resp) {
                            window.location.reload();
                        },
                        error: function(e) {
                            console.log(e);
                        }
                    });
                }
            });
        });

        function updateURL(key,val){
            var url = window.location.href;
            var reExp = new RegExp("[\?|\&]"+key + "=[0-9a-zA-Z\_\+\-\|\.\,\;]*");

            if(reExp.test(url)) {
                // update
                var reExp = new RegExp("[\?&]" + key + "=([^&#]*)");
                var delimiter = reExp.exec(url)[0].charAt(0);
                url = url.replace(reExp, delimiter + key + "=" + val);
            } else {
                // add
                var newParam = key + "=" + val;
                if(!url.indexOf('?')){url += '?';}

                if(url.indexOf('#') > -1){
                    var urlparts = url.split('#');
                    url = urlparts[0] +  "&" + newParam +  (urlparts[1] ?  "#" +urlparts[1] : '');
                } else {
                    url += "&" + newParam;
                }
            }
            window.history.pushState(null, document.title, url);
            window.location.reload();
        }

        function getQueryVariable(url, variable) {
            var query = url.substring(1);
            var vars = query.split('&');
            for (var i=0; i<vars.length; i++) {
                var pair = vars[i].split('=');
                if (pair[0] == variable) {
                    return pair[1];
                }
            }

            return false;
        }
    </script>
EOT;
;
    ?>

<?php include_once('footer.php'); ?>