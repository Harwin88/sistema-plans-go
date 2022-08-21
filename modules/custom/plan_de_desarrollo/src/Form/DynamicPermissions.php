<?php

namespace Drupal\plan_de_desarrollo;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class DynamicPermissions
 * @package Drupal\plan_de_desarrollo
 */
class DynamicPermissions
{

  use StringTranslationTrait;

  /**
   * @return array
   */
  public function permissions()
  {
    $permissions = [];

    $count = 1;
    while ($count <= 5) {
      $permissions += [
        "plan_de_desarrollo permission $count" => [
          'title' => $this->t('plan_de_desarrollo permission @number', ['@number' => $count]),
          'description' => $this->t('This is a sample permission generated dynamically.'),
          'restrict access' => $count == 2 ? true : false,
        ],
      ];
      $count++;
    }
    return $permissions;
  }

}