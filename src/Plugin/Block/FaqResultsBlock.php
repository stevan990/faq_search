<?php

namespace Drupal\faq_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Provides a 'FaqResultsBlock' block.
 *
 * @Block(
 *  id = "faq_results_block",
 *  admin_label = @Translation("FAQ Results Block"),
 * )
 */
class FaqResultsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $build = [];

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'faq')
      ->condition('langcode', $language)
      ->execute();
    $nodes = Node::loadMultiple($nids);

    $sidebar = [];
    $content = [];

    $i = 0;
    foreach ($nodes as $node) {
      $paragraph = Paragraph::load($node->get('field_faq_service')->target_id);

      // Check if reference is term.
      if ($paragraph->hasField('field_faq_area_area')) {

        // Set results sidebar if reference is term.
        if (Term::load($paragraph->get('field_faq_area_area')->target_id)) {
          if (!isset($sidebar[$i - 1]['category']) || Term::load($paragraph->get('field_faq_area_area')->target_id)->label() != $sidebar[$i - 1]['category']) {
            $sidebar[$i] = [
              'class' => 'term-result-' . $paragraph->get('field_faq_area_area')->target_id,
              'category' => Term::load($paragraph->get('field_faq_area_area')->target_id)
                ->label(),
            ];
            $i++;
          }

          // Set results content if reference is term.
          $content[] = [
            'class' => 'term-result-' . $paragraph->get('field_faq_area_area')->target_id,
            'question' => $node->label(),
            'answer' => $node->get('field_faq_body')->value,
          ];
        }
      }
      // Check if reference is node.
      elseif ($paragraph->hasField('field_faq_service_service')) {

        // Set results sidebar if reference is node.
        if (Node::load($paragraph->get('field_faq_service_service')->target_id)) {
          if (!isset($sidebar[$i - 1]['category']) || Node::load($paragraph->get('field_faq_service_service')->target_id)->label() != $sidebar[$i - 1]['category']) {
            $sidebar[$i] = [
              'class' => 'node-result-' . $paragraph->get('field_faq_service_service')->target_id,
              'category' => Node::load($paragraph->get('field_faq_service_service')->target_id)
                ->label(),
            ];
            $i++;
          }

          // Set results content if reference is node.
          $content[] = [
            'class' => 'node-result-' . $paragraph->get('field_faq_service_service')->target_id,
            'question' => $node->label(),
            'answer' => $node->get('field_faq_body')->value,
          ];
        }
      }
    }

    usort($sidebar, function ($a, $b) {
      return $a['category'] <=> $b['category'];
    });

    $build = [
      '#theme' => 'faq_results',
      '#sidebar' => $sidebar,
      '#content' => $content,
      '#config' => $config['faq_results_block_settings'],
      '#form' => \Drupal::formBuilder()
        ->getForm('Drupal\faq_search\Form\FaqSearchForm'),
    ];

    return $build;
  }

  /**
   * Block form configuration.
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['faq_results_block_settings'] = [
      'section_sidebar_title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Section Sidebar Title'),
        '#default_value' => !empty($config['faq_results_block_settings']['section_sidebar_title']) ? $config['faq_results_block_settings']['section_sidebar_title'] : '',
        '#required' => TRUE,
      ],
      'section_sidebar_body' => [
        '#type' => 'textarea',
        '#title' => $this->t('Section Sidebar Body'),
        '#default_value' => !empty($config['faq_results_block_settings']['section_sidebar_body']) ? $config['faq_results_block_settings']['section_sidebar_body'] : '',
        '#required' => TRUE,
      ],
      'section_content_title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Section Content Title'),
        '#default_value' => !empty($config['faq_results_block_settings']['section_content_title']) ? $config['faq_results_block_settings']['section_content_title'] : '',
        '#required' => TRUE,
      ],
    ];

    return $form;
  }

  /**
   * Block form configuration submit.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['faq_results_block_settings'] = $form_state->getValue('faq_results_block_settings');
  }

}
