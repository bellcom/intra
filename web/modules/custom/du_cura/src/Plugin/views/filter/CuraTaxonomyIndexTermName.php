<?php

namespace Drupal\du_cura\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\os2web_rest_api\Plugin\views\filter\TaxonomyIndexTermName;
use Drupal\Component\Serialization\Json;

/**
 * CURA Filter by term name.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("cura_os2web_rest_taxonomy_index_term_name")
 */
class CuraTaxonomyIndexTermName extends TaxonomyIndexTermName {

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $vocabulary = $this->vocabularyStorage->load($this->options['vid']);
    if (empty($vocabulary) && $this->options['limit']) {
      $form['markup'] = [
        '#markup' => '<div class="js-form-item form-item">' . $this->t('An invalid vocabulary is selected. Please change it in the options.') . '</div>',
      ];
      return;
    }
    $input_argument = $this->options['expose']['identifier'];
    // Getting form input.
    $user_input = $form_state->getUserInput();

    // Getting JSON input and mering it with forms input.
    $json = file_get_contents('php://input');
    if (!empty($json)) {
      $json_data = Json::decode($json);
      $user_input = array_merge_recursive($user_input, $json_data);
    }
    $tids = [];
    if (!empty($user_input[$input_argument])) {
      $term_names = $user_input[$input_argument];
      /** @var \Drupal\taxonomy\Entity\Term $term */
      foreach ($term_names as $term_name) {
        $terms = $this->termStorage->loadByProperties([
          'name' => strip_tags(trim($term_name)),
          'vid' => $vocabulary->id(),
        ]);
        // In case taxonomy term not found we pass "-1" as tid.
        if (empty($terms)) {
          $tids[] = -1;
          continue;
        }
        $term = reset($terms);
        $tids[] = $term->id();
      }
    }
    $form['value'] = [
      '#type' => 'textfield',
      '#default_value' => $tids,
    ];
    $form_state->setUserInput(['cura_tids_by_names' => $tids]);
  }

}
