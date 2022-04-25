<?php header('Content-Type: charset=utf-8'); ?>
<?php



include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');

include_once(dirname(dirname(__FILE__)) . '/admin/classes/add_user.class.php');

protect("Admin");

init_message_bags();

$client = array();

global $generic;



if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    $post = filter_var_array($_POST, FILTER_SANITIZE_STRING);



    $name = $post['first_name'] . ' ' . $post['last_name'];



    if ( isset($post['user_id']) && is_numeric($post['user_id']) && $post['user_id'] > 0 ) {

        $params = array(

            ':name'       => $name,

            ':email'      => $post['email'],

            ':username'   => $post['username'],

        );



        if ( !empty(trim($post['user_level'])) ) {

            $params[':user_level'] = serialize(array(

                $post['user_level']

            ));

        } else {

            $params[':user_level'] = $generic->getOption('default-level');

        }



        if ( !empty(trim($post['password'])) ) {

            $params[':password'] = $generic->hashPassword($post['password']);

        } else {

            $skip_password = 1;

        }



        foreach ( array(

                      'first_name',

                      'last_name',

                      'tel1',

                      'tel2',

                      'address',

                      'social_insurance',

                      'tech_code',

                      'company_name',

                      'note'

                  )

                  as $key ) {

            if ( !empty($post[$key]) ) {

                $params[':' . $key] = $post[$key];

            } else {

                $params[':' . $key] = '';

            }

        }



        $params[':user_id'] = $post['user_id'];



        if ( !empty($skip_password) ) {

            $query = 'UPDATE login_users SET 

          name = :name,

          user_level = :user_level,

          email = :email,

          username = :username,

          first_name = :first_name,

          last_name = :last_name,

          tel1 = :tel1,

          tel2 = :tel2,

          address = :address,

          social_insurance = :social_insurance,

          tech_code = :tech_code,

          company_name = :company_name,

          note = :note

          WHERE user_id = :user_id';

        } else {

            $query = 'UPDATE login_users SET 

          name = :name,

          user_level = :user_level,

          email = :email,

          username = :username,

          password = :password,

          first_name = :first_name,

          last_name = :last_name,

          tel1 = :tel1,

          tel2 = :tel2,

          address = :address,

          social_insurance = :social_insurance,

          tech_code = :tech_code,

          company_name = :company_name,

          note = :note

          WHERE user_id = :user_id';

        }



        $rs = $generic->query($query, $params);



        if ( ! $rs->errorInfo()[2] ) {

            $mg = sprintf(_('Successfully updated user <b>%s</b> to the database.'), $post['username']);

            put_flash_message('message', $mg);

        } else {

            put_flash_message('message', 'Opps! User not updated. Reason: ' . json_encode($rs->errorInfo()[2] ) );

        }

    } else {

        $params = array(

            ':name'       => $name,

            ':email'      => $post['email'],

            ':username'   => $post['username'],

        );



        if ( !empty(trim($post['user_level'])) ) {

            $params[':user_level'] = serialize(array(

                $post['user_level']

            ));

        } else {

            $params[':user_level'] = $generic->getOption('default-level');

        }



        $pass = $generic->hashPassword($post['password']);

        if ( !empty(trim($post['password'])) ) {

            $params[':password'] = $pass;

        }



        foreach ( array(

                      'first_name',

                      'last_name',

                      'tel1',

                      'tel2',

                      'address',

                      'social_insurance',

                      'tech_code',

                      'company_name',

                      'note'

                  )

                  as $key ) {

            if ( !empty($post[$key]) ) {

                $params[':' . $key] = $post[$key];

            } else {

                $params[':' . $key] = '';

            }

        }



        $rs = $generic->query("INSERT INTO `login_users` (`user_level`, `name`, `email`, `username`, `password`, `first_name`, `last_name`,`tel1`,`tel2`,`address`,`social_insurance`,`tech_code`, `company_name`, `note`)

						VALUES (:user_level, :name, :email, :username, :password, :first_name, :last_name, :tel1, :tel2, :address, :social_insurance, :tech_code, :company_name, :note);", 

            $params);



        if ( $rs ) {

            $shortcodes = array(

                'site_address'	=>	SITE_PATH,

                'full_name'		=>	$name,

                'username'		=>	$post['username'],

                'email'			=>	$post['email'],

                'password'		=>	$post['password'],

            );



            $subj = $generic->getOption('email-add-user-subj');

            $msg = $generic->getOption('email-add-user-msg');



            if(!$generic->sendEmail($post['email'], $subj, $msg, $shortcodes))

                $mg = _('ERROR. Mail not sent');



            $mg = sprintf(_('Successfully added user <b>%s</b> to the database. Credentials sent to user.'), $post['username']);

            put_flash_message('message', $mg);

        } else {

            put_flash_message('message', 'Opps! User not created.');

        }

    }

}



