<?php

<?php

namespace Drupal\lbc_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Implements a hello admin form.
 */
class UserForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormID() {
    return 'user_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['user.settings'];
  }

// Form d ajout d utilisateurs
function build_form()
{
    global $user, $language;
    $role_actif = _role_user($user);
    $uid    = (int) arg(2);
    $prenom = $nom = $initials = $email = $ville = $departement = $region = $cp  = '';
    $form['#attributes'] = array('id' => 'formAddUser');
    if (!empty($uid)) {
        $u = user_load($uid);
        if ($u->uid) {
            foreach ($roles as $k => $role)
                $index_role          = $k;
            $cp = isset($u->field_code_postal['und'][0]['value']) ? $cp                  = $u->field_code_postal['und'][0]['value'] : $cp                  = '';
            $ville = isset($u->field_ville['und'][0]['value']) ? $u->field_ville['und'][0]['value'] : '';
            $departement = isset($u->field_departement['und'][0]['value']) ? $u->field_departement['und'][0]['value'] : '';
            $region = isset($u->field_region['und'][0]['value']) ? $u->field_region['und'][0]['value'] : '';
        }
    }


    $form['prenom']            = array(
        '#type'          => 'textfield',
        '#title'         => t('First name'),
        '#default_value' => $prenom,
        '#attributes'    => array('class' => array('champForm champTexte champsPrenom'))
    );
    $form['nom']               = array(
        '#type'          => 'textfield',
        '#title'         => t('Last name'),
        '#default_value' => $nom,
        '#attributes'    => array('class' => array('champForm champTexte champsNom'))
    );

    $form['mail']             = array(
        '#type'          => 'textfield',
        '#title'         => t('Email'),
        '#default_value' => $email,
        '#attributes'    => array('class' => array('champForm champTexte champsEmail'))
    );
    $form['password']          = array(
        '#type'       => 'password',
        '#title'      => t('Password'),
        '#attributes' => array('class' => array('champForm champTexte champsPassword'))
    );
    $form['confirm_password']  = array(
        '#type'       => 'password',
        '#title'      => t('Confirm password'),
        '#attributes' => array('class' => array('champForm champTexte champsCPassword'))
    );
    $form['cp']                = array(
        '#type'          => 'textfield',
        '#title'         => t('Zip code'),
        '#default_value' => $cp,
        '#attributes'    => array('class' => array('champForm champTexte champsCP'))
    );
    $form['ville']           = array(
        '#type'          => 'textfield',
        '#title'         => t('Address'),
        '#default_value' => $ville,
        '#attributes'    => array('class' => array('champForm champTexte champsAdresse'))
    );
    $form['departement']   = array(
        '#type'          => 'textfield',
        '#title'         => t('Complementary Address'),
        '#default_value' => $departement,
        '#attributes'    => array('class' => array('champForm champTexte champsAComplet'))
    );
    $form['region']   = array(
        '#type'          => 'textfield',
        '#title'         => t('Complementary Address'),
        '#default_value' => $departement,
        '#attributes'    => array('class' => array('champForm champTexte champsAComplet'))
    );

    $form['prix']              = array(
        '#type'          => 'textfield',
        '#title'         => t('Price of design'),
        '#default_value' => $prix,
        '#attributes'    => array('class' => array('champForm champTexte champsPrix'))
    );

    $terms = entity_load('taxonomy_term', FALSE, array('vid' => 8));

    $product_options = array();
    foreach($terms as $key =>$value){
        $i18n_object = i18n_get_object('taxonomy_term', $key);
        $term = $i18n_object->localize($language->language);
        $product_options[$key] = $term->name;
    }
    $form['produit']           = array(
        '#type'          => 'radios',
        '#title'         => t('Product'),
        '#options'       => $product_options,
        '#default_value' => $produit,
        '#attributes'    => array('class' => array('champForm champRadio champsProduit'))
    );
    if (!empty($uid) && arg(1) != 'duplicate') {
        $form['uid'] = array(
            '#type'  => 'hidden',
            '#value' => $uid,
        );
        $submit      = t('Update');
    } else
        $submit          = t('Validate');
    $form['ajouter'] = array(
        '#type'       => 'submit',
        '#value'      => $submit,
        '#attributes' => array('class' => array('champForm btnForm'))
    );
    return $form;
}

