<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement SubprogramaController 
 */

class SubprogramaController extends ControllerBase {
    
   
  /**
  * Function.
  */
 public static function getReportData($excel = FALSE) {
   $session = \Drupal::request()->getSession();
  // $search_filter = trim($session->get(planForm::$filterSessionKey));
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
    $id_subprogrma = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;

   $rows = [];
   $header = [
     t('#'),
     t('Codigo sub programa'),
     t('Nompre sub programa'),
     t('Enlace Ir a programa'),
     t('created'),
     t('Edit'),
     t('delete'),
     t(''),    
   ];

   $query = \Drupal::database()->select('TblsubProgramas', 'pt');
   $query->fields('pt', [
     'id_subprogrma',
     'CodSubPrograma',
     'NombreSubPrograma',
     'id_programas',
     'created'
   ]);
   if (isset($excel) && isset($id_programas)) {
     $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
     $query
       ->condition('id_programas', $id_programas, '=')
       ->limit(20)
       ->element(0);
   }
   if(isset($id_subprogrma) || isset($delete)){
     $query->condition('id_subprogrma',   $id_subprogrma  != NULL ?   $id_subprogrma  : $delete, '=');
   }
   if ($search_filter) {
     $or = $query->orConditionGroup();
     $or
       ->condition('NombreSubPrograma', '%' . $search_filter . '%', 'LIKE');
       $query->condition($or);
   }
   $query->condition('estado',   1, '<>');
   $result = $query->orderBy('NombreSubPrograma', 'ASC')
   ->execute();

   $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
   $page = !empty($request['page']) ? $request['page'] : 0;
   $date_formatter = \Drupal::service('date.formatter');

   foreach ($result as $item) {
    // $created = new DrupalDateTime($item->created);
     $delete = Url::fromUserInput('/subprogramas?delete_id=' . $item->id_subprogrma.'&sector=' . $item->id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea. '&page=' . $page);
     $edit = Url::fromUserInput('/subprogramas?id_subprogramas=' . $item->id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea. '&page=' . $page);
     $metas = Url::fromUserInput('/metasproducto?id_subprogramas='.$item->id_subprogrma.'&page='.$page.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
     $programa = Url::fromUserInput('/programas?id_programas=' . $item->id_programas .'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page=' . $page);
     $row = [
    $item->id_subprogrma,
    $item->CodSubPrograma,
    $item->NombreSubPrograma,
    Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Programa'), $programa),
    $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
    Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
    Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
    Link::fromTextAndUrl(t('Metas <i class="fas fa-arrow-right"></i>'), $metas),
     ];
     $rows[] = $row;
   }

   return [$header, $rows];
 }


  /**
   * consultar sectores por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $id_subprogramas = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $edi = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
     
    
    $query = \Drupal::database()->select('TblsubProgramas', 'p');
    $query->condition('id_subprogrma',  $edi != NULL ?  $edi : $delete, '=');
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

public static function addSubPrograma($form_state, $id_programass){
    $id = \Drupal::database()->insert('TblsubProgramas')
    ->fields([
          'CodSubPrograma' => $form_state->getValue('CodSubPrograma'), 
          'NombreSubPrograma' => $form_state->getValue('NombreSubPrograma'),
          'id_programas' =>  $id_programass,
          'created' => \Drupal::time()->getRequestTime(),
          'update' => \Drupal::time()->getRequestTime(),
    ])
      ->execute();


    return $id;
}

/**
 * cambio de estado sectores
 * 
 */

 public static function updateSubPrograma($form_state, $edi){
  $id = \Drupal::database()->update('TblsubProgramas')
   ->fields([
    'CodSubPrograma' => $form_state->getValue('CodSubPrograma'), 
    'NombreSubPrograma' => $form_state->getValue('NombreSubPrograma'),
    'update' => \Drupal::time()->getRequestTime(),
   ])->condition('id_subprogrma',  $edi, '=')->execute();
  return "delete";
 }

 /**
 * cambio de estado sectores
 * 
 */

public static function deleteSubPrograma($delete_id){
  $id = \Drupal::database()->update('TblsubProgramas')
   ->fields([
    'estado' => 1,
    'update' => \Drupal::time()->getRequestTime(),
   ])->condition('id_subprogrma',  $delete_id, '=')->execute();
   return "delete";
 }

}