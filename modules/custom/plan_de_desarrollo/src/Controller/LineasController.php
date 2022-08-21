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
 * Implement LineasController 
 */

class LineasController extends ControllerBase {
    
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
     $linea = !empty($request['linea']) ? $request['linea'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
     $tras = !empty($request['id_plan']) ? $request['id_plan'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Lineas Codigo'),
      t('Lineas'),
      t('created'),
      t('Volver al Plan'),
      t('Edit'),
      t('delete'),
      t(''),    
    ];

    $query = \Drupal::database()->select('TblLineas', 'pt');
    $query->fields('pt', [
      'id_linea',
      'cod_linea',
      'nombre_linea',
      'created',
      'id_PlanDesarrollo'
    ]);
    if (isset($excel) && isset($id_plan) && !$tras == true) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_PlanDesarrollo', $id_plan, '=')
        ->limit(20)
        ->element(0);
    }
    if(isset($id_linea) || isset($delete)){
      $query->condition('id_linea',   $id_linea  != NULL ?   $id_linea  : $delete, '=');

    }else if(isset($linea)) {
      $query->condition('id_linea',   $linea, '='); 

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('nombre_linea', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('cod_linea', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/lineas?delete_id='.$item->id_linea.'&id='.$item->id_PlanDesarrollo.'&page='.$page);
      $edit = Url::fromUserInput('/lineas?id_linea='.$item->id_linea."&id=".$item->id_PlanDesarrollo.'&page='.$page);
      $url_sectores = Url::fromUserInput('/sectores?id_linea='.$item->id_linea.'&id='.$id_plan.'&page='.$page);
      $atras_plan = Url::fromUserInput('/plandedesarrollo?id='.$item->id_PlanDesarrollo.'&page='.$page);
      
      $row = [
     $item->id_linea,
     $item->cod_linea,
     $item->nombre_linea,
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
  public static function getReporLineaId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblLineas', 'p');
    $query->condition('id_linea',   $id_linea  != NULL ?   $id_linea  : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }

 /**
   * consultar Lineas  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar Lineas a planes de desarrollo
   * 
   */
public static function addLineas($form_state, $plan){

  $id = \Drupal::database()->insert('TblLineas')
      ->fields([
        'cod_linea' => $form_state->getValue('cod_linea'), 
        'nombre_linea' => $form_state->getValue('nombre_linea'),
         'id_PlanDesarrollo' =>  $plan,
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

  /***
   * funcincion para editar lineas plan de desarrollo
   * 
   */
  public static function updateLineas($form_state, $id_lineas){

    $id = \Drupal::database()->update('TblLineas')
    ->fields([
          'cod_linea' => $form_state->getValue('cod_linea'), 
          'nombre_linea' => $form_state->getValue('nombre_linea'),
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_linea', $id_lineas, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar lineas plan de desarrollo
   * 
   */
  public static function deleteLineas($form, $delete){

    $id= \Drupal::database()->update('TblLineas')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_linea', $delete, '=')->execute();
  
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