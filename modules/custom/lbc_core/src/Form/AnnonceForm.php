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
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Champ destiné à afficher le résultat du calcul.
    // if (isset($form_state->getRebuildInfo()['result'])) {
    //   $form['result'] = [
    //     '#type' => 'html_tag',
    //     '#tag' => 'h2',
    //     '#value' => $this->t('Result: ') . $form_state->getRebuildInfo()['result'],
    //   ];
    // }
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title of your ad'),
      '#required' => TRUE,
    ];
    $validators = array(
      'file_validate_extensions' => array('png, jpeg, jpg'),
    );
    $form['image'] = [
      '#type' => 'file',
      '#title' => $this->t('Image for your ad'),
      '#upload_validators' => $validators,
    ];
    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description of your ad'),
      '#required' => TRUE,
    ];
    $form['prix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateNumeric'],
        'event' => 'change'
      ],
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
      '#prefix' => '<span id="error-message-phone"></span>',
    ];

    $form['mail'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Mail'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateMail'],
        'event' => 'change',
      ],
      '#prefix' => '<span id="error-message-mail"></span>',
    ];

    $form['codepostal'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Code postal'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['postal_code']
      ],
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
      '#prefix' => '<span id="error-message-region"></span>',
    ];
    $form['latitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['latitude'],
        'disabled' => true,
        'class' => ['hidden']
      ],
      //'#attributes' => array( "class" => array("hidden") ),
    ];
    $form['longitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['longitude'],
        'disabled' => true,
        'class' => ['hidden']
      ],
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
    var_dump($field);
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
    $image  = $form_state->getValue('mail');
    $image  = $form_state->getValue('prix');
    $image  = $form_state->getValue('codepostal');
    $image  = $form_state->getValue('ville');
    $image  = $form_state->getValue('departement');
    $image  = $form_state->getValue('region');
    $image  = $form_state->getValue('latitude');
    $image  = $form_state->getValue('longitude');


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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Récupère la valeur des champs.
    global $user;
    $data              = $form_state['values'];

    $title        = $form_state->getValue('title') ? __filter_html($form_state->getValue('title')) : '';
    $image        = $form_state->getValue('image') ? __filter_html($form_state->getValue('image')) : '';
    $body         = $form_state->getValue('body') ? __filter_html($form_state->getValue('body')) : '';
    $prix         = $form_state->getValue('prix') ? __filter_html($form_state->getValue('prix')) : '';
    // $phone        = $form_state->getValue('phone') ? __filter_html($form_state->getValue('title')) : '';
    // $mail         = $form_state->getValue('mail') ? __filter_html($form_state->getValue('title')) : '';
    $cp           = $form_state->getValue('codepostal') ? __filter_html($form_state->getValue('codepostal')) : '';
    $ville        = $form_state->getValue('ville') ? __filter_html($form_state->getValue('ville')) : '';
    $departement  = $form_state->getValue('departement') ? __filter_html($form_state->getValue('departement')) : '';
    $region       = $form_state->getValue('region') ? __filter_html($form_state->getValue('region')) : '';
    $latitude     = $form_state->getValue('latitude') ? __filter_html($form_state->getValue('latitude')) : '';
    $longitude    = $form_state->getValue('longitude') ? __filter_html($form_state->getValue('longitude')) : '';




    // On passe le résultat.
    // $form_state->addRebuildInfo('result', $resultat);
    // Reconstruction du formulaire avec les valeurs saisies.
    // $form_state->setRebuild();

    // Enregistrement de l'heure de soumission avec State API.
    // $this->state->set('hello_form_submission_time', $this->time->getCurrentTime());
  }

}
