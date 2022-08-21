<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\plan_de_desarrollo\Controller\PlanController;
use Drupal\plan_de_desarrollo\Helper\FuntionHelp;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\plan_de_desarrollo_presupuesto\Controller\helpController; 
use Drupal\plan_de_desarrollo_presupuesto\Controller\presupuestoController;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;

/**
 * Configure example settings for this site.
 */
class PresupuestoForm extends ConfigFormBase {
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
    return 'app_config_tabs';
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
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $subPrograma = !empty($request['subPrograma']) ? $request['subPrograma'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
   
    $respuesta = [];
    $user_rol = [];
    $user_rol = \Drupal::currentUser()->getRoles();
    $form['presupuesto'] = [
      '#type' => 'details',
      '#title' => t('Agrgar presupuesto Metas: '),
      '#open' => TRUE,
    ];
    
    $form['presupuesto']['plan_id'] = [
      '#title' => 'Seleccione una Plan',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Selecciona Un Plan'),
      '#options' => helpController::getPlanes(),
      '#attributes' => [
        'placeholder' => t('Selecciona una'),
        'class' => ['select_control'],
      ],
     
      '#ajax' => [
        'event' => 'change',
        'callback' =>'::planlineasSelectCallback',
        'wrapper' => 'field-lineas',
      ],
      '#prefix' => '<div id="field-planes">',
      '#suffix' => '</div>',
    ]; 

     
      $form['presupuesto']['lineas_id'] = [
        '#title' => 'Seleccione una Linea',
        '#type' => 'select',
        '#required' => false,
        '#empty_option' => t('Selecciona Un Lineas'),
        '#options' => helpController::getPlanes_lineas($form_state->getValue('plan_id')),
        '#attributes' => [
          'placeholder' => t('Selecciona una'),
          'class' => ['select_control'],
        ],
        '#states' => [
          '!visible' => [
            ':input[name="plan_id"]' => ['value' => ''],
          ],
        ],
        '#ajax' => [
          'event' => 'change',
          'callback' => '::lineasSectoresSelectCallback',
          'wrapper' => 'field-sectores',
        ],
        '#prefix' => '<div id="field-lineas">',
        '#suffix' => '</div>',
      ];
  
    $form['presupuesto']['sector_id'] = [
      '#title' => 'Seleccione un sector',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Selecciona Un sector'),
      '#options' => helpController::get_sectores_lineas($form_state->getValue('lineas_id')),
      '#attributes' => [
        'placeholder' => t('Selecciona sector'),
        'class' => ['select_control'],
      ],
      '#states' => [
        '!visible' => [
          ':input[name="lineas_id"]' => ['value' => ''],
        ],
      ],
      '#ajax' => [
        'event' => 'change',
        'callback' => '::SectoresProgramaCallback',
        'wrapper' => 'field-programas',
      ],
      '#prefix' => '<div id="field-sectores">',
      '#suffix' => '</div>',
    ];


    $form['presupuesto']['programas_id'] = [
      '#title' => 'Seleccione un programas',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Selecciona Un programa'),
      '#options' => helpController::get_programas($form_state->getValue('sector_id')),
      '#attributes' => [
        'placeholder' => t('Selecciona programa'),
        'class' => ['select_control'],
      ],
      '#states' => [
        '!visible' => [
          ':input[name="sector_id"]' => ['value' => ''],
        ],
      ],
      '#ajax' => [
        'event' => 'change',
        'callback' => '::SubprogramaProgramaCallback',
        'wrapper' => 'field-subprograma',
      ],
      
      '#prefix' => '<div id="field-programas">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['ConsecutivoSubPrograma'] = [
      '#title' => 'Seleccione un sud programa',
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Selecciona Un subprograma'),
      '#options' => helpController::get_subprogramas($form_state->getValue('programas_id')),
      '#attributes' => [
        'placeholder' => t('Selecciona programa'),
        'class' => ['select_control'],
      ],
      '#ajax' => [
        'event' => 'change',
        'callback' => '::reporteOnload',
        'wrapper' => 'field-subprograma-id',
      ],
      '#states' => [
        '!visible' => [
          ':input[name="programas_id"]' => ['value' => ''],
        ],
      ],
      '#prefix' => '<div id="field-subprograma">',
      '#suffix' => '</div>',
    ];
    
    $form['presupuesto']['vigencia'] = [
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->cod_linea) ? $respuesta[0]->cod_linea : date("Y"),
      '#disabled' => false,
      '#attributes' => [
        'class' => ['form-control '],
        'placeholder' => t('Vigencia'),
      ],
      '#prefix' => '<div class="row"><div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['Fondo'] = [
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('fondo'),
      '#options' =>helpController::getFondos(),
      '#attributes' => [
        'placeholder' => t('fondo'),
        'class' => ['select_control'],
      ],
      '#prefix' => '<div class="col"></div><div class="col">',
      '#suffix' => '</div>',
    ];
    

