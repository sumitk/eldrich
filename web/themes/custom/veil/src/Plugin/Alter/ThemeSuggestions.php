<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Alter\ThemeSuggestions.
 */

namespace Drupal\veil\Plugin\Alter;

use Drupal\bootstrap\Annotation\BootstrapAlter;
use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Unicode;
use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Entity\EntityInterface;
use Drupal\bootstrap\Plugin\Alter\AlterInterface;
use Drupal\block\Entity\Block;
use Drupal\node\Entity\Node;

/**
 * Implements hook_theme_suggestions_alter().
 *
 * @ingroup plugins_alter
 *
 * @BootstrapAlter("theme_suggestions")
 */
class ThemeSuggestions extends PluginBase implements AlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(&$suggestions, &$context1 = NULL, &$hook = NULL) {
    $variables = Variables::create($context1);

    switch ($hook) {
      case 'form':
        foreach ($variables->element['#theme'] as $theme) {
          $suggestions[] = 'form__' . $theme; // Machine name of form.
        }
        $suggestions[] = 'form__' . $variables->element['#form_id'];

      case 'block':
        // Don't actually need this anymore, but I kinda like the look of it.
        if (($id = $variables->elements['#id']) && ($block = Block::load($id))) {
          $suggestions[] = 'block__' . $block->getRegion();
        }
        break;

      case 'views_view':
        // Eh, no one ever regretted this.
        $display = $variables->view->current_display;
        $view_id = $variables->view->id();

        $suggestions[] = 'views_view__' . $view_id;
        $suggestions[] = 'views_view__' . $view_id . '__' . $display;

        // The dirtiest damn thing you've ever seen.
        // Too lazy to write a display handler, and I don't want to let
        // Views take care of bootstrappifying the markup for single-item
        // feature views. So? Here we go.

        if (($variables->view->display_handler->pluginId == 'block')
          && ($variables->view->pager->options['items_per_page'] != 1)) {
          $suggestions[] = 'views_view__' . 'bootstrap_panel';
        }
        break;

      case 'eva_display_entity_view':
      case 'views_tree':
        $display = $variables->view->current_display;
        $view_id = $variables->view->id();
        $suggestions[] = $hook . '__' . $view_id;
        $suggestions[] = $hook . '__' . $view_id . '__' . $display;
        break;
    }
  }
}
