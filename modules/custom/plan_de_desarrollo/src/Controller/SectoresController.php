<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @file
 * Implement SectoresController 
 */

class SectoresController extends ControllerBase {
    
   
  /**
  * Function.
  */
 public static function getReportData($excel = FALSE) {
   $session = \Drupal::request()->getSession();
  // $search_filter = trim($session->get(planForm::$filterSessionKey));
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $sector_id = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $id_plan = !empty($request['id']) ? $request['id'] : NULL;
    
   $rows = [];
   $header = [
     t('#'),
     t('Codigo Sector'),
     t('Sector'),
     t('Linea Enlace'),
     t('created'),
     t('Edit'),
     t('delete'),
     t(''),    
   ];

   $query = \Drupal::database()->select('TblSectores', 'pt');
   $query->fields('pt', [
     'id_sector',
     'CodSector',
     'Sector',
     'id_linea',
     'created'
   ]);
   if (isset($excel) && isset($id_linea) && !$tras == true) {
     $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
     $query
       ->condition('id_linea', $id_linea, '=')
       ->limit(20)
       ->element(0);
   }
   if(isset($sector_id) || isset($delete)){
     $query->condition('id_sector',   $sector_id  != NULL ?   $sector_id  : $delete, '=');
   }
   if ($search_filter) {
     $or = $query->orConditionGroup();
     $or
       ->condition('Sector', '%' . $search_filter . '%', 'LIKE');
       $query->condition($or);
   }
   $query->condition('estado',   1, '<>');
   $result = $query->orderBy('Sector', 'ASC')
   ->execute();

   $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
   $page = !empty($request['page']) ? $request['page'] : 0;
   $date_formatter = \Drupal::service('date.formatter');

   foreach ($result as $item) {
    // $created = new DrupalDateTime($item->created);
     $delete = Url::fromUserInput('/sectores?delete_id=' . $item->id_sector.'&id_linea=' . $item->id_linea.'&id='.$id_plan.'&page=' . $page);
     $edit = Url::fromUserInput('/sectores?id_sector=' . $item->id_sector.'&id_linea=' . $item->id_linea .'&id='.$id_plan.'&page=' . $page);
     $programas = Url::fromUserInput('/programas?id_sector='.$item->id_sector.'&id_linea='.$id_linea.'&id='.$id_plan.'&page='.$page);
     $lineas = Url::fromUserInput('/lineas?id_linea=' . $item->id_linea.'&id='.$id_plan.'&page=' . $page);
     $row = [
    $item->id_sector,
    $item->CodSector,
    $item->Sector,
    Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Linea'), $lineas),
    $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
    Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
    Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
    Link::fromTextAndUrl(t('Programas <i class="fas fa-arrow-right"></i>'), $programas),
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
    $edi = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblSectores', 'p');
    $query->condition('id_sector',  $edi != NULL ?  $edi : $delete, '=');
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

public static function addSectores($form_state, $id_lineas){
    $id = \Drupal::database()->insert('TblSectores')
    ->fields([
          'CodSector' => $form_state->getValue('CodSector'), 
          'Sector' => $form_state->getValue('Sector'),
          'id_linea' =>  $id_lineas,
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

 public static function updateSector($form_state, $edi){
  $id = \Drupal::database()->update('TblSectores')
   ->fields([
         'CodSector' => $form_state->getValue('CodSector'), 
         'Sector' => $form_state->getValue('Sector'),
         'update' => \Drupal::time()->getRequestTime(),
   ])->condition('id_sector',  $edi, '=')->execute();
  return "delete";
 }

 /**
 * cambio de estado sectores
 * 
 */

public static function deleteSector($delete_id){
  $id = \Drupal::database()->update('TblSectores')
   ->fields([
    'estado' => 1,
    'update' => \Drupal::time()->getRequestTime(),
   ])->condition('id_sector',  $delete_id, '=')->execute();
   return "delete";
 }

}