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
use Drupal\plan_de_desarrollo_presupuesto\Controller\pacController;
use Drupal\plan_de_desarrollo\Controller\PlanController;
use Drupal\plan_de_desarrollo\Controller\helpController;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;

/**
 * Configure example settings for this site.
 */
class pacForm extends ConfigFormBase {
  /*
   * @var string Config settings
   */
  const SETTINGS = 'app.core';

  /**
   * Get form Id string.
   */
  public function getFormId() {
    return 'pac_config_tabs';
  }
   
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
    $subPrograma = !empty($request['subPrograma']) ? $request['subPrograma'] : NULL; 
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_edit = !empty($request['id_edit']) ? $request['id_edit'] : NULL;
    $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $respuesta = [];
    $user_rol = [];
    // $id = 0;
    $user_rol = \Drupal::currentUser()->getRoles();

  //  $respuesta_plan = PlanController::getReportDataId($id_edit);
    if (isset($id_edit) || isset($delete)) {
      $respuesta = pacController::getReporPacId($id_edit);
    } 
 if(!isset($id_edit)){
  [$Saldo, $Mes] = pacController::ObtenerSaldoRestanteAnterior();
 }
   if($Mes != false ||  isset($id_edit)){
    $form['pac'] = [
      '#type' => 'details',
      '#title' => t('Formulario PAC'),
      '#open' => TRUE,
     
    ];

    $form['pac']['Cod_Mes'] = [
      '#type' => 'number',
      '#default_value' => isset($id_edit) ?  $respuesta[0]->Cod_Mes : $Mes,
      '#disabled' => $respuesta[0]->Cod_Operacion == 2 ? TRUE : FALSE,
      '#attributes' => [
        'class' => ['form-control '],
        'placeholder' => t('Mes'),
      ],
      '#prefix' => '<div class="row"><div class="col">',
      '#suffix' => '</div>'
    ];

    $form['pac']['Valor_Programado'] = [
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Valor_Programado) ? $respuesta[0]->Valor_Programado : '',
      '#disabled' => $respuesta[0]->Cod_Operacion == 2 ? TRUE : FALSE,
      '#attributes' => [
        'class' => ['form-control valores_operaction'],
        'placeholder' => t('Valor Programado'),
        'id'=> t('valor_programado'),
        'type'=>"currency",
         'placeholder'=>"$1,000,000.00"
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>',
     
    ];

    $form['pac']['Valor_Adicion'] = [
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->Valor_Adicion) ? $respuesta[0]->Valor_Adicion : '',
      '#disabled' => $respuesta[0]->Cod_Operacion == 2 ? TRUE : FALSE,
      '#attributes' => [
        'class' => ['form-control valores_operaction'],
        'id'=> t('valor_adicion'),
        'placeholder' => t('Valor Adicion'),
      ],
      '#prefix' => '<div class="col">',
      '#suffix' => '</div>'
    ]; 

 

    $form['pac']['Valor_Programado_Real'] = [
        '#type' => 'textfield',
        '#disabled' => true,
        '#default_value' => isset($respuesta[0]->Valor_Programado_Real) ? $respuesta[0]->Valor_Programado_Real : '',
        '#attributes' => [
          'class' => ['form-control '],
          'placeholder' => t('Valor Real'),
          'id'=> t('valor_real'),
        ],
        '#prefix' => '<div class="col">',
        '#suffix' => '</div>'
      ]; 
    
      $form['pac']['Valor_Ejecutado'] = [
        '#type' => 'textfield',
        '#default_value' => isset($respuesta[0]->Valor_Ejecutado) ? $respuesta[0]->Valor_Ejecutado : '',
        '#disabled' => $respuesta[0]->Cod_Operacion == 2 ? TRUE : FALSE,
        '#attributes' => [
          'class' => ['form-control value_ejecutado'],
          'placeholder' => t('Valor Ejecutado'),
          'id'=> t('valor_ejecutado'),
        ],
        '#prefix' => '<div class="col">',
        '#suffix' => '</div>'
      ]; 

      $form['pac']['Saldo'] = [
        '#type' => 'textfield',
        '#default_value' => isset($respuesta[0]->Saldo) ? $respuesta[0]->Saldo : '',
        '#disabled' => $respuesta[0]->Cod_Operacion == 2 ? TRUE : FALSE,
        '#attributes' => [
          'class' => ['form-control '],
          'placeholder' => t('Saldo'),
          'id'=> t('valor_saldo'),
        ],
        '#prefix' => '<div class="col">',
        '#suffix' => '</div>'
      ]; 

