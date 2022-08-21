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
use Drupal\plan_de_desarrollo_presupuesto\Controller\areaFuncionalController;
use Drupal\plan_de_desarrollo\Controller\PlanController;
use Drupal\plan_de_desarrollo\Controller\helpController;

/**
 * Configure example settings for this site.
 */
class AreasFuncionalesForm extends ConfigFormBase {
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
      $respuesta = areaFuncionalController:: getReporLineaId();
    } 

    $form['AreasFuncionales'] = [
      '#type' => 'details',
      '#title' => t('Formulario Areas Funcionales'),
      '#open' => TRUE,
    ];

    $form['AreasFuncionales']['CodAreaFuncional'] = [
      '#title' => t('codigo del area'),
      '#type' => 'number',
      '#default_value' => isset($respuesta[0]->CodAreaFuncional) ? $respuesta[0]->CodAreaFuncional : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];


    $form['AreasFuncionales']['DescripcionAreaFuncional'] = [
      '#title' => t('Descripcion Area Funcional'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->DescripcionAreaFuncional) ? $respuesta[0]->DescripcionAreaFuncional : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['AreasFuncionales']['Estado'] = [
      '#title' => t('Estado'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Fondo) ? $respuesta[0]->Fondo : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['AreasFuncionales']['CodSubprograma'] = [
      '#title' => t('Codigo Sub programa'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodSubprograma) ? $respuesta[0]->CodSubprograma : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ]; 
    
    $markup_html_one = isset($id_linea) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split" href="/lineas?id='.$respuesta[0]->id_PlanDesarrollo.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/plandedesarrollo?id='.$plan.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   
    $text_btn =  helpController::getBtnText($id_linea,  $delete);
    $form['AreasFuncionales']['create_plan'] = [
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

    list($header, $rows) = areaFuncionalController::getReportData();

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
      $id=  areaFuncionalController::deleteFondos($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Lineas delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$id_lineas){
       $id = areaFuncionalController::addAreas($form_state, $plan);
      \Drupal::logger('Agregar nueva LIneas a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);

        }
        if($id_lineas){
          $id = areaFuncionalController::updateFondos($form_state, $id_lineas);
          \Drupal::logger('Editar Lineas por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Plan de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

}
