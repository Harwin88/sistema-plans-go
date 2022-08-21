<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement fondoController 
 */

class fondoController extends ControllerBase {
    
   
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $CodFondo = !empty($request['CodFondo']) ? $request['CodFondo'] : NULL;
     $linea = !empty($request['linea']) ? $request['linea'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
     $tras = !empty($request['id_plan']) ? $request['id_plan'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Fondo'),
      t('Codigo Vigencia Fondo'),
      t('Codigo Tipo Recurso'),
      t('Codigo Situacion Fondo'),
      t('Codigo Fuente CUIPO'),
      t('created'),
      t('Volver al Fondo'),
      t('Edit'),
      t('delete'),
      t(''),    
    ];

    $query = \Drupal::database()->select('TblFondos', 'pt');
    $query->fields('pt', [
      'CodFondo',
      'Fondo',
      'CodVigenciaFondo',
      'CodTipoRecurso',
      'CodSituacionFondo',
      'CodFuenteCUIPO',
      'created',
    ]);
    if (isset($excel) && isset($id_plan) && !$tras == true) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->limit(20)
        ->element(0);
    }
    if(isset($CodFondo) || isset($delete)){
      $query->condition('CodFondo',   $CodFondo  != NULL ?   $CodFondo  : $delete, '=');

    }else if(isset($linea)) {
      $query->condition('CodFondo',   $linea, '='); 

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('nombre_linea', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('Fondo', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/Fondos?delete_id='.$item->CodFondo.'&id='.$item->id_PlanDesarrollo.'&page='.$page);
      $edit = Url::fromUserInput('/Fondos?CodFondo='.$item->CodFondo."&id=".$item->id_PlanDesarrollo.'&page='.$page);
      $url_sectores = Url::fromUserInput('/sectores?CodFondo='.$item->CodFondo.'&page='.$page);
      $atras_plan = Url::fromUserInput('/plandedesarrollo?id='.$item->id_PlanDesarrollo.'&page='.$page);
      
      $row = [
     $item->CodFondo,
     $item->Fondo,
     $item->CodVigenciaFondo,
     $item->CodTipoRecurso,
     $item->CodSituacionFondo,
     $item->CodFuenteCUIPO,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Plan'), $atras_plan),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('sectores <i class="fas fa-arrow-right"></i>'), $url_sectores),
      ];
      $rows[] = $row;
    }
 
    return [$header, $rows];
  }


  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReporFondoId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $CodFondo = !empty($request['CodFondo']) ? $request['CodFondo'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblFondos', 'p');
    $query->condition('CodFondo',   $CodFondo  != NULL ?   $CodFondo  : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }

 /**
   * consultar Fondos  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['CodFondo']) ? $request['CodFondo'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar Fondos a planes de desarrollo
   * 
   */
public static function addFondos($form_state, $plan){

  $id = \Drupal::database()->insert('TblFondos')
      ->fields([
        'CodFondo'=> $form_state->getValue('CodFondo'),
        'Fondo' => $form_state->getValue('Fondo'),
        'CodVigenciaFondo' => $form_state->getValue('CodVigenciaFondo'),
        'CodTipoRecurso' => $form_state->getValue('CodTipoRecurso'),
        'CodSituacionFondo' => $form_state->getValue('CodSituacionFondo'),
        'CodFuenteCUIPO' => $form_state->getValue('CodFuenteCUIPO'),
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

  /***
   * funcincion para editar Fondos plan de desarrollo
   * 
   */
  public static function updateFondos($form_state, $CodFondos){

    $id = \Drupal::database()->update('TblFondos')
    ->fields([
      'Fondo' => $form_state->getValue('Fondo'),
      'CodVigenciaFondo' => $form_state->getValue('CodVigenciaFondo'),
      'CodTipoRecurso' => $form_state->getValue('CodTipoRecurso'),
      'CodSituacionFondo' => $form_state->getValue('CodSituacionFondo'),
      'CodFuenteCUIPO' => $form_state->getValue('CodFuenteCUIPO'),
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodFondo', $CodFondos, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar Fondos plan de desarrollo
   * 
   */
  public static function deleteFondos($form, $delete){

    $id= \Drupal::database()->update('TblFondos')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodFondo', $delete, '=')->execute();
  
      return $id;
  }
 /***
 * devuelve el texto del boton para el crub 
 * 
 */
public static function getBtnText($edi, $delete){
  $edi= $edi != null? 'Grabar' : 'Agregar';
  $delete= $delete != null ? 'Eliminar' : 'Agregar';
  $text_btn =  $edi == 'Grabar' ? $edi : $delete;

  return $text_btn;
}


}