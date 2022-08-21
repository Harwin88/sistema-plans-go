<?php

namespace Drupal\plan_de_desarrollo\Form;

use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\plan_de_desarrollo\Controller\SectoresController;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\plan_de_desarrollo\Controller\LineasController;
use Drupal\plan_de_desarrollo\Controller\helpController;

/**
 * Configure example settings for this site.
 */
class SectoresForm extends ConfigFormBase {
  /*
   * @var string Config settings
   */
  const SETTINGS = 'app.core';

  /**
   * Filter.
   *
   * @var string
   */
  public static $filterSessionKey = 'plan_filter';

  /**
   * Get form Id string.
   */
  public function getFormId() {
    return 'sectores_config_tabs';
  }

  /**
   * Create form render.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Function.
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * Function.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $session = \Drupal::request()->getSession();
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_linea= !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $respuesta_lineas = LineasController:: getReporLineaId();

    if(isset($edi) || isset($delete)){
      $respuesta = SectoresController::getReportDataId();
    }

    $form['sectores_form'] = [
      '#type' => 'details',
      '#title' => t('agregar sector a la lilena: '.  $respuesta_lineas[0]->nombre_linea),
      '#open' => TRUE,
    ];

    $form['sectores_form']['CodSector'] = [
      '#title' => t('Code Sector'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodSector) ? $respuesta[0]->CodSector : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['sectores_form']['Sector'] = [
      '#title' => t('Sector'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Sector) ? $respuesta[0]->Sector : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];
   
    $text_btn =  helpController::getBtnText($edi,  $delete);
   
   
    $markup_html_one = isset($edi) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="/sectores?id_linea='.$id_linea.'&id='.$id_plan.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/lineas?id='.$id_plan.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   

    $form['lineas_form']['create_sectores'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
         
    ];

    $form['report'] = [
      '#type' => 'details',
      '#title' => t('Sectores REPORT'),
      '#open' => TRUE,
    ];

 list($header, $rows) = SectoresController::getReportData();


 
    $form['report']['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#attributes' => [
        'id'=>"dataTable",
        'width'=>"100%",
        'cellspacing'=> "0",
        'class' => ['table cell-border table table-bordered'],
      ],
      '#prefix' => '<div class="card-body">  <div class="table-responsive">',
      '#suffix' => '</div>',
      '#empty' => t('No records'),
    ];

    $form['report']['pager'] = [
      '#type' => 'pager',
      '#element' => 0,
    ];

   // $form['#cache']['contexts'][] = 'session';

    return $form;
  }

  /**
   * Function.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  
        return $form;
      

    
  }

  /**
   * Public function addmoreCallback_config(array &$form, FormStateInterface $form_state)
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   
      try {
        $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
        $edi = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
        $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
        $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
          $current_user = \Drupal::currentUser();
          $user = \Drupal\user\Entity\User::load($current_user->id());
          $edi = isset($delete) ? $delete : $edi;
         if(isset($edi) != true  || isset($delete) != true)
         {
             $id = SectoresController::addSectores($form_state, $id_linea);
            \Drupal::logger('Agregar nueva Sector a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
      
          }
          if(isset($edi) == true  && isset($delete) != true){
            $id = SectoresController::updateSector($form_state, $edi);
            \Drupal::logger('Editar Sector por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
            }
            if(isset($delete)){
         
              $id =  SectoresController::deleteSector($delete);
            \Drupal::logger('Eliminar Logico de Sectores')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);

            }
    
  
              return $form;
            }
            catch (Exception $e) {
              \Drupal::logger('error crub Plan de desarrollo')->error("crud error " . print_r($e, 1));
            }
          
        }

  /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Codigo sector'),
      t('Sector'),
      t('Id Lineas'),
      t('created'),
      t('Edit'),
      t('delete'),
      t(''),
    ];

    $query = \Drupal::database()->select('TblSectores', 'pt');
    $query->fields('pt', [
      'id_sector',
      'CodSector',
      'Sector',
      'id_linea',
      'created'
    ]);
    if (!$excel) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_linea', $plan, '=')
        ->limit(20)
        ->element(0);
    }

    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        ->condition('Sector', '%' . $search_filter . '%', 'LIKE');
     
      $query->condition($or);
    }

    $result = $query
      ->orderBy('Sector', 'ASC')
      ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/plandedesarrollo?delete_id=' . $item->CodSector . '&page=' . $page);
      $edit = Url::fromUserInput('/plandedesarrollo?CodSector=' . $item->CodSector . '&page=' . $page);
      $lineas = Url::fromUserInput('/lineas?CodSector=' . $item->CodSector . '&page=' . $page);
      $row = [
     $item->id_linea,
     $item->CodSector,
     $item->Sector,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('Edit'), $edit),
     Link::fromTextAndUrl(t('delete'), $delete),
     Link::fromTextAndUrl(t('lineas'), $lineas),
      ];
      $rows[] = $row;
    }

    return [$header, $rows];
  }

   /**
   * Function.
   */
  public static function getReportDataId($id) {
    $query = \Drupal::database()->select('TblLineas', 'p');
    $query->condition('CodSector', $id, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;
  }

    /**
   * To get all cities from taxonomies.
   */
  public function deleteDateProduct($id) {
    $database = \Drupal::database();
    $query = $database->delete('plan');
    // Add extra detail to this query object: a condition, fields and a range.
    $query->condition('id_linea', $id, '=');
   
    $respu = $query->execute();

    return $respu;
  }


  /**
   * To get all cities from taxonomies.
   */
  private function getTaxonomyList($name) {
    $vid = $name;
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data = [];
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    return $term_data;
  }

  /**
   * To get all cities from taxonomies.
   */
  private function getIdTaxonomy($name_taxonomy, $name) {
    $vid = $name_taxonomy;
    $id_brand = '';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      if ($term->name == $name) {
        $id_brand = $term->tid;
        return $id_brand;
      }
    }
    return '';
  }

  /**
   * To get all cities from taxonomies.
   */
  private function getCategory() {
    $vid = 'categorias';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data = [];
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    return $term_data;
  }

  /**
   * Add imagen for product.
   */
  public function editproductImage($urlDes, $urlMobile, $id) {
    \Drupal::database()->update('imagen_peoductos')
      ->fields([
        'url_image' => $urlDes,
        'url_image_mobile' => $urlMobile,
      ])->condition('id_linea', $id, '=')
      ->execute();
  }

  /**
   * Add imagen for product.
   */
  public function addImagenproduct($urlDes, $urlMobile, $id) {
    \Drupal::database()->insert('imagen_peoductos')
      ->fields([
        'url_image' => $urlDes,
        'url_image_mobile' => $urlMobile,
        'id_linea' => $id,
      ])
      ->execute();
  }


}
