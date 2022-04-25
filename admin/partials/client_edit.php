<?php
    $fields = array(
        'text' => array(

            'fname' => array(
                'label' => 'Prénom',
            ),

            'lname' => array(
                'label' => 'Nom',
            ),

            'address' => array(
                'label' => 'Adresse',
            ),

            'cie' => array(
                'label' => 'Compagnie',
            ),

            'email' => array(
                'label' => 'Courriel',
            ),

            'tel1' => array(
                'label' => 'Téléphone 1',
            ),

            'tel2' => array(
                'label' => 'Téléphone 2',
            ),
        ),

        'textarea' => array(
            'note' => array(
                'label' => 'Note',
            ),
        )
    );

    $form_errors = get_errors();

?>




    <input type="hidden" name="action" value="edit_client">
    <input type="hidden" name="client_id" value="<?php echo !empty($client['clientid']) ? $client['clientid'] : null; ?>">

    <?php foreach ( $fields['text'] as $field => $details ): ?>
        <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">
            <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>
            <input type="text" name="<?php echo $field; ?>" value="<?php echo !empty($client[$field]) ? $client[$field] : null; ?>" class="form-control" id="<?php echo $field; ?>">
            <span class="help-block">
                <?php if ( isset($form_errors[$field]) ): ?>
                    <?php echo $form_errors[$field]; ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endforeach; ?>

    <?php foreach ( $fields['textarea'] as $field => $details ): ?>
        <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">
            <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>
            <textarea name="<?php echo $field; ?>" class="form-control" id="<?php echo $field; ?>"><?php echo !empty($client[$field]) ? $client[$field] : null; ?></textarea>
            <span class="help-block">
                <?php if ( isset($form_errors[$field]) ): ?>
                    <?php echo $form_errors[$field]; ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endforeach; ?>
<?php flush_errors(); ?>