    $form['presupuesto']['CentroGestor'] = [
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('CG'),
      '#options' => helpController::getTaxonomyData('tb_centro_gestor'),
      '#attributes' => [
        'placeholder' => t('CG'),
        'class' => ['select_control'],
      ],
  
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['Pospre'] = [
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Pospre'),
      '#options' =>  [
        230101013 => '230202213 | Casa de cultura, Bibliotecas y Similares',
        230101014 => '230202214 | unidades',
        230101012 => '230202214 | planteles educativos'
      ],
      '#attributes' => [
        'placeholder' => t('Pospre'),
        'class' => ['select_control'],
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['AreaFuncional'] = [
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('Area Funcional'),
      '#options' => helpController::getAreas(),
      '#attributes' => [
        'placeholder' => t('Area Funcional'),
        'class' => ['select_control'],
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['Proyecto'] = [
      '#type' => 'select',
      '#required' => false,
      '#empty_option' => t('proyecto'),
      '#options' =>  [
        '2022-170000-0020' => '2022-170000-0020 | mEJORAMIENTO DE LA CONDICIONES DE SEGURIDAD EN LA RED VIAL',
        '2022-170000-0020' => '32022-170000-0027 | PAVIMENTO DE LA RED VIAL, VIEGENCIA 2022 - DEPARTAMENTO DE CALDAS',
        '308115' => '308115 | ADMINISTRACION DE LOS RECURSOS HUMANOS PARA LA PRESTACION DE SERVICIOS EDUCATIVOS, '
    ],
      '#attributes' => [
        'placeholder' => t('proyecto'),
        'class' => ['select_control'],
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['Valor_DecretoLiquidacion_Inicial'] = [
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->cod_linea) ? $respuesta[0]->cod_linea : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
        'placeholder' => t('valor decreto inicial'),
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
    ];

    $form['presupuesto']['guardar_btn'] = [
      '#type' => 'submit',
      '#value' => t('agregar presupuesto'),
      '#attributes' => [
        'class' => [' btn '],
      ],
      //'#ajax' => [
      //  'event' => 'click',
      //  'callback' => '::agregar',
      //  'wrapper' => 'field-table',
      //],
      '#prefix' => '<div class="col btn-sub">',
      '#suffix' => '</div></div>',
    ];
  
if($subPrograma){
  $form['reporte'] = [
    '#type' => 'details',
    '#title' => t('Reporte: '),
    '#open' => TRUE,
  ];

  list($header, $rows, $totales)= helpController::getReportData($form_state->getValue('ConsecutivoSubPrograma'));

$form['reporte']['table'] = [
    '#type' => 'table',
    '#rows' => $rows,
    '#header' => $header,
    '#attributes' => [
      'id'=>"dataTable",
      'width'=>"100%",
      'cellspacing'=> "0",
      'class' => [' table cell-border table table-bordered'],
    ],

    '#prefix' => '<div class="card-body table-responsive"><div id="field-subprograma-id" > ',
    '#suffix' => '</div></div>',
    '#empty' => t('No records'),
 
  ];
}else{
  $form['reporte'] = [
    '#type' => 'details',
    '#title' => t('Reporte: '),
    '#open' => TRUE,
  ];

  list($header, $rows, $totales)= helpController::getReportData($form_state->getValue('ConsecutivoSubPrograma'));

$form['reporte']['table'] = [
    '#type' => 'table',
    '#rows' => $rows,
    '#header' => $header,
    '#attributes' => [
      'id'=>"dataTable",
      'width'=>"100%",
      'cellspacing'=> "0",
      'class' => [' table cell-border table table-bordered'],
    ],
    
  '#states' => [
      '!visible' => [
        ':input[name="ConsecutivoSubPrograma"]' => ['value' => ''],
      ],
    ],
    '#prefix' => '<div class="card-body table-responsive"><div id="field-subprograma-id" > ',
    '#suffix' => '</div></div>',
    '#empty' => t('No records'),
 
  ];
}
   

    

    return $form;
  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
 
    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#tl_teacher_form', $form));
    }
    else {
      $message = $this->t('The email has been sent.');
      drupal_set_message($message);
      \Drupal::logger('tl_session')->notice($message);
      $response->addCommand(new OpenModalDialogCommand("Success!", $message, ['width' => 555]));
    }
    return $response;
  }

  /**
   * Public function addmoreCallback_config(array &$form, FormStateInterface $form_state)
   */
public function agregar(array &$form, FormStateInterface $form_state) {

try {
 if($form_state->getValue('sudprogramas_id')){
 $id= presupuestoController::addPresupuesto($form_state);
   return $form;
 } 

return $form_state;


    if ($delete) {  
      $id=  presupuestoController::updateDeleteProgrma($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Programa de Indicadores delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$edits){
      $document_url = presupuestoController::getDocumenUrl($form_state);
       $id = presupuestoController::presupuestoController($form_state, $indicadores_id, $document_url);
      \Drupal::logger('Agregar nueva Programa de Indicadores a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
         
        }
        if($edits){
          $id = presupuestoController::updateProgrma($form_state, $edits);
          \Drupal::logger('Editar Programa de Indicadores por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Programa de Indicadores de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    return $form_state;
}

public function submitForm(array &$form, FormStateInterface $form_state) {
 
  $id= presupuestoController::addPresupuesto($form_state);
  return $form_state;
    
}

public function reporteOnload(array $form, FormStateInterface $form_state){
  return $form['reporte']['table'];
}

public function planlineasSelectCallback(array $form, FormStateInterface $form_state) {
  if($form_state->getValue('plan_id') != '' ){
    return $form['presupuesto']['lineas_id'];
  }else{
    return $form_state;
  }
  
}

public function lineasSectoresSelectCallback(array $form, FormStateInterface $form_state) {
  if($form_state->getValue('lineas_id') != '' ){
    return $form['presupuesto']['sector_id'];
  }else{
    return;
  }
}


public function SectoresProgramaCallback(array $form, FormStateInterface $form_state) {
  if($form_state->getValue('sector_id') != '' ){
    return $form['presupuesto']['programas_id'];
  }else{
    return;
  }
}



public function SubprogramaProgramaCallback(array $form, FormStateInterface $form_state) {
  
      return $form['presupuesto']['ConsecutivoSubPrograma'];

}

public function returnRun($id){
  list($header, $rows, $totales)= helpController::getReportData($id);
  return $rows;
}

public function returnheader($id){
  list($header, $rows, $totales)= helpController::getReportData($id);
  return $header;
}


}