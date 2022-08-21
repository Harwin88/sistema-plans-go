<?php
namespace Drupal\plan_de_desarrollo\Form;

use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\plan_de_desarrollo\Helper\FuntionHelp;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\plan_de_desarrollo\Controller\MetasController;
use Drupal\plan_de_desarrollo\Controller\SubprogramaController;
use Drupal\plan_de_desarrollo\Controller\helpController;

/**
 * Configure example settings for this site.
 */
class MetasForm extends ConfigFormBase {
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
    return 'metas_config_tabs';
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
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $edit = !empty($request['id_metas']) ? $request['id_metas'] : NULL;
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $respuesta = [];
    $user_rol = [];
    // $id = 0;
    $user_rol = \Drupal::currentUser()->getRoles();

    $respuesta_subpro = SubprogramaController::getReportDataId();
    if ( isset($delete) || isset($id_meta)) {
      $respuesta = MetasController:: getReporid_metasId();
    } 

    $form['metas_form'] = [
      '#type' => 'details',
      '#title' => t('Agrgar Metas productos: '.$respuesta_subpro[0]->NombreSubPrograma.' -  Vigencia Plan: '.$respuesta_plan[0]->Vigencia_Terminacion),
      '#open' => TRUE,
    ];

    $form['metas_form']['CodMetaProducto'] = [
      '#title' => t('Code Meta'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodMetaProducto) ? $respuesta[0]->CodMetaProducto : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['metas_form']['MetaProducto'] = [
      '#title' => t('Nombre Meta productos'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->MetaProducto) ? $respuesta[0]->MetaProducto : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['metas_form']['Tipo'] = [
        '#title' => t('Tipos'),
        '#type' => 'textfield',
        '#default_value' => isset($respuesta[0]->Tipo) ? $respuesta[0]->Tipo : '',
        '#disabled' => FALSE,
        '#attributes' => [
          'class' => ['form-control '],
        ],
      ];

      $form['metas_form']['PesoMetas'] = [
        '#title' => t('Peso Meta'),
        '#type' => 'number',
        '#default_value' => isset($respuesta[0]->PesoMetas) ? $respuesta[0]->PesoMetas : '',
        '#disabled' => FALSE,
        '#attributes' => [
          'class' => ['form-control '],
        ],
      ];

     $text_btn =  helpController::getBtnText($id_meta,  $delete);
     $markup_html_one = isset($id_meta) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="/metasproducto?id_linea='.$respuesta[0]->id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
     $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/subprogramas?page='.$page.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';

    $form['metas_form']['create_programa'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
         
    ];
    $form['report'] = [
      '#type' => 'details',
      '#title' => t('Metas REPORT'),
      '#open' => TRUE,
    ];

    list($header, $rows) = MetasController::getReportData();

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
    $edits = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    if ($delete) {  
      $id=  MetasController::deleteMetas($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Metas delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$edits){
       $id = MetasController::addMetas($form_state, $id_subprogrma);
      \Drupal::logger('Agregar nueva Metas a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);

        }
        if($edits){
          $id = MetasController::updateMetas($form_state, $edits);
          \Drupal::logger('Editar Metas por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Metas de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

}
