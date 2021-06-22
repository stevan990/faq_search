<?php

namespace Drupal\faq_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FaqSearchForm provide form for custom search.
 */
class FaqSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'faq_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['faq_search'] = [
      '#type' => 'search_api_autocomplete',
      '#search_id' => 'faq_search',
      '#title' => $this->t('Search'),
      '#description' => $this->t('You can also browse the topics below to find what you are looking for.'),
      '#weight' => '0',
      '#title_display' => 'invisible',
      '#attributes' => [
        'placeholder' => t('Type keywords to find answers'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format' ? $value['value'] : $value));
    }
  }

}
