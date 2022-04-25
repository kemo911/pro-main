<?php
$lable_style = 'font-weight: bold; display: inline-block; padding: 0 10px; width: 150px;';
$span_style = 'font-weight: bold; display: inline-block; padding: 0 10px; font-style: italic;';
?>
<div style="max-width: 700px;">
Bonjour <?php echo $appointment['client_name']; ?>,
Voici les informations concernant votre rendez-vous:

    <div style="background: #000; color: #fff; font-size: 18px; padding: 5px 10px; font-weight: bold;">Details</div>
    <div><span style="<?php echo $lable_style; ?>">Date:</span> <?php echo date('F j, Y', strtotime($appointment['date'])); ?></div>
    <div><span style="<?php echo $lable_style; ?>">Nom du client:</span> <?php echo $appointment['client_name']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Compagnie:</span> <?php echo $appointment['cie']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Assurreur:</span> <?php echo $appointment['insurer']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Réclamation:</span> <?php $appointment['reclamation']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Courriel:</span> <?php echo $appointment['email']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Téléphone:</span> <?php echo $appointment['tel1']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">VIN:</span> <?php $appointment['vin']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Marque:</span> <?php $appointment['brand']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Année:</span> <?php echo $appointment['year']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Couleur:</span> <?php echo $appointment['color']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">PA:</span> <?php echo $appointment['particular_area']; ?>
    </div>
    <div><span style="<?php echo $lable_style; ?>">Modèle:</span> <?php $appointment['model']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Inventaire:</span> <?php echo $appointment['inventory']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">BT:</span> <?php echo $appointment['brake_type']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Millage:</span> <?php echo $appointment['millage']; ?></div>
    <!--START: Schedule-->
    <div style="background: #000; color: #fff; font-size: 18px; padding: 5px 10px; font-weight: bold;">Horraire</div>
    <div style="font-weight: bold; padding: 0 10px; font-size: 17px;"><?php echo ucfirst($appointment['type']); ?> Rendez-vous</div>
    <div><span style="<?php echo $lable_style; ?>">Estimateurr/Tech:</span> <?php $appointment['tech_name']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Adresse:</span> <?php echo $appointment['schedule_address']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Heure:</span> <?php echo $appointment['start_time']; ?> to <?php echo $appointment['end_time']; ?></div>
    <div><span style="<?php echo $lable_style; ?>">Note:</span> <?php echo $appointment['notes']; ?></div>
    <?php if($appointment['checkbox_not_presented'] == 1): ?>
    <span style="<?php echo $span_style; ?> " >Ne cest pas presente</span>
    <?php endif; ?>
    <?php if($appointment['checkbox_total_loss'] == 1): ?>
    <span style="<?php echo $span_style; ?> " >Pere total</span>
    <?php endif; ?>
    <?php if($appointment['checkbox_want_repair_appointment'] == 1): ?>
    <span style="<?php echo $span_style; ?> " >Ne veux pas fixer de rdv pour la reparation</span>
    <?php endif; ?>
    <?php if($appointment['checkbox_monetary_compensation'] == 1): ?>
    <span style="<?php echo $span_style; ?> " >Compensation moniaitaire</span>
    <?php endif; ?>
    <?php if($appointment['checkbox_call_back_for_appointment'] == 1): ?>
    <span style="<?php echo $span_style; ?> ">Call back for appointment</span>
    <?php endif; ?>
    </div>

Thank you

Best regards,
Bosseesg Team
</div>