// Validation de form
function _add_user_form_validate($form, &$form_state)
{
    global $user;
    $role_actif = _role_user($user);
    // Recuperation et formatage des donnes de form
    $data       = $form_state['values'];
    isset($data['email']) ? $email      = __filter_html($data['email']) : $email      = '';
    isset($data['uid']) ? $uid        = __filter_html($data['uid']) : $uid        = '';
    // verifie s il n y a pas un compte avec la meme adresse mail
    $user_name  = user_load_multiple(array(), array('name' => $email));
    $user_name  = reset($user_name);
    if (@$user_name->uid && $user_name->uid != $uid) {
        form_set_error($data['email'], t("Un compte avec votre adresse émail existe déjà dans l'intranet."));
        return;
    }
    $user_mail = user_load_multiple(array(), array('mail' => $email));
    $user_mail = reset($user_mail);
    if (@$user_mail->uid && $user_name->uid != $uid) {
        form_set_error($data['email'], t("Un compte avec votre adresse émail existe déjà dans l'intranet."));
        return;
    }
}

// Envoie de form
function _add_user_form_submit($form, &$form_state)
{
    global $language, $user;
    $role_actif        = _role_user($user);
    $lang              = $language->language;
    // Recuperation et formatage des donnes de form
    $data              = $form_state['values'];
    // _p($lang);
    isset($data['status']) ? $status            = __filter_html($data['status']) : $status            = 0;
    isset($data['civilite']) ? $civilite          = __filter_html($data['civilite']) : $civilite          = 'M.';
    isset($data['prenom']) ? $prenom            = __filter_html($data['prenom']) : $prenom            = '';
    isset($data['nom']) ? $nom               = __filter_html($data['nom']) : $nom               = '';
    isset($data['initials']) ? $initials          = __filter_html($data['initials']) : $initials          = '';
    isset($data['societe']) ? $societe           = __filter_html($data['societe']) : $societe           = '';
    isset($data['email']) ? $email             = __filter_html($data['email']) : $email             = '';
    isset($data['password']) ? $pass              = __filter_html($data['password']) : $pass              = '';
    isset($data['role']) ? $index_role        = (int) __filter_html($data['role']) : $index_role        = 4;
    isset($data['adresse']) ? $adresse           = __filter_html($data['adresse']) : $adresse           = '';
    isset($data['adresse-complet']) ? $adresse_complet   = __filter_html($data['adresse-complet']) : $adresse_complet   = '';
    isset($data['cp']) ? $cp                = __filter_html($data['cp']) : $cp                = '';
    isset($data['ville']) ? $ville             = __filter_html($data['ville']) : $ville             = '';
    isset($data['pays']) ? $pays              = __filter_html($data['pays']) : $pays              = '';
    isset($data['notifier_user']) ? $notifier_user     = __filter_html($data['notifier_user']) : $notifier_user     = 0;
    isset($data['prix']) ? $prix              = __filter_html($data['prix']) : $prix              = '';
    isset($data['prix_colorie']) ? $prix_colorie      = __filter_html($data['prix_colorie']) : $prix_colorie      = '';
    isset($data['prix_reimpression']) ? $prix_reimpression = __filter_html($data['prix_reimpression']) : $prix_reimpression = '';
    isset($data['devise']) ? $devise            = __filter_html($data['devise']) : $devise            = '';
    isset($data['tva']) ? $tva               = __filter_html($data['tva']) : $tva               = '';
    isset($data['mode']) ? $mode              = (int) __filter_html($data['mode']) : $mode              = 1;
    isset($data['escompte']) ? $escompte          = __filter_html($data['escompte']) : $escompte          = 0;
    isset($data['mail_compta']) ? $mail_compta       = __filter_html($data['mail_compta']) : $mail_compta       = '';
    isset($data['produit']) ? $produit           = __filter_html($data['produit']) : $produit           = 180;
    isset($data['dessinateur_externe']) ? $dessinateur_externe           = __filter_html($data['dessinateur_externe']) : $dessinateur_externe           = "";
    isset($data['uid']) ? $uid               = __filter_html($data['uid']) : $uid               = '';

    /* Time zone */
    if (date_default_timezone_get())
        $timezone = date_default_timezone_get();
    else
        $timezone = "";
    /* Hash password */
    require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
    if (!empty($pass))
        $pass     = user_hash_password(trim($pass));
    /* Construction de l objet user */
    // si nouveau
    if (empty($uid)) {
        $new_user                   = new stdClass();
        $new_user->language         = LANGUAGE_NONE;
        $new_user->signature_format = 'filtered_html';
        $new_user->timezone         = $timezone;
    } else
        $new_user         = user_load($uid); // si updated
    $new_user->status = $status;
    // user_object_prepare($new_user);
    // _p($index_role);
    if (!empty($uid)) {
        $passmd5 = getUserMD5($uid);
    }
    switch ($index_role) {
        case 3: $role = "administrator";
            break;
        case 4: $role = "client";
            break;
        case 5: $role = "vendeur";
            break;
        case 6: $role = "dessinateur";
            break;
    }
    $new_user->roles = array($index_role => $role);
    $new_user->name  = $email;
    if (!empty($pass))
        $new_user->pass  = $pass;

    $new_user->mail                         = $email;
    $new_user->field_civilit_               = array('und' => array(0 => array('value' => $civilite)));
    $new_user->field_prenom                 = array('und' => array(0 => array('value' => $prenom)));
    $new_user->field_nom                    = array('und' => array(0 => array('value' => $nom)));
    $new_user->field_initials               = array('und' => array(0 => array('value' => $initials)));
    $new_user->field_texte                  = array('und' => array(0 => array('value' => $societe)));
    $new_user->field_adresse                = array('und' => array(0 => array('value' => $adresse)));
    $new_user->field_adresse_compl_mentaire = array('und' => array(0 => array('value' => $adresse_complet)));
    $new_user->field_code_postal            = array('und' => array(0 => array('value' => $cp)));
    $new_user->field_ville                  = array('und' => array(0 => array('value' => $ville)));
    $new_user->field_pays                   = array('und' => array(0 => array('value' => $pays)));
    $new_user->field_notifier_utilisateur   = array('und' => array(0 => array('value' => $notifier_user)));
    $new_user->field_prix_dessin            = array('und' => array(0 => array('value' => $prix)));
    $new_user->field_prix_colorie           = array('und' => array(0 => array('value' => $prix_colorie)));
    $new_user->field_prix_r_impression      = array('und' => array(0 => array('value' => $prix_reimpression)));
    $new_user->field_devise                 = array('und' => array(0 => array('value' => $devise)));
    $new_user->field_numero_tva             = array('und' => array(0 => array('value' => $tva)));
    $new_user->field_mode_reglement         = array('und' => array(0 => array('value' => $mode)));
    $new_user->field_escompte               = array('und' => array(0 => array('value' => $escompte)));
    $new_user->field_email_comptabilit_     = array('und' => array(0 => array('value' => $mail_compta)));
    $new_user->field_produit                = array('und' => array(0 => array('tid' => $produit)));
    $new_user->field_dessinateur_externe    = array('und' => array(0 => array('value' => $dessinateur_externe)));
    user_save($new_user);

    //Set field passmds5
    if (!empty($uid)) { //si Update
        if (empty($pass)) { //si le mot de passe n'a pas était saisie
            $result = db_query("UPDATE {users} SET updated = :datetimestamp , passmd5 = :pass WHERE uid = :id", array(':datetimestamp' => time(), ':pass' => strval($passmd5), ':id' => $uid));
        }
    }
    if ($new_user->uid) {
        if ($notifier_user)
            _user_mail_notify('register_admin_created', $new_user, $lang);
        if (empty($uid))
            drupal_set_message(t('La création du compte de @prenom @nom a été bien faite', array('@prenom' => $prenom, '@nom' => $nom)));
        else
            drupal_set_message(t('La modification du compte de @prenom @nom a été bien faite', array('@prenom' => $prenom, '@nom' => $nom)));
        if ($role_actif == 'vendeur')
            drupal_goto('clients');
        else
            drupal_goto('users');
    } else
        drupal_set_message(t('An error has occurred. Please try again later'), 'error');
}