      $form['pac']['Cod_Operacion'] = [
        '#type' => 'textfield',
        '#type' => 'select',
        '#required' => FALSE,
        '#default_value' => isset($respuesta[0]->Cod_Operacion) ? ($respuesta[0]->Cod_Operacion == 1 ?  1 : 2 ) : 'Operacion',
        '#empty_option' =>  'Operacion',
        '#options' => [1=>'Abierto', 2=>'Cerrado'],
        '#attributes' => [
          'class' => ['form-control '],
          'placeholder' => t('Codigo Operacion'),
        ],
        '#prefix' => '<div class="col">',
        '#suffix' => '</div>'
      ]; 

      $form['pac']['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] = [
        '#type' => 'textfield',
        '#default_value' => $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal,
        '#disabled' => true,
        '#attributes' => [
          'class' => ['form-control '],
        ],
        '#prefix' => '<div class="col">',
        '#suffix' => '</div>'
      ]; 
      
   // $markup_html_one = isset($id_edit) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split" href="/lineas?id='.$respuesta[0]->id_PlanDesarrollo.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    if(isset($id_edit)){
      $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="pac?ConsecutivoPresupuestoxVigenciaxLineaPresupuestal='.$ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'&subPrograma='.$subPrograma.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
    }else{
      $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="presupuesto?subPrograma='.$subPrograma.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
    }
  ///  $markup_html_one = isset($id_edit) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split" href="/lineas?id='.$respuesta[0]->id_PlanDesarrollo.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
  
  
    $text_btn =  helpController::getBtnText($id_edit,  $delete);
    $form['pac']['create_plan'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' =>  $markup_html_two ,
      '#prefix' => '<div class="col">',
      '#suffix' => '</div></div>'   
    ];
  }else{

    if(isset($id_edit)){
      $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="pac?ConsecutivoPresupuestoxVigenciaxLineaPresupuestal='.$ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
    }else{
      $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="presupuesto?subPrograma='.$subPrograma.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
    }
    $text_btn =  helpController::getBtnText($id_linea,  $delete);
    $form['pac']['create_plan'] = [
      '#markup' => $markup_html_one != "" ? $markup_html_one :  $markup_html_two ,
      '#prefix' => '<div class="col">',
      '#suffix' => '</div></div>'   
    ];
    $form['pac']['mensaje']=[
      '#type' => 'markup',
      '#markup' => $Saldo,
      '#prefix' => '<div class="card bg-light text-black shadow"><div class="border-bottom-primary card-body mensaje">',
      '#suffix' => '</div></div>'  

    ];
  }
    $form['report'] = [
      '#type' => 'details',
      '#title' => t('LINEAS REPORT'),
      '#open' => TRUE,
    ];

    list($header, $rows) = pacController::getReportData();

    $form['pac']['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#attributes' => [
        'id'=>"dataTable",
        'width'=>"100%",
        'cellspacing'=> "0",
        'class' => [' table cell-border table table-bordered  '],
        'id'=>"dataTable",
        'width'=>"100%",
        'cellspacing'=>"0",
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
    $id_edit = !empty($request['id_edit']) ? $request['id_edit'] : NULL;
    
    if ($delete) {  
      $id=  pacController::deleteFondos($form, $delete);
      \Drupal::logger('Editar para cambiar de estado Lineas delete')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 


   if(!$id_edit){
      $id = pacController::addPac($form_state, $plan);
      \Drupal::logger('Agregar nueva LIneas a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
        }
        if($id_edit){
          $id = pacController::updatePac($form_state, $id_edit);
          \Drupal::logger('Editar Lineas por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
          }
  

        return $form;
      }
      catch (Exception $e) {
        \Drupal::logger('error crub Plan de desarrollo')->error("crud error " . print_r($e, 1));
      }
    
  }

  public static function returnInput(array &$form, FormStateInterface $form_state){
    return $form['pac']['Valor_Programado_Real'];
  }
  
  public static function obtenerValor($form_state)
  {
    $sum='$'.'100';
    $sum = $form_state->getValue('Valor_Programado')+$form_state->getValue('Valor_Adicion');
    return $sum;
  }
}
