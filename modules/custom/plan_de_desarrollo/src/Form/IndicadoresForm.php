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
use Drupal\plan_de_desarrollo\Controller\IndicadoresController;
use Drupal\plan_de_desarrollo\Controller\SubprogramaController;
use Drupal\plan_de_desarrollo\Controller\MetasController;
use Drupal\plan_de_desarrollo\Controller\helpController;


/**
 * Configure example settings for this site.
 */
class IndicadoresForm extends ConfigFormBase {
  /*
   * @var string Config settings
   */
  const SETTINGS = 'app.core';

  /**
   * Filter.
   *
   * @var string
   */
  public static $filterSessionKey = 'indicadores_filter';

  /**
   * Get form Id string.
   */
  public function getFormId() {
    return 'plan_de_desarrollo_config_tabs';
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
    $edit = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL; 
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

     $respuesta_metas = MetasController::getReporid_metasId();
    if (isset($edit) || isset($delete)) {
      $respuesta = IndicadoresController::getReportDataId();
    } 

    $form['indicadores'] = [
      '#type' => 'details',
      '#title' => t('Agrgar Indicadores Metas: '.$respuesta_metas[0]->MetaProducto),
      '#open' => TRUE,
    ];

    $form['indicadores']['CodIndicadorProducto'] = [
      '#title' => t('Code Indicadores'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodIndicadorProducto) ? $respuesta[0]->CodIndicadorProducto : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['indicadores']['IndicadorProducto'] = [
      '#title' => t('Indicadores'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->IndicadorProducto) ? $respuesta[0]->IndicadorProducto : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['indicadores']['Tipo'] = [
        '#title' => t('Tipos'),
        '#type' => 'number',
        '#default_value' => isset($respuesta[0]->Tipo) ? $respuesta[0]->Tipo : '',
        '#disabled' => FALSE,
        '#attributes' => [
          'class' => ['form-control '],
        ],
      ];

      /*$form['indicadores']['CodSecretaria'] = [
        '#title' => t('Codigo Secretaria'),
        '#type' => 'textfield',
        '#default_value' => isset($respuesta[0]->CodSecretaria) ? $respuesta[0]->CodSecretaria : '',
        '#disabled' => FALSE,
        '#attributes' => [
          'class' => ['form-control '],
        ],
      ];*/

  
      $form['indicadores']['CodSecretaria'] = [
        '#title' => 'Seleccione una Secretaria',
        '#type' => 'select',
        '#required' => TRUE,
        '#default_value' => helpController::getIdTaxonomy('secretarias', $respuesta[0]->CodSecretaria),
        '#empty_option' => t('Selecciona una'),
        '#options' => helpController::getTaxonomyData('secretarias'),
        '#attributes' => [
          'placeholder' => t('Selecciona una'),
          'class' => ['abi-select_control'],
        ],
      ];

      $text_btn =  helpController::getBtnText($edit,  $delete);
      
     $markup_html_one = isset($edit) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="/indicadores?id_subprogramas='.$id_subprogrma.'&id_meta='.$id_meta.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
     $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/metasproducto?id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_meta='.$id_meta.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   

    $form['indicadores']['create_indicadores'] = [
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

    list($header, $rows) = IndicadoresController::getReportData();


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
    $edits = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    if ($delete) {  
      $id=  IndicadoresController::deleteIndicador($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Metas delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$edits){
       $id = IndicadoresController::addIndicador($form_state, $id_meta);
      \Drupal::logger('Agregar nueva Metas a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);

        }
        if($edits){
          $id = IndicadoresController::updateIndicador($form_state, $edits);
          \Drupal::logger('Editar Metas por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Metas de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

}
