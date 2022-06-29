<?php

/**
 * @file
 * Contains \Drupal\civicrm_autologin\Form\CivicrmAutologinSettings.
 */

namespace Drupal\civicrm_autologin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CivicrmAutologinSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'civicrm_autologin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('civicrm_autologin.settings');
    $values = $form_state->getValues();

    $config->set('webforms', $values['webforms'])->save();
    $config->set('webform_open', $values['webform_open'])->save();
    $config->set('webform_submission', $values['webform_submission'])->save();
    $config->set('webform_submission_open', $values['webform_submission_open'])->save();
    $config->set('views', $values['views'])->save();
    $config->set('view_filter', $values['view_filter'])->save();
    $config->set('view_tags', $values['view_tags'])->save();
    $config->set('absurl', $values['absurl'])->save();
    $config->set('trace', $values['trace'])->save();
    $config->set('debug', $values['debug'])->save();


    // if (method_exists($this, '_submitForm')) {
    //   $this->_submitForm($form, $form_state);
    // }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['civicrm_autologin.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];
    $config = $this->config('civicrm_autologin.settings');

    $form['civicrm_autologin_entities'] = [
      '#type' => 'fieldset',
      '#title' => t('Entités à présenter'),
      '#description' => t('Entités à présenter') ,
    ];

    $form['civicrm_autologin_entities']['webform_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Webforms'),
    ];
    $form['civicrm_autologin_entities']['webform_group']['webforms'] = array(
      '#type' => 'checkbox',
      '#title' => t('Webforms.') ,
      '#default_value' => $config->get('webforms') ,
      );
    $form['civicrm_autologin_entities']['webform_group']['webform_open'] = array(
        '#type' => 'checkbox',
        '#title' => t('Only Opened Webforms') ,
        '#default_value' => $config->get('webform_open') ,
        '#prefix' => '<div class="indentalw">',
        '#suffix' => '</div>',
     );
       $form['civicrm_autologin_entities']['webform_group']['webform_submission'] = array(
        '#type' => 'checkbox',
        '#title' => t('Webform Submissions.') ,
        '#default_value' => $config->get('webform_submission') ,
     );
     $form['civicrm_autologin_entities']['webform_group']['webform_submission_open'] = array(
      '#type' => 'checkbox',
      '#title' => t('Only Opened Webform submissions') ,
      '#default_value' => $config->get('webform_submission_open') ,
      '#prefix' => '<div class="indentalw">',
      '#suffix' => '</div>',
   );

    $form['civicrm_autologin_entities']['view_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Views'),
    ];
    $form['civicrm_autologin_entities']['view_group']['views'] = array(
          '#type' => 'checkbox',
          '#title' => t('Views.') ,
          '#default_value' => $config->get('views') ,
       );

       $form['civicrm_autologin_entities']['view_group']['view_filter'] = array(
        '#type' => 'checkbox',
        '#title' => t('Filter views by tag') ,
        '#default_value' => $config->get('view_filter') ,
        '#prefix' => '<div class="indentalw">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="views"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
     );

    $views = \Drupal::entityQuery('view')
       ->execute();
    $b=\Drupal::entityTypeManager()->getStorage('view');
    $c=$b->loadMultiple($views);
    $tags = array();
    foreach ($c as $key) {
      foreach (explode(',', $key->get('tag')) as $tag) {
        if (trim($tag) != '') { 
          $tags[trim($tag)] = trim($tag);
        }
      }
    }
    $options = array();
    foreach (array_unique($tags,SORT_STRING) as $tag) {
      $options[$tag] = $tag;
    }
    $form['civicrm_autologin_entities']['view_group']['view_tags'] = [
      '#type' => 'select',
        '#title' => $this->t('Tags'),
        '#description' => $this->t('View tags'),
        '#options' => $options,
        '#size' => 5,
        '#multiple' => TRUE,
        '#default_value' => $config->get('view_tags'),
        '#prefix' => '<div class="indentalw">',
        '#suffix' => '</div>',
        '#states' => array(
          // Only show this field when the 'toggle_me' checkbox is enabled.
          'visible' => array(
            ':input[name="view_filter"]' => array(
              'checked' => TRUE,
            ),
            ':input[name="views"]' => array(
              'checked' => TRUE,
            ),
          ),
        ),
      ];
  

    $form['civicrm_autologin_url'] = [
      '#type' => 'fieldset',
      '#title' => t('Format de l\'URL'),
      '#description' => t('Format de l\'URL') ,
    ];

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    $form['civicrm_autologin_url']['absurl'] = array(
          '#type' => 'checkbox',
          '#title' => t('Absolue.') ,
          '#default_value' => $config->get('absurl') ,
          '#description' => t('Absolue: inclu l\'adresse du site. Sinon, fournit juste le chemin de la page') ,
       );


    $form['civicrm_autologin_trace'] = [
      '#type' => 'fieldset',
      '#title' => t('Trace'),
      '#description' => t('Log des operations.') ,
    ];

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    $form['civicrm_autologin_trace']['trace'] = array(
          '#type' => 'checkbox',
          '#title' => t('Log la créationdes URLS.') ,
          '#default_value' => $config->get('trace') ,
          '#description' => t('Log en base les URLS créées') ,
       );


    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    $form['civicrm_autologin_trace']['debug'] = array(
          '#type' => 'checkbox',
          '#title' => t('Debug.') ,
          '#default_value' => $config->get('debug') ,
          '#description' => t('Log en base des informations de debug') ,
       );

    $form['#attached']['library'][] = 'civicrm_autologin/civicrm_autologin_settings';
    
    $form = parent::buildForm($form, $form_state);
    $form['#submit'][] = 'civicrm_autologin_settings_submit';

    return $form;
  }

  public function _submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // Reload hook_menu cache.
    \Drupal::service('router.builder')->rebuild();
  }

}
?>
