<?php
/**
 * @file
 * Control: Attribution.
 */

namespace Drupal\openlayers_examples\Plugin\Control\OL3LayerSwitcher;
use Drupal\openlayers\Component\Annotation\OpenlayersPlugin;
use Drupal\openlayers\Types\Control;

/**
 * Class OL3LayerSwitcher.
 *
 * @OpenlayersPlugin(
 *  id = "OL3LayerSwitcher",
 *  description = ""
 * )
 *
 */
class OL3LayerSwitcher extends Control {
  /**
   * {@inheritdoc}
   */
  public function optionsForm(&$form, &$form_state) {
    $form['options']['collapsible'] = array(
      '#type' => 'checkbox',
      '#title' => t('Collapsible'),
      '#default_value' => $this->getOption('collapsible'),
    );
  }
}
