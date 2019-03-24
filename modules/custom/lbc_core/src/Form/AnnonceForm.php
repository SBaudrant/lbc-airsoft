<?php

namespace Drupal\lbc_core\Form;

use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\user\UserInterface;

/**
 * Implements a hello admin form.
 */
class AnnonceForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormID() {
    return 'annonce_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['annonce.settings'];
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    // Champ destiné à afficher le résultat du calcul.
    // if (isset($form_state->getRebuildInfo()['result'])) {
    //   $form['result'] = [
    //     '#type' => 'html_tag',
    //     '#tag' => 'h2',
    //     '#value' => $this->t('Result: ') . $form_state->getRebuildInfo()['result'],
    //   ];
    // }
    $title = $body = $prix = $cp = $ville = $departement = $region = $latitude = $longitude = $ville = $tags = $image = $mail = $phone = '';
    if($nid){
      $node = Node::load($nid);

      if( in_array("administrator", $this->currentUser()->getRoles()) || $this->currentUser()->id() == $node->getOwner()->id() ){
        $title = $node->get('title');
        $body = $node->get('body');
        $prix = $node->get('field_prix');
        $cp = $node->get('field_code_postal');
        $ville = $node->get('field_ville');
        $departement = $node->get('field_departement');
        $region = $node->get('field_region');
        $latitude = $node->get('field_latitude');
        $longitude = $node->get('field_longitude');
        $ville = $node->get('field_ville');
        $tags = $node->get('field_annonce_tags');
      }
    }
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title of your ad'),
      '#default_value'=> $title,
      '#required' => TRUE,
    ];
    $validators = array(
      'file_validate_extensions' => array('png, jpeg, jpg'),
    );
    $form['image'] = [
      '#type' => 'file',
      '#title' => $this->t('Image for your ad'),
      // TO DO : get default image
      '#upload_validators' => $validators,
    ];
    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description of your ad'),
      '#default_value'=> $body,
      '#required' => TRUE,
    ];
    $form['prix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateNumeric'],
        'event' => 'change'
      ],
      '#default_value'=> $prix,
      '#required' => TRUE,
      '#prefix' => '<span id="error-message-prix"></span>',
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title'  => $this->t('Phone number'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateNumeric'],
        'event' => 'change',
      ],
      '#default_value'=> $phone,
      '#prefix' => '<span id="error-message-phone"></span>',
    ];
    $form['mail'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Mail'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateMail'],
        'event' => 'change',
      ],
      '#default_value'=> $mail,
      '#prefix' => '<span id="error-message-mail"></span>',
    ];

    $form['codepostal'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Code postal'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['postal_code']
      ],
      '#default_value'=> $cp,
      '#prefix' => '<span id="error-message-code-postal"></span>',
    ];

    $form['ville'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('City'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['locality'],
        'disabled' => true
      ],
      '#default_value'=> $ville,
      '#prefix' => '<span id="error-message-ville"></span>',
    ];

    $form['departement'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Departement'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['administrative_area_level_2'],
        'disabled' => true
      ],
      '#default_value'=> $departement,
      '#prefix' => '<span id="error-message-departement"></span>',
    ];
    $form['region'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Region'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['administrative_area_level_1'],
        'disabled' => true
      ],
      '#default_value'=> $region,
      '#prefix' => '<span id="error-message-region"></span>',
    ];
    $form['latitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['latitude'],
        'class' => ['hidden']
      ],
      '#default_value'=> $latitude,
      //'#attributes' => array( "class" => array("hidden") ),
    ];
    $form['longitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['longitude'],
        'class' => ['hidden']
      ],
      '#default_value'=> $longitude,
      //'#attributes' => array( "class" => array("hidden") ),
    ];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Creer votre annonce'),
    ];
    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function AjaxValidateNumeric(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $field = $form_state->getTriggeringElement()['#name'];

    if (is_numeric($form_state->getValue($field))) {
      $css = ['border' => '2px solid green'];
      $message = $this->t('OK!');
    } else {
      $css = ['border' => '2px solid red'];
      $message = $this->t('%field must be numeric!', ['%field' => $form[$field]['#title']]);
    }

    $response->AddCommand(new CssCommand("[name=$field]", $css));
    $response->AddCommand(new HtmlCommand('#error-message-' . $field, $message));

    return $response;
  }


  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function AjaxValidateMail(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $field = $form_state->getTriggeringElement()['#name'];
    if ( preg_match ( "[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" , $field ) )
    {
      $css = ['border' => '2px solid green'];
      $message = $this->t('OK!');
    } else {
      $css = ['border' => '2px solid red'];
      $message = $this->t('Le mail renseigné n\est pas au bon format', ['%field' => $form[$field]['#title']]);
    }

    $response->AddCommand(new CssCommand("[name=$field]", $css));
    $response->AddCommand(new HtmlCommand('#error-message-' . $field, $message));

    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $title  = $form_state->getValue('title');
    $body   = $form_state->getValue('body');
    $image  = $form_state->getValue('image');
    $mail  = $form_state->getValue('mail');
    $prix  = $form_state->getValue('prix');
    $codepostal  = $form_state->getValue('codepostal');
    $ville  = $form_state->getValue('ville');
    $departement  = $form_state->getValue('departement');
    $region  = $form_state->getValue('region');
    $latitude  = $form_state->getValue('latitude');
    $longitude  = $form_state->getValue('longitude');


    // if (!is_numeric($value_1)) {
    //   $form_state->setErrorByName('value1', $this->t('First value must be numeric!'));
    // }
    // if (!is_numeric($value_2)) {
    //   $form_state->setErrorByName('value2', $this->t('Second value must be numeric!'));
    // }
    // if ($value_2 == '0' && $operation == 'division') {
    //   $form_state->setErrorByName('value2', $this->t('Cannot divide by zero!'));
    // }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, $nid = NULL) {
    // Récupère la valeur des champs.
    $user = \Drupal::currentUser();
    $title        = $form_state->getValue('title') ? $form_state->getValue('title') : '';
    $image        = $form_state->getValue('image') ? $form_state->getValue('image') : '';
    $body         = $form_state->getValue('body') ? $form_state->getValue('body') : '';
    $prix         = $form_state->getValue('prix') ? $form_state->getValue('prix') : '';
    // $phone        = $form_state->getValue('phone') ? $form_state->getValue('phone') : '';
    // $mail         = $form_state->getValue('mail') ? $form_state->getValue('mail') : '';
    $cp           = $form_state->getValue('codepostal') ? $form_state->getValue('codepostal') : '';
    $ville        = $form_state->getValue('ville') ? $form_state->getValue('ville') : '';
    $departement  = $form_state->getValue('departement') ? $form_state->getValue('departement') : '';
    $region       = $form_state->getValue('region') ? $form_state->getValue('region') : '';
    $latitude     = $form_state->getValue('latitude') ? $form_state->getValue('latitude') : '';
    $longitude    = $form_state->getValue('longitude') ? $form_state->getValue('longitude') : '';

    if($nid){
      $node = Node::load($nid);
      if($node->getOwner()->id() == \Drupal::currentUser()->id()){
        $node->set('title' , $title);
        $node->set('body' , $body);
        $node->set('field_prix' , $prix);
        $node->set('field_code_postal' , $cp);
        $node->set('field_ville' , $ville);
        $node->set('field_departement' , $departement);
        $node->set('field_region' , $region);
        $node->set('field_latitude' , $latitude);
        $node->set('field_longitude' , $longitude);
        $node->set('field_ville' , $ville);
        $node->set('field_annonce_tags', $tags);
      } else {
        // TO DO : Set a drupal message to say you are not the creator of this annonce
      }
    } else {
      $node = Node::create([
        'type' => 'annonce',
        'uid' => $user->id(),
        'status' => 1,
        'comment' => 0,
        'promote' => 0,
        'title' => $title,
        'body' => $body,
        'field_prix' => $prix,
        'field_code_postal' => $cp,
        'field_ville' => $ville,
        'field_departement' => $departement,
        'field_region' => $region,
        'field_latitude' => $latitude,
        'field_longitude' => $longitude,
        'field_ville' => $ville,
        'field_annonce_tags' => $tags,

      ]);
    }
    $node->save();

    // On passe le résultat.
    // $form_state->addRebuildInfo('result', $resultat);
    // Reconstruction du formulaire avec les valeurs saisies.
    // $form_state->setRebuild();

    // Enregistrement de l'heure de soumission avec State API.
    // $this->state->set('hello_form_submission_time', $this->time->getCurrentTime());
  }

}
