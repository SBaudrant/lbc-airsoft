<?php

namespace Drupal\lbc_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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
    return ['createAnnonce.settings'];
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
    $form['image'] = [
      '#type' => 'image',
      '#title' => $this->t('Image for your ad'),
    ];
    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description of your ad'),
      '#required' => TRUE,
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title'  => $this->t('Phone number'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateNumeric'],
        'event' => 'keyup',
      ],
      '#prefix' => '<span id="error-message-phone"></span>',
    ];

    $form['mail'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Mail'),
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateMail'],
        'event' => 'keyup',
      ],
      '#prefix' => '<span id="error-message-mail"></span>',
    ];

    $form['ville'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('City'),
      '#required' => TRUE,
      '#attributes' => [
        'id' => ['ville_autocomplete_address']
      ],
      '#prefix' => '<span id="error-message-ville"></span>',
    ];
    $form['codepostal'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Code postal'),
      '#required' => TRUE,
      '#attributes' => ['id' => ['ville_autocomplete_address'] ],
      '#prefix' => '<span id="error-message-ville"></span>',
    ];
    $form['departement'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Departement'),
      '#required' => TRUE,
      '#prefix' => '<span id="error-message-departement"></span>',
    ];
    $form['region'] = [
      '#type' => 'textfield',
      '#title'  => $this->t('Region'),
      '#required' => TRUE,
      '#prefix' => '<span id="error-message-region"></span>',
    ];
    $form['latitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateAutocompleteLocation'],
        'event' => 'keyup',
      ],
      //'#attributes' => array( "class" => array("hidden") ),
    ];
    $form['longitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'AjaxValidateAutocompleteLocation'],
        'event' => 'keyup',
      ],
      //'#attributes' => array( "class" => array("hidden") ),
    ];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Envoyer'),
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

  public function AjaxValidateNotEmpty(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $field = $form_state->getTriggeringElement()['#name'];

    if ( !empty($form_state->getValue($field))) {
      $css = ['border' => '2px solid green'];
      $message = $this->t('OK!');
    } else {
      $css = ['border' => '2px solid red'];
      $message = $this->t('%field can\'t be numeric!', ['%field' => $form[$field]['#title']]);
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

    if ( preg_match ( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $variable ) )
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


  public function AjaxValidateAutocompleteLocation(array &$form, FormStateInterface $form_state) {

    // Method: POST, PUT, GET etc
    // Data: array("param" => "value") ==> index.php?param=value
    $method = "GET";
    $curl = curl_init();

    $url = "https://geo.api.gouv.fr/communes?fields=nom,codesPostaux,codeDepartement,departement,codeRegion,region&format=json&geometry=centre&nom=Villepinte";


    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
    ksm($result);

    curl_close($curl);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value_1 = $form_state->getValue('value1');
    $value_2 = $form_state->getValue('value2');
    $operation = $form_state->getValue('operation');

    if (!is_numeric($value_1)) {
      $form_state->setErrorByName('value1', $this->t('First value must be numeric!'));
    }
    if (!is_numeric($value_2)) {
      $form_state->setErrorByName('value2', $this->t('Second value must be numeric!'));
    }
    if ($value_2 == '0' && $operation == 'division') {
      $form_state->setErrorByName('value2', $this->t('Cannot divide by zero!'));
    }

    if (isset($form['result'])) {
      unset($form['result']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Récupère la valeur des champs.
    $value_1 = $form_state->getValue('value1');
    $value_2 = $form_state->getValue('value2');
    $operation = $form_state->getValue('operation');

    $resultat = '';
    switch ($operation) {
      case 'addition':
        $resultat = $value_1 + $value_2;
        break;
      case 'soustraction':
        $resultat = $value_1 - $value_2;
        break;
      case 'multiplication':
        $resultat = $value_1 * $value_2;
        break;
      case 'division':
        $resultat = $value_1 / $value_2;
        break;
    }

    // On passe le résultat.
    $form_state->addRebuildInfo('result', $resultat);
    // Reconstruction du formulaire avec les valeurs saisies.
    $form_state->setRebuild();

    // Enregistrement de l'heure de soumission avec State API.
    $this->state->set('hello_form_submission_time', $this->time->getCurrentTime());
  }

}