if ( !empty($_GET['user_id']) && is_numeric($_GET['user_id']) ) {



    $stmt = $generic->query('SELECT * FROM login_users WHERE user_id = :user_id', array(':user_id' => $_GET['user_id']));

    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( !$client )

        $client = array();

}





include_once('header.php');

$message = flush_message('message');

?>



    <div class="container">



        <div class="row">

            <div class="col-md-12">

                <div id="message">

                    <div class="alert <?php echo !empty($message) ? 'alert-info' : ''; ?>">

                        <?php echo $message; ?>

                    </div>

                </div>

            </div>

        </div>



        <form id="user_add_form" method="post" action="">

            <input type="hidden" name="user_id" value="<?echo isset($_GET['user_id']) ? $_GET['user_id'] : null; ?>">

        <div class="row">

            <div class="col-md-6">

                <div class="panel panel-primary">

                    <div class="panel-heading">

                        <h2 class="text-center text-uppercase text-"><strong><?php echo !empty($client['user_id']) ? 'Modifier ' : 'Créer '; ?> Profile d'uitilisateur</strong></h2>

                    </div>

                    <div class="panel-body">

                        <?php

                        $fields = array(

                            'text' => array(



                                'company_name' => array(

                                    'label' => 'Compagnie',

                                    //'required' => true,

                                ),



                                'first_name' => array(

                                    'label' => 'Prénom',

                                    //'required' => true,

                                ),



                                'last_name' => array(

                                    'label' => 'Nom',

                                    //'required' => true,

                                ),



                                'email' => array(

                                    'label' => 'Courriel',

                                    'required' => true,

                                    'type' => 'email',

                                    'remote' => array(

                                        "data-parsley-error-message" => "Assurez vous que votre courriel est unique.",

                                        'data-parsley-remote' => '/admin/ajax/remote_validation.php?validationAction=email_uniqueness_create&email={value}' . ( isset($_GET['user_id']) ? '&skip=' . $_GET['user_id'] : '' ),

                                        'data-parsley-email-message' => 'Entrez un courriel valide.'

                                    )

                                ),



                                'tel1' => array(

                                    'label' => 'Téléphone 1',

                                    //'required' => true,

                                ),



                                'tel2' => array(

                                    'label' => 'Téléphone 2',

                                ),



                                'address' => array('label' => 'Adresse', /*'required' => true,*/ ),

                                'social_insurance' => array('label' => 'Assurance social', /*'required' => true,*/),

                                'tech_code' => array('label' => 'Code Tech', /*'required' => true,*/),

                            ),



                            'textarea' => array(

                                'note' => array(

                                    'label' => 'Note',

                                ),

                            ),



                            'text2' => array(

                                'username' => array(

                                    'label' => 'Nom d\'utilisateur',

                                    'remote' => array(

                                        "data-parsley-error-message" => "Le nom d'utilisateur doit-être unique.",

                                        'data-parsley-remote' => '/admin/ajax/remote_validation.php?validationAction=username_uniqueness_create&username={value}' . ( isset($_GET['user_id']) ? '&skip=' . $_GET['user_id'] : '' ),

                                    )

                                ),

                            ),

                            'pass' => array(

                                'password' => array('label' => 'Mot de passe'),

                            )

                        );



                        $form_errors = get_errors();



                        ?>

                        <?php foreach ( $fields['text'] as $field => $details ): ?>

                            <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">

                                <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>

                                <input type="<?php echo isset($details['type']) ? $details['type'] : 'text' ?>"

                                       name="<?php echo $field; ?>"

                                       <?php if ( isset($details['required']) && $details['required'] == true ) echo ' required '; ?>

                                       <?php if ( !empty($details['remote']) ) {

                                                foreach ( $details['remote'] as $attr => $attrValue ) {

                                                    echo  " $attr " . "=" . "\"$attrValue\"";

                                                }

                                            }

                                       ?>

                                       value="<?php echo !empty($client[$field]) ? $client[$field] : null; ?>" class="form-control" id="<?php echo $field; ?>">

                                <span class="help-block">

                                    <?php if ( isset($form_errors[$field]) ): ?>

                                        <?php echo $form_errors[$field]; ?>

                                    <?php endif; ?>

                                </span>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

            <div class="col-md-6">



                <div class="panel panel-primary">

                    <div class="panel-heading">

                        <h2 class="text-center text-uppercase text-"><strong>Connexion</strong></h2>

                    </div>

                    <div class="panel-body">



                        <?php foreach ( $fields['text2'] as $field => $details ): ?>

                            <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">

                                <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>

                                <input type="text" required

                                       name="<?php echo $field; ?>"

                                       value="<?php echo !empty($client[$field]) ? $client[$field] : null; ?>" class="form-control"

                                        <?php if ( !empty($details['remote']) ) {

                                            foreach ( $details['remote'] as $attr => $attrValue ) {

                                                echo  " $attr " . "=" . "\"$attrValue\"";

                                            }

                                        }

                                        ?>

                                       id="<?php echo $field; ?>">

                                <span class="help-block">

                                    <?php if ( isset($form_errors[$field]) ): ?>

                                        <?php echo $form_errors[$field]; ?>

                                    <?php endif; ?>

                                </span>

                            </div>

                        <?php endforeach; ?>



                        <?php foreach ( $fields['pass'] as $field => $details ): ?>

                            <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">

                                <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>

                                <input type="password" <?php echo (!empty($_GET['user_id']) && is_numeric($_GET['user_id'])) ? '' : 'required'; ?> name="<?php echo $field; ?>" class="form-control" id="<?php echo $field; ?>">

                                <span class="help-block">

                                    <?php if ( isset($form_errors[$field]) ): ?>

                                        <?php echo $form_errors[$field]; ?>

                                    <?php endif; ?>

                                </span>

                            </div>

                        <?php endforeach; ?>



                        <div class="form-group">

                            <label for="user_level" class="control-label">Type de compte:</label><br>



                            <?php

                                $ulvl = 5;

                                if ( !empty($client) ) {

                                    $user_level = unserialize($client['user_level']);

                                    foreach ( $user_level as $level ) {

                                        $ulvl = $level; break;

                                    }

                                }

                            ?>



                            <div class="btn-group" data-toggle="buttons">

                                <label class="btn btn-xs btn-success <?php echo $ulvl == 1 ? 'active' : '' ?>">

                                    <input type="radio" name="user_level" id="user_level1" value="1" <?php echo $ulvl == 1 ? 'checked' : '' ?> autocomplete="off"> Admin

                                </label>

                                <label class="btn btn-xs btn-success <?php echo $ulvl == 2 ? 'active' : '' ?>">

                                    <input type="radio"  name="user_level" id="user_level2" value="2" <?php echo $ulvl == 2 ? 'checked' : '' ?> autocomplete="off"> Expert Tech

                                </label>

                                <label class="btn btn-xs btn-success <?php echo $ulvl == 3 ? 'active' : '' ?>">

                                    <input type="radio"  name="user_level" id="user_level3" value="3" <?php echo $ulvl == 3 ? 'checked' : '' ?> autocomplete="off"> Estimateur

                                </label>

                               

                            </div>

                        </div>



                        <?php foreach ( $fields['textarea'] as $field => $details ): ?>

                            <div class="form-group <?php echo isset($form_errors[$field]) ? 'has-error' : null; ?>">

                                <label class="control-label" for="<?php echo $field; ?>"><?php echo $details['label']; ?></label>

                                <textarea name="<?php echo $field; ?>" style="min-height: 150px;" class="form-control" id="<?php echo $field; ?>"><?php echo !empty($client[$field]) ? $client[$field] : null; ?></textarea>

                                <span class="help-block">

                                    <?php if ( isset($form_errors[$field]) ): ?>

                                        <?php echo $form_errors[$field]; ?>

                                    <?php endif; ?>

                                </span>

                            </div>

                        <?php endforeach; ?>



                    </div>

                    <div class="panel-footer">

                        <h2 class="text-center text-muted"><a class="btn btn-link" href="/admin/users-levels.php">Voir la liste de utilisateurs</a></h2>

                    </div>

                </div>



            </div>

            <div class="col-md-12 text-center">

                <button class="btn-lg text-uppercase btn btn-primary"><i class="glyphicon glyphicon-user"></i> Sauvegarder</button>

            </div>

        </div>

        </form>

    </div>



<?php



include_once('footer.php');

