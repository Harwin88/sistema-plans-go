<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement ProgramaController 
 */

class ProgramaController extends ControllerBase {
    
   
  /**
  * Function.
  */
 public static function getReportData($excel = FALSE) {
   $session = \Drupal::request()->getSession();
  // $search_filter = trim($session->get(planForm::$filterSessionKey));
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $sector_id = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $edi = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $tras = !empty($request['id_plan']) ? $request['id_plan'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;

   $rows = [];
   $header = [
     t('#'),
     t('Codigo Programa'),
     t('Programa'),
     t('Objetivo'),
     t('Volver al sector'),
     t('created'),
     t('Edit'),
     t('delete'),
     t(''),    
   ];

   $query = \Drupal::database()->select('TblProgramas', 'pt');
   $query->fields('pt', [
     'id_programas',
     'CodPrograma',
     'Programa',
     'Objectivo',
     'id_sector',
     'created'
   ]);
   if (isset($excel) && isset($id_linea) && !$tras == true) {
     $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
     $query
       ->limit(20)
       ->element(0);
   }
   if(isset($sector_id) ){
     $query->condition('id_sector',   $sector_id, '=');
   } 

   if(isset($delete) || isset($edi)){
    $query->condition('id_programas',  isset($delete) ? $delete : $edi , '=');
   }
   if ($search_filter) {
     $or = $query->orConditionGroup();
     $or
       ->condition('Programa', '%' . $search_filter . '%', 'LIKE');
       $query->condition($or);
   }
   $query->condition('estado',   1, '<>');
   $result = $query->orderBy('Programa', 'ASC')
   ->execute();

   $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
   $page = !empty($request['page']) ? $request['page'] : 0;
   $date_formatter = \Drupal::service('date.formatter');

   foreach ($result as $item) {
    // $created = new DrupalDateTime($item->created);
     $delete = Url::fromUserInput('/programas?delete_id='.$item->id_programas.'&page='.$page.'&id='.$id_plan.'&id_sector='.$item->id_sector.'&id_linea='.$id_linea);
     $edit = Url::fromUserInput('/programas?id_programas='.$item->id_programas.'&page='.$page.'&id='.$id_plan.'&id_sector='.$item->id_sector.'&id_linea='.$id_linea);
     $sectores = Url::fromUserInput('/sectores?id_sector='.$item->id_sector.'&id='.$id_plan.'&page='. $page.'&id_linea='.$id_linea);
     $subprograma = Url::fromUserInput('/subprogramas?id_programas='.$item->id_programas.'&page='.$page.'&id_sector='.$sector_id.'&id='.$id_plan.'&id_linea='.$id_linea);
    
     $row = [
    $item->id_programas,
    $item->CodPrograma,
    $item->Programa,
    $item->Objectivo,
    Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Sectores'), $sectores),
    $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
    Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
    Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
    Link::fromTextAndUrl(t('Sub Programa <i class="fas fa-arrow-right"></i>'), $subprograma),
     ];
     $rows[] = $row;
   }

   return [$header, $rows];
 }


  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
  
    $query = \Drupal::database()->select('TblProgramas', 'p');
    $query->condition('id_programas',  isset($edi) ?  $edi : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
  
    return $result;

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




public static function addPrograma($form_state, $id_sector){

    $id = \Drupal::database()->insert('TblProgramas')
    ->fields([
          'CodPrograma' => $form_state->getValue('CodPrograma'), 
          'Programa' => $form_state->getValue('Programa'),
          'Objectivo' =>  $form_state->getValue('objetivo'),
          'id_sector' =>  $id_sector,
          'created' => \Drupal::time()->getRequestTime(),
          'update' => \Drupal::time()->getRequestTime(),
    ])
      ->execute();


    return $id;
}

/**
 * cambio modificar programa de desarrollo
 * 
 */

 public static function updateProgrma($form_state, $edi){
  $id = \Drupal::database()->update('TblProgramas')
   ->fields([
    'CodPrograma' => $form_state->getValue('CodPrograma'), 
    'Programa' => $form_state->getValue('Programa'),
    'Objectivo' =>  $form_state->getValue('objetivo'),
    'update' => \Drupal::time()->getRequestTime(),
])->condition('id_programas',  $edi, '=')->execute();
  return $id;
 }

 /**
 * cambio modificar programa de desarrollo
 * 
 */

public static function updateDeleteProgrma($form_state, $edi){
  $id = \Drupal::database()->update('TblProgramas')
   ->fields([
    'estado' => 1, 
    'update' => \Drupal::time()->getRequestTime(),
])->condition('id_programas',  $edi, '=')->execute();
  return $id;
 }

}