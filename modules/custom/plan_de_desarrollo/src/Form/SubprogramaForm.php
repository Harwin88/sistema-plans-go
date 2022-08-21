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
use Drupal\plan_de_desarrollo\Controller\SubprogramaController;
use Drupal\plan_de_desarrollo\Controller\helpController;


/**
 * Configure example settings for this site.
 */
class SubprogramaForm extends ConfigFormBase {
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
    $edi = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;

    $respuesta_sectores = ProgramaController::getReportDataId();
    if(isset($edi) == true || isset($delete) == true){
      $respuesta = SubprogramaController::getReportDataId();
    }

    $form['sub_progrma_form'] = [
      '#type' => 'details',
      '#title' => t('Agregar sub programa al Programas: '.$respuesta_sectores[0]->Programa),
      '#open' => TRUE,
    ];

    $form['sub_progrma_form']['CodSubPrograma'] = [
      '#title' => t('Codigo sub Programa'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->CodSubPrograma) ? $respuesta[0]->CodSubPrograma : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];

    $form['sub_progrma_form']['NombreSubPrograma'] = [
      '#title' => t('sub Programa'),
      '#type' => 'textfield',
      '#default_value' => isset($respuesta[0]->NombreSubPrograma) ? $respuesta[0]->NombreSubPrograma : '',
      '#disabled' => FALSE,
      '#attributes' => [
        'class' => ['form-control '],
      ],
    ];


    $text_btn =  helpController::getBtnText($edi,  $delete);
   
   
    $markup_html_one = isset($edi) ||  isset($delete) ? '<a  class="btn btn-light btn-icon-split"  href="subprogramas?id_programas='.$id_programas.'&id='.$id_plan.'&id_linea='.$id_linea.'&id_sector='.$id_sector.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>' : "";
    $markup_html_two = '<a  class="btn btn-light btn-icon-split" href="/programas?id='.$id_plan.'&id_linea='.$id_linea.'&id_sector='.$id_sector.'"><span class="icon text-gray-600"><i class="fas fa-arrow-left"></i></span><span class="text">Atras</span></i></a>';
   

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

 list($header, $rows) = SubprogramaController::getReportData();

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
        $edi = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
        $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
        $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
          $current_user = \Drupal::currentUser();
          $user = \Drupal\user\Entity\User::load($current_user->id());
      
         if(isset($edi) != true && isset($delete) != true){
             $id = SubprogramaController::addSubPrograma($form_state, $id_programas);
            \Drupal::logger('Agregar nueva Progrma a planes')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
             }
          if($edi){
            $id = SubprogramaController::updateSubPrograma($form_state, $edi);
            \Drupal::logger('Editar Programa por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$id);
            }

            if($delete){
              $id = SubprogramaController::deleteSubPrograma($delete);
              \Drupal::logger('Eliminar Programa por')->info("user email:: ".$user->get('mail')->value.' id registro: '.$delete);
              }
        
      
              return $form;
            }
            catch (Exception $e) {
              \Drupal::logger('error crub Plan de desarrollo')->error("crud error " . print_r($e, 1));
            }
          
        }



}
