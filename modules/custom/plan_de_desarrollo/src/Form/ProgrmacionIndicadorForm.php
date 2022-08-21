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
use Drupal\plan_de_desarrollo\Controller\PlanController;
use Drupal\plan_de_desarrollo\Helper\FuntionHelp;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\plan_de_desarrollo\Controller\ProgramacionIndicadoresController;
use Drupal\plan_de_desarrollo\Controller\helpController;
use Drupal\plan_de_desarrollo\Controller\IndicadoresController;

/**
 * Configure example settings for this site.
 */
class ProgrmacionIndicadorForm extends ConfigFormBase {
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
    $session = \Drupal::request()->getSession();
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edit = !empty($request['id_programacion']) ? $request['id_programacion'] : NULL; 
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_indicador = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $respuesta = [];
    $user_rol = [];
    // $id = 0;
    $user_rol = \Drupal::currentUser()->getRoles();

     $respuesta_indicadores = IndicadoresController::getReportDataId();
    if (isset($edit) || isset($delete)) {
      $respuesta = ProgramacionIndicadoresController::getReportDataId();
    } 

    $form['plan_de_desarrollo'] = [
      '#type' => 'details',
      '#title' => t('Agrgar Programacion Indicadores: '.$respuesta_indicadores[0]->IndicadorProducto),
      '#open' => TRUE,
    ];

    $form['plan_de_desarrollo']['Vigencia'] = [
      '#title' => t('Vigencia'),
      '#type' => 'number',
      '#default_value' => isset($respuesta[0]->Vigencia) ? $respuesta[0]->Vigencia : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['plan_de_desarrollo']['ProgramacionIndicador'] = [
      '#title' => t('Programado'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->ProgramacionIndicador) ? $respuesta[0]->ProgramacionIndicador : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['plan_de_desarrollo']['UnidadMedida'] = [
        '#title' => 'Seleccione Unidades',
        '#type' => 'select',
        '#required' => TRUE,
        '#default_value' => helpController::getIdTaxonomy('Unidades', $respuesta[0]->UnidadMedida),
        '#empty_option' => t('Selecciona una'),
        '#options' => helpController::getTaxonomyData('Unidades'),
        '#attributes' => [
          'placeholder' => t('Selecciona una'),
          'class' => ['abi-select_control'],
        ],
      ];


    $form['plan_de_desarrollo']['EjecucionIndicador'] = [
        '#title' => t('Ejecucion Indicador'),
        '#type' => 'number',
        '#default_value' => isset($respuesta[0]->EjecucionIndicador) ? $respuesta[0]->EjecucionIndicador : 'Ejecucion Indicador',
        '#disabled' => FALSE,
        '#attributes' => [
          'class' => ['form-control '],
        ],
      ];


      $validators = [
        'file_validate_extensions' => ['pdf'],
        // 10 MB limit.
        'file_validate_size' => [10 * 1024 * 1024],
      ];
  
      $form['plan_de_desarrollo']['url_pdf_programacion'] = [
        '#type' => 'managed_file',
        '#placeholder' => t(' el plan de desarrollo en formato pdf.'),
        '#size' => 20,
        '#description' => t("Limit size") . ' 100 MB. ' . t('Allowed extensions') . ': pdf',
        '#upload_validators' =>$validators,
        '#upload_location' => 'public://'.\Drupal::time()->getRequestTime().'documentos_planes/',
        '#multiple' => false,
        '#attributes' => [
          'class' => ['form-control '],
          'accept' => ['image/x-png,image/jpeg'],
        
        ],
      ];
  


      $text_btn =  helpController::getBtnText($edit,  $delete);
   
     $markup_html_one = isset($edit) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="/programas_indicadores?id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&id_indicador='.$id_indicador.'">Atras</a>' : "";
     $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/indicadores?id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&id_indicador='.$id_indicador.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   

    $form['plan_de_desarrollo']['create_Programa'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
         
    ];
  
    $form['report'] = [
      '#type' => 'details',
      '#title' => t('Programa de Indicadores REPORT'),
      '#open' => TRUE,
    ];

    list($header, $rows, $totales) = ProgramacionIndicadoresController::getReportData();


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
      '#prefix' =>'<div class="card-body">  <div class="table-responsive">',
      '#suffix' =>'<p class="p-report">Total Programado: '.$totales[2].' -Total Ejecutado: '.$totales[4].' - % Avance: ' .$totales[5].'</p></div>',
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
    $edits = !empty($request['id_programacion']) ? $request['id_programacion'] : NULL;
    $id_indicador= !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    if ($delete) {  
      $id=  ProgramacionIndicadoresController::updateDeleteProgrma($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Programa de Indicadores delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$edits){
      $document_url = ProgramacionIndicadoresController::getDocumenUrl($form_state);
       $id = ProgramacionIndicadoresController::addPrograma($form_state, $id_indicador, $document_url);
      \Drupal::logger('Agregar nueva Programa de Indicadores a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
         
        }
        if($edits){
          $id = ProgramacionIndicadoresController::updateProgrma($form_state, $edits);
          \Drupal::logger('Editar Programa de Indicadores por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Programa de Indicadores de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

}