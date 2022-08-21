<?php

namespace Drupal\plan_de_desarrollo\Form;

use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\plan_de_desarrollo\Controller\ProgramaController;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\plan_de_desarrollo\Controller\SectoresController;
use Drupal\plan_de_desarrollo\Controller\helpController;


/**
 * Configure example settings for this site.
 */
class ProgramaForm extends ConfigFormBase {
  /*
   * @var string Config settings
   */
  const SETTINGS = 'app.core';

  /**
   * Filter.
   *
   * @var string
   */
  public static $filterSessionKey = 'programas_filter';

  /**
   * Get form Id string.
   */
  public function getFormId() {
    return 'programas_config_tabs';
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
    $edi = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $respuesta_sectores = SectoresController::getReportDataId();
    if(isset($edi) == true || isset($delete) == true){
      $respuesta = ProgramaController::getReportDataId();
    }

    $form['progrma_form'] = [
      '#type' => 'details',
      '#title' => t('Agregar Programa al Sector: '. $respuesta_sectores[0]->Sector),
      '#open' => TRUE,
    ];

    $form['progrma_form']['CodPrograma'] = [
      '#title' => t('Codigo Programa'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodPrograma) ? $respuesta[0]->CodPrograma : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['progrma_form']['Programa'] = [
      '#title' => t('Programa'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Programa) ? $respuesta[0]->Programa : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['progrma_form']['objetivo'] = [
      '#title' => t('objetivo'),
      '#type' => 'textarea',
      '#default_value' => isset($respuesta[0]->Objectivo) ? $respuesta[0]->Objectivo : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];
    $text_btn =  helpController::getBtnText($edi,  $delete);
   
    
    $markup_html_one = isset($edi) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="/programas?id_linea='.$id_linea.'&id='.$id_plan.'&id_sector='.$id_sector.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/sectores?id_linea='.$id_linea.'&id='.$id_plan.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   

    $form['progrma_form']['create_programa'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
         
    ];

    $form['report'] = [
      '#type' => 'details',
      '#title' => t('Programas REPORT'),
      '#open' => TRUE,
    ];

 list($header, $rows) = ProgramaController::getReportData();

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
        $edi = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
        $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
        $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
          $current_user = \Drupal::currentUser();
          $user = \Drupal\user\Entity\User::load($current_user->id());
      
         if(isset($edi) != true && isset($delete) != true){
             $id = ProgramaController::addPrograma($form_state, $id_sector);
            \Drupal::logger('Agregar nueva Progrma a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
             }
          if($edi){
            $id = ProgramaController::updateProgrma($form_state, $edi);
            \Drupal::logger('Editar Programa por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
            }

            if($delete){
              $id = ProgramaController::updateDeleteProgrma($form_state, $delete);
              \Drupal::logger('Eliminar Programa por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$delete);
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
      'id_programas',
      'CodSector',
      'Sector',
      'id_sector',
      'created'
    ]);
    if (!$excel) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_sector', $plan, '=')
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
     $item->id_sector,
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
    $query->condition('id_sector', $id, '=');
   
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
      ])->condition('id_sector', $id, '=')
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
        'id_sector' => $id,
      ])
      ->execute();
  }


}
