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
use Drupal\plan_de_desarrollo\Controller\helpController;

/**
 * Configure example settings for this site.
 */
class PlanDeDesarrolloForm extends ConfigFormBase {
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
    $edi = !empty($request['id']) ? $request['id'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $respuesta = [];
    $user_rol = [];
    // $id = 0;
    $user_rol = \Drupal::currentUser()->getRoles();

    if (isset($edi) || isset($delete)) {
      $respuesta = PlanController::getReportDataId();
    } 
 
    $form['plan_form'] = [
      '#type' => 'details',
      '#title' => t('plan de desarrollo Formulario'),
      '#open' => TRUE,
    ];


    $form['plan_form']['NombrePlanDesarrollo'] = [
      '#placeholder' => t('Nombre del plan'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => isset($respuesta[0]->NombrePlanDesarrollo) ? $respuesta[0]->NombrePlanDesarrollo : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['plan_form']['NombreGobernador'] = [
      '#placeholder' => t('Nombre Gobernador'),
      '#type' => 'textfield',
      '#required' => true,
      '#default_value' => isset($respuesta[0]->NombreGobernador) ? $respuesta[0]->NombreGobernador : '',
     
         '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['plan_form']['Cedula'] = [
      '#placeholder' => t('Cedula'),
      '#type' => 'number',
      '#requiere' => true,
      '#default_value' => isset($respuesta[0]->Cedula) ? $respuesta[0]->Cedula : '',
      '#required' => true,
      '#attributes' => [
        'class' => ['form-control '],
        'max'=> "999999999",
        'min'=>"10000", 
     
      ],

    ];

    $form['plan_form']['Vigencia_Inicio'] = [
      '#placeholder' => t('Vigencia Inicio'),
      '#type' => 'date',
      '#required' => true,
      '#default_value' => isset($respuesta[0]->Vigencia_Inicio) ? $respuesta[0]->Vigencia_Inicio : NULL,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['plan_form']['Vigencia_Terminacion'] = [
      '#placeholder' => t('Vigencia Terminacion'),
      '#type' => 'date',
      '#required' => true,
      '#default_value' => isset($respuesta[0]->Vigencia_Terminacion) ? $respuesta[0]->Vigencia_Terminacion : NULL,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];


    $validators = [
      'file_validate_extensions' => [' jpeg jpg png'],
      // 10 MB limit.
      'file_validate_size' => [10 * 1024 * 1024],
    ];

    $form['plan_form']['url_documentos'] = [
      '#type' => 'managed_file',
      '#placeholder' => t('Cargue imagen (png o jpg) de su documento.'),
      '#size' => 20,
      '#requiere' => true,
      '#description' => t("Limit size") . ' 100 MB. ' . t('Allowed extensions') . ':jpeg jpg png',
      '#upload_validators' =>$validators,
      '#upload_location' => 'public://documentos_planes/',
      '#multiple' => false,
      '#attributes' => [
        'class' => ['form-control '],
        'accept' => ['image/x-png,image/jpeg'],
 
      ],
     // '#default_value' => !empty($edi) ? [EventApiController::getImageId($edi, 'd')] : NULL,
    ];


    $validators = [
      'file_validate_extensions' => ['pdf'],
      // 10 MB limit.
      'file_validate_size' => [10 * 1024 * 1024],
    ];

    $form['plan_form']['url_pdf_plan'] = [
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
   // $url_file_public= explode("public:", $respuesta[0]->AdjuntoPlanDesarrollo);
  
    $text_btn =  helpController::getBtnText($edi, $delete);
    $uel_document_global= $respuesta[0]->AdjuntoPlanDesarrollo;
    $url_file_public= explode("public:", $respuesta[0]->AdjuntoPlanDesarrollo);
    $form['plan_form']['create_plan'] = [
      '#type' => 'submit',
      '#value' => t($text_btn),
      '#attributes' => [
        'class' => [' btn '],
      ],
      '#markup' =>   isset($uel_document_global) ? '<a  class="btn btn-light btn-icon-split" href="/plandedesarrollo"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "" ,
  ];
 
//  $img_files =  explode(",",  $url_file_public[1]);

 // $request = \Drupal::service('request_stack')->getCurrentRequest();



  //$request = \Drupal::service('request_stack')->getCurrentRequest();
  //$url_img = 'https://'.$request->getHost().':'.$request->getPort().'/sites/default/files'.$img_files[0];
   //   $url_pdf = 'https://'.$request->getHost().':'.$request->getPort().'/sites/default/files'.$url_file_public[2];
   if(isset($uel_document_global)){

        $url_documentos = helpController::urlView($uel_document_global);
        $markup = '<img src="'.$url_documentos[0].'" alt="Img" >'; 
        $form['plan_form']['visorpdf'] = [
          '#type' => 'details',
          '#title' => t('Imagenes '),
        ];
        $form['plan_form']['visorpdf']['field_mycustommarkup'] = [
            '#type' => 'markup',
            '#markup' => $markup,
            '#weight' => 1,            
        ];

        $form['plan_form']['pdf_plan'] = [
          '#type' => 'details',
          '#title' => t('Archivos pdf'),
      
        ];
         
       
       $markup = ' <p>Open a PDF file <a href="'.$url_documentos[1].'">Ver plan</a>.</p>'; 
       $form['plan_form']['pdf_plan']['field_mycustommarkup'] = [
           '#type' => 'markup',
           '#markup' => $markup,
           '#weight' => 1,            
       ];
  }
      

    $form['report'] = [
      '#type' => 'details',
      '#title' => t('Reporte Plan de desarrollo'),
      '#open' => TRUE,
    ];

    list($header, $rows) = PlanController::getReportData();


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

    $form['#cache']['contexts'][] = 'session';

    return $form;
  }

  /**
   * Function validation .
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('NombrePlanDesarrollo') == 'Hola') {
      $form_state->setErrorByName('NombrePlanDesarrollo', $this->t('Nombre de plan de desarrollo es obligatorio'));
    }
    return $form_state;
  }

  /**
   * Public function addmoreCallback_config(array &$form, FormStateInterface $form_state)
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id']) ? $request['id'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $btn_action = strval($form_state->getValues()['op']);
    $current_user = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($current_user->id());
    $id= Null;

    if ($delete) {  
      $id=  PlanController::deletePlan($delete);
      \Drupal::logger('Editar para cambiar de estado plan de desarrollo')->info("user email::".$user->get('mail')->value.' id registro: '.$id);
      return $form;
    } 

    if ($btn_action == t('Filter')) {
      $filter = trim($form_state->getValue('filter'));
      $session = \Drupal::request()->getSession();
      $session->set(planForm::$filterSessionKey, $filter);
      $form_state->setRedirect('plan.admin.plan.config');
    }
    elseif ($btn_action == t('Clear filter')) {
      $session = \Drupal::request()->getSession();
      $session->set(planForm::$filterSessionKey, '');
    }
    else {
      try {
        if(($form_state->getValue(['url_documentos', 0])) || ($form_state->getValue(['url_pdf_plan', 0]))){
         $document_url = PlanController::getDocumenUrl($form_state);
         if ($edi) {
          $id= PlanController::editPlan($edi, $form_state, $document_url);
          \Drupal::logger('Editar columnas plan de desarrollo')->info("user email::".$user->get('mail')->value.' id registro:'.$id);
          return $form;
        }
        }else if($edi){
          $respuesta = PlanController::getReportDataId();
          PlanController::editPlan($edi, $form_state,  $respuesta[0]->AdjuntoPlanDesarrollo);
          return $form;
        }
       
         $id= PlanController::addPlan($form_state, $document_url); 
        \Drupal::logger('Agregar un nuevo plan de desarrollo')->info("user email::".$user->get('mail')->value.' id registro:'.$id); 
        return $form;
      }
      catch (Exception $e) {   
        \Drupal::logger('Modulo Plan de desarrollo: Error crud Planes de desarrollo ')->info("user email::".$user->get('mail')->value.' id registro:'.$id);
      }
    }
  }

}
