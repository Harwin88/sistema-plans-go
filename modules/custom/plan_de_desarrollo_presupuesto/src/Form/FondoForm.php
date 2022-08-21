<?php
namespace Drupal\plan_de_desarrollo_presupuesto\Form;

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
use Drupal\plan_de_desarrollo_presupuesto\Controller\fondoController;
use Drupal\plan_de_desarrollo\Controller\PlanController;
use Drupal\plan_de_desarrollo\Controller\helpController;

/**
 * Configure example settings for this site.
 */
class FondoForm extends ConfigFormBase {
  /*
   * @var string Config settings
   */
  const SETTINGS = 'app.core';

  /**
   * Get form Id string.
   */
  public function getFormId() {
    return 'fondo_config_tabs';
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
    $edi = !empty($request['cod_linea']) ? $request['cod_linea'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL; 
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $plan = !empty($request['id']) ? $request['id'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $respuesta = [];
    $user_rol = [];
    // $id = 0;
    $user_rol = \Drupal::currentUser()->getRoles();

    $respuesta_plan = PlanController::getReportDataId();
    if (isset($id_linea) || isset($delete)) {
      $respuesta = fondoController:: getReporLineaId();
    } 

    $form['fondo'] = [
      '#type' => 'details',
      '#title' => t('Agregar Fondos Al Sud programa: '.$respuesta_plan[0]->NombrePlanDesarrollo.' -  Vigencia Plan: '.$respuesta_plan[0]->Vigencia_Terminacion),
      '#open' => TRUE,
    ];
    $form['fondo']['CodFondo'] = [
      '#title' => t('Codigo Fondo'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodFondo) ? $respuesta[0]->CodFondo : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['fondo']['Fondo'] = [
      '#title' => t('Fondo'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Fondo) ? $respuesta[0]->Fondo : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['fondo']['CodVigenciaFondo'] = [
      '#title' => 'Codigo Vigencia Fondo',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Codigo Vigencia Fondo'),
      '#options' => helpController::getTaxonomyData('TBL_vigencia_fondo '),
      '#attributes' => [
        'placeholder' => t('Selecciona una'),
        'class' => ['select_control'],
      ],
   
    ]; 
    

    $form['fondo']['CodTipoRecurso'] = [
      '#title' => 'Codigo Tipo Recurso',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Codigo Tipo Recurso'),
      '#options' => helpController::getTaxonomyData('TBL_tipo_recurso'),
      '#attributes' => [
        'placeholder' => t('Selecciona una'),
        'class' => ['select_control'],
      ],
   
    ]; 
    
      $form['fondo']['CodSituacionFondo'] = [
        '#title' => 'Codigo Situacion Fondo',
        '#type' => 'select',
        '#required' => false,
        '#empty_option' => t('Codigo Situacion Fondo'),
        '#options' => helpController::getTaxonomyData('tbl_situacion_fondo'),
        '#attributes' => [
          'placeholder' => t('Selecciona una'),
          'class' => ['select_control'],
        ],
     
      ]; 

      $form['fondo']['CodFuenteCUIPO'] = [
        '#title' => 'Cod Fuente CUIPO',
        '#type' => 'select',
        '#required' => false,
        '#empty_option' => t('Codigo Fuente CUIPO'),
        '#options' => helpController::getTaxonomyData('tbl_fuente_financiacion_cupo'),
        '#attributes' => [
          'placeholder' => t('Selecciona una'),
          'class' => ['select_control'],
        ],
      ]; 
    
    $markup_html_one = isset($id_linea) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split" href="/lineas?id='.$respuesta[0]->id_PlanDesarrollo.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/plandedesarrollo?id='.$plan.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   
    $text_btn =  helpController::getBtnText($id_linea,  $delete);
    $form['fondo']['create_plan'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
         
    ];
   
    $form['report'] = [
      '#type' => 'details',
      '#title' => t('LINEAS REPORT'),
      '#open' => TRUE,
    ];

    list($header, $rows) = fondoController::getReportData();

    $form['report']['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#attributes' => [
        'id'=>"dataTable",
        'width'=>"100%",
        'cellspacing'=> "0",
        'class' => [' table cell-border table table-bordered'],
      ],
      '#prefix' => '<div class="card-body">  <div class="table-responsive">',
      '#suffix' => '</div>',
    ];



   $form['#cache']['contexts'][] = 'session';

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
    $id_lineas = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $plan = !empty($request['id']) ? $request['id'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    if ($delete) {  
      $id=  fondoController::deleteFondos($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Lineas delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$id_lineas){
       $id = fondoController::addFondos($form_state, $plan);
      \Drupal::logger('Agregar nueva LIneas a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);

        }
        if($id_lineas){
          $id = fondoController::updateFondos($form_state, $id_lineas);
          \Drupal::logger('Editar Lineas por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Plan de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

}
