<?php

namespace Drupal\plan_de_desarrollo\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * @file
 * Implement IndicadoresController 
 */

class IndicadoresController extends ControllerBase {
    
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
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

    $rows = [];
    $header = [
      t('#'),
      t('Código Indicador productos'),
      t('Indicador Producto'),
      t('Tipo'),
      t('Codigo Secretaria'),
      t('created'),
      t('Volver sub programa'),
      t('Edit'),
      t('delete'),
      t(''),    
    ];

    $query = \Drupal::database()->select('TblIndicadoresProducto', 'pt');
    $query->fields('pt', [
      'id_indicador',
      'CodIndicadorProducto',
      'IndicadorProducto',
      'Tipo',
      'CodSecretaria',
      'created',
      'id_meta'
    ]);
    if (isset($excel) && isset($id_meta)) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_meta', $id_meta, '=')
        ->limit(20)
        ->element(0);
    }
    if(isset($edit) || isset($delete)){
      $query->condition('id_indicador', isset($edit)  ? $edit : $delete, '=');

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('IndicadorProducto', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

  
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('IndicadorProducto', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/indicadores?delete_id='.$item->id_indicador.'&page='.$page.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      $edit = Url::fromUserInput('/indicadores?id_indicador='.$item->id_indicador.'&page='.$page.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      $url_programa = Url::fromUserInput('/programas_indicadores?id_indicador='.$item->id_indicador.'&page='.$page.'&id_meta='.$id_meta.'&id_subprogramas='.$id_subprogrma.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea);
      $atras_metas = Url::fromUserInput('/metasproducto?id_meta='.$item->id_meta.'&page='.$page);
      
      $row = [
     $item->id_indicador,
     $item->CodIndicadorProducto,
     $item->IndicadorProducto,
     $item->Tipo,
     $item->CodSecretaria,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Metas'), $atras_metas),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('Programación <i class="fas fa-arrow-right"></i>'), $url_programa),
      ];
      $rows[] = $row;
    }
 
    return [$header, $rows];
  }


  
 /**
   * consultar id_indicadors  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_indicador']) ? $request['id_indicador'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblIndicadoresProducto', 'p');
    $query->condition('id_indicador', isset($edi) ? $edi : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar id_indicadors a planes de desarrollo
   * 
   */
public static function addIndicador($form_state, $id_meta){

  $id = \Drupal::database()->insert('TblIndicadoresProducto')
      ->fields([
       'CodIndicadorProducto' => $form_state->getValue('CodIndicadorProducto'),
       'IndicadorProducto' => $form_state->getValue('IndicadorProducto'),
       'id_meta' => $id_meta,
       'Tipo' => $form_state->getValue('Tipo'),
       'CodSecretaria' => $form_state->getValue('CodSecretaria'), 
       'created' => \Drupal::time()->getRequestTime(),
       'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

  /***
   * funcincion para editar id_indicadors id_subpro$id_indicador de desarrollo
   * 
   */
  public static function updateIndicador($form_state, $id_indicador){

    $id = \Drupal::database()->update('TblIndicadoresProducto')
    ->fields([
      'CodIndicadorProducto' => $form_state->getValue('CodIndicadorProducto'),
      'IndicadorProducto' => $form_state->getValue('IndicadorProducto'),
      'Tipo' => $form_state->getValue('Tipo'),
      'CodSecretaria' => $form_state->getValue('CodSecretaria'), 
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_indicador', $id_indicador, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar id_indicadors id_subpro$id_indicador de desarrollo
   * 
   */
  public static function deleteIndicador($form, $delete){
    $id= \Drupal::database()->update('TblIndicadoresProducto')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_indicador', $delete, '=')->execute();
  
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