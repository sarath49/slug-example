<?php

namespace Drupal\slugify\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\slugify\Service\SlugifyService;

/**
 * Plugin implementation of the 'text_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "slugify_field_formatter",
 *   label = @Translation("Slugify Field Formatter"),
 *   field_types = {
 *     "string",
 *   },
 *   edit = {
 *     "editor" = "form"
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class SlugifyFieldFormatter extends StringFormatter implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\slugify\Service\SlugifyService
   */
  protected $slugify;

  /**
   * {@inheritdoc}
   */
  public function __construct(
      $plugin_id, 
      $plugin_definition, 
      FieldDefinitionInterface $field_definition, 
      array $settings, 
      $label, 
      $view_mode, 
      array $third_party_settings, 
      EntityTypeManagerInterface $entity_type_manager,
      SlugifyService $slugify) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_type_manager);
    $this->slugify = $slugify;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('slugify.concur')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'seperator' => '-',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['seperator'] = [
      '#title' => $this->t('Slug seperatory'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('seperator'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $seperator = $this->getSetting('seperator');
    if (trim($seperator) === "") {
        $summary[] = $this->t('No seperatory defined.');
    }
    else {
      $summary[] = $this->t('Slug will be seperated with: @seperator', ['@seperator' => $seperator]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $seperator = $this->getSetting('seperator');
    foreach ($items as $delta => $item) {
        if ($seperator == "") {
          
        }
        
        $element[$delta] = [
          '#type' => 'markup',
          '#markup' => ($seperator === "") ? 
            $this->slugify->slugify($item->value) : $this->slugify->slugify($item->value, $seperator),
        ];
    }

    return $element;
  }
}