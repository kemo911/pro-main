<?php
/**
 * Created by PhpStorm.
 * User: Kajem
 * Date: 1/23/2018
 * Time: 2:21 AM
 */
include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
protect("*");
include_once 'header.php';
?>
<div class="container">
    <div class="report-items text-center content">
        <div class="row">
            <?php
if ( protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3])) ): ?>
            <div class="col-md-4">
                <a href="/admin/reclamation_status_report.php?page=1">
                    <img src="/admin/assets/img/parts.jpg" alt="Estimation"/><br/>
                    <span>RÉCLAMATIONS</span>
                </a>
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <a href="/admin/estimation_report.php">
                    <img src="/admin/assets/img/estimation.jpg" alt="Estimation"/><br/>
                    <span>ESTIMATIONS</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/admin/estimation_no_show_report.php?no=1">
                    <img src="/admin/assets/img/estimation.jpg" alt="Estimation"/><br/>
                    <span>ESTIMATION RENDEZ-VOUS MANQUÉ</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/admin/invoice_report.php">
                    <img src="/admin/assets/img/invoice.jpg" alt="Estimation"/><br/>
                    <span>FACTURES</span>
                </a>
            </div>
            <?php if( protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3])) ) : ?>
            <div class="col-md-4">
                <a href="/admin/parts_report.php">
                    <img src="/admin/assets/img/parts.jpg" alt="Estimation"/><br/>
                    <span>PIÈCES</span>
                </a>
            </div>
            <?php else : ?><?php endif; ?>
        </div>
    </div>
</div>
<?php include_once('footer.php'); ?>
