<?php if ( isset($reclamation) ): ?>
<div class="car-info" style="margin-top: 12px;">
    <strong>VIN:</strong> <?php echo $reclamation['vin']; ?>
    <strong>Marque:</strong> <?php echo $reclamation['brand']; ?>
    <strong>Modèle:</strong> <?php echo $reclamation['model']; ?>
    <strong>Année:</strong> <?php echo $reclamation['year']; ?>
    <strong>P.A:</strong> <?php echo $reclamation['particular_area']; ?>
    <strong>B.T:</strong> <?php echo $reclamation['brake_type']; ?>
    <strong>PLAQUE:</strong> <?php echo !empty($reclamation['inventory']) ? $reclamation['inventory'] : ''; ?>
    <strong>Couleur:</strong> <?php echo !empty($reclamation['color']) ? $reclamation['color'] : ''; ?>
    <strong>Odométre:</strong> <?php echo !empty($reclamation['millage']) ? $reclamation['millage'] : ''; ?>
</div>
<?php endif; ?>

