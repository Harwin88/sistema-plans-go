<?php

namespace Drupal\plan_de_desarrollo_presupuesto\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use \Drupal\Core\Database\Transaction;
/**
 * @file
 * Implement pacController 
 */

class pacController extends ControllerBase {
    
   
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $CodAreaFuncional = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
     $subPrograma = !empty($request['subPrograma']) ? $request['subPrograma'] : NULL; 
     $linea = !empty($request['linea']) ? $request['linea'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

     $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;
    
     
    $rows = [];
    $header = [
      t('Mes'),
      t('Valor Programado'),
      t('Valor Adicion'),
      t('Valor Programado Real'),
      t('Valor Ejecutado'),
      t('Saldo'),
      t('Cod Operacion'),
      t('Consecutivo Presupuestos Vigencias Linea Presupuestal'),
      t('created'),
      t('Edit'),
      t('delete'),
    ];

    $query = \Drupal::database()->select('TblSubprograma_Linea_Pac', 'pac');
    $query->fields('pac', [
      'CodDistribucionPAC',
      'Cod_Mes',
      'Valor_Programado',
      'Valor_Adicion',
      'Valor_Programado_Real',
      'Valor_Ejecutado',
      'Saldo',
      'Cod_Operacion',
      'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',
      'created',
    ]);
    if (isset($excel) && isset($id_plan) && !$tras == true) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->limit(20)
        ->element(0);
    }
    if(isset($CodAreaFuncional) || isset($delete)){
      $query->condition('CodAreaFuncional',   $CodAreaFuncional  != NULL ?   $CodAreaFuncional  : $delete, '=');

    }else if(isset($linea)) {
      $query->condition('CodAreaFuncional',   $linea, '='); 

    }
    if(isset($ConsecutivoPresupuestoxVigenciaxLineaPresupuestal)){
      $query->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',   $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal, '='); 
    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('Cod_Mes', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

   
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('Cod_Mes', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {
     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/DescripcionAreaFuncional?delete_id='.$item->CodAreaFuncional.'&id='.$item->id_PlanDesarrollo.'&page='.$page);
      $edit = Url::fromUserInput('/pac?id_edit='.$item->CodDistribucionPAC."&ConsecutivoPresupuestoxVigenciaxLineaPresupuestal=".$item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal.'&subPrograma='.$subPrograma.'&page='.$page);
      $url_sectores = Url::fromUserInput('/sectores?CodAreaFuncional='.$item->CodAreaFuncional.'&page='.$page);
      $atras_plan = Url::fromUserInput('/plandedesarrollo?id='.$item->id_PlanDesarrollo.'&page='.$page);
      
      $row = [
        $item->Cod_Mes,
        number_format($item->Valor_Programado),
        number_format($item->Valor_Adicion),
        number_format($item->Valor_Programado_Real),
        number_format($item->Valor_Ejecutado),
        number_format($item->Saldo),
        $item->Cod_Operacion == 1 ? 'Abierto' : 'Cerrado',
        $item->ConsecutivoPresupuestoxVigenciaxLineaPresupuestal,
        $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
        Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
        Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
      ];
      $rows[] = $row;
    }
 


    return [$header, $rows];
  }


  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReporPacId($id_edit) {
    $query = \Drupal::database()->select('TblSubprograma_Linea_Pac', 'p');
    $query->condition('CodDistribucionPAC',    $id_edit, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }


    /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReporPacMes($mes, $presupuesto_id, $operacion = false) {
    $query = \Drupal::database()->select('TblSubprograma_Linea_Pac', 'p');
    $query->condition('Cod_Mes',    $mes, '=');
    $query->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal', $presupuesto_id, '=');
    $query->condition('Cod_Operacion', $operacion ? 1 : 2, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }

    /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function ObtenerSaldoRestanteAnterior() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;
    $query = \Drupal::database()->select('TblSubprograma_Linea_Pac', 'pac');
    $query->fields('pac', [
      'Cod_Mes',
      'Saldo',
    ])->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',   $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal, '=');;
    $result = $query->execute()->fetchAll();
 if(($result[sizeof($result) - 1]->Cod_Mes + 1) == 13 ){
  return ['Pac Completo', ($result[sizeof($result) - 1]->Cod_Mes + 1) > 13 ? $result[sizeof($result)- 1]->Cod_Mes + 1 : false];
 }
  return [$result[sizeof($result)- 1]->Saldo ? $result[sizeof($result)- 1]->Saldo : 0, $result[sizeof($result)- 1]->Cod_Mes + 1];
 
  }

 /**
   * consultar DescripcionAreaFuncionals  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['CodAreaFuncional']) ? $request['CodAreaFuncional'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('Tblplandesarrollo', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar DescripcionAreaFuncionals a planes de desarrollo
   * 
   */
public static function addPac($form_state, $plan){

  try {
 
  $id = \Drupal::database()->insert('TblSubprograma_Linea_Pac')
         ->fields([
      //  'CodDistribucionPAC'=> $form_state->getValue('CodDistribucionPAC'),
        'Cod_Mes'=> $form_state->getValue('Cod_Mes'),
        'Valor_Programado'=> $form_state->getValue('Valor_Programado'),
        'Valor_Adicion'=> $form_state->getValue('Valor_Adicion'),
        'Valor_Programado_Real'=> $form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion'),
        'Valor_Ejecutado'=> $form_state->getValue('Valor_Ejecutado'),
        'Saldo'=>  ($form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion')) - $form_state->getValue('Valor_Ejecutado'),
        'Cod_Operacion'=> $form_state->getValue('Cod_Operacion'),
        'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'=>  $form_state->getValue('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'),
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
  }
  catch (Exception $e) {
    // Something went wrong somewhere, so roll back now.
    // Log the exception to watchdog.
    \Drupal::logger('type')->error($e->getMessage());
  }
}

  /***
   * funcincion para editar DescripcionAreaFuncionals plan de desarrollo
   * 
   */
  public static function updatePac($form_state, $id_edit){
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal = !empty($request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal']) ? $request['ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'] : NULL;
    $id=0;
    $respuesta = pacController::getReporPacId($id_edit);
    $value_anterior =pacController::getReporPacMes($respuesta[0]->Cod_Mes - 1, $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal);
    $opera = $form_state->getValue('Cod_Operacion') ? $form_state->getValue('Cod_Operacion') : false;
    if($opera == 2){
    
      if($respuesta[0]->Cod_Operacion == 1) {
        $value_siguiente =pacController::getReporPacMes($respuesta[0]->Cod_Mes + 1, $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal, true);
      //  pacController::editMesCierra($id_edit);
        $id = \Drupal::database()->update('TblSubprograma_Linea_Pac')
        ->fields([
          'Valor_Programado_Real'=>  ($value_siguiente[0]->Valor_Programado + $value_siguiente[0]->Valor_Adicion)   +  $respuesta[0]->Saldo,
          'Saldo'=> (($value_siguiente[0]->Valor_Programado + $value_siguiente[0]->Valor_Adicion) - $value_siguiente[0]->Valor_Ejecutado) +  $respuesta[0]->Saldo,
          'update' => \Drupal::time()->getRequestTime(),
      ])
      ->condition('Cod_Mes', $respuesta[0]->Cod_Mes + 1, '=')
      ->condition('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal',  $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal, '=')
      ->execute();
      $valor_real_saldo=0;
      if($respuesta[0]->Valor_Programado_Real){
      $valor_real_saldo =   $value_anterior[0]->Saldo;
      }
      $id = \Drupal::database()->update('TblSubprograma_Linea_Pac')
      ->fields([
        'Cod_Mes'=> $form_state->getValue('Cod_Mes'),
        'Valor_Programado'=> $form_state->getValue('Valor_Programado'),
        'Valor_Adicion'=> $form_state->getValue('Valor_Adicion'),
        'Valor_Programado_Real'=> ($form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion')) +  $valor_real_saldo,
        'Valor_Ejecutado'=> $form_state->getValue('Valor_Ejecutado'),
        'Saldo'=>  (($form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion')) - $form_state->getValue('Valor_Ejecutado')) +  $valor_real_saldo,
        'Cod_Operacion'=> $form_state->getValue('Cod_Operacion'),
        'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'=>  $form_state->getValue('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'),
        'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodDistribucionPAC', $id_edit, '=')->execute();

      }
   //  $value_siguiente =pacController::getReporPacMes($respuesta[0]->Cod_Mes + 1, $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal);
    // pacController::updatePac(Null, $id_edit, $respuesta[0]->Saldo);

    }
 if($opera == 1){
  $valor_real_saldo=0;
  if($respuesta[0]->Valor_Programado_Real){
    $valor_real_saldo =   $value_anterior[0]->Saldo;
  }

  $respuesta = pacController::getReporPacId($id_edit);
    $id = \Drupal::database()->update('TblSubprograma_Linea_Pac')
    ->fields([
    //  'CodDistribucionPAC'=> $form_state->getValue('CodDistribucionPAC'),
      'Cod_Mes'=> $form_state->getValue('Cod_Mes'),
      'Valor_Programado'=> $form_state->getValue('Valor_Programado'),
      'Valor_Adicion'=> $form_state->getValue('Valor_Adicion'),
      'Valor_Programado_Real'=> $form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion') +  $valor_real_saldo,
      'Valor_Ejecutado'=> $form_state->getValue('Valor_Ejecutado'),
      'Saldo'=>  ($form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion')) - $form_state->getValue('Valor_Ejecutado') +   $valor_real_saldo,
      'Cod_Operacion'=> $form_state->getValue('Cod_Operacion'),
      'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'=>  $form_state->getValue('ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'),
      'update' => \Drupal::time()->getRequestTime(),
  ])
  ->condition('CodDistribucionPAC', $id_edit, '=')->execute();
  }
  
  return $id;
  }


  public static function editMesCierra($id_edit){
    if(isset($form_state)){
      $id = \Drupal::database()->update('TblSubprograma_Linea_Pac')
      ->fields([
      //  'CodDistribucionPAC'=> $form_state->getValue('CodDistribucionPAC'),
        'Cod_Mes'=> $form_state->getValue('Cod_Mes'),
        'Valor_Programado'=> $form_state->getValue('Valor_Programado'),
        'Valor_Adicion'=> $form_state->getValue('Valor_Adicion'),
        'Valor_Programado_Real'=> $form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion'),
        'Valor_Ejecutado'=> $form_state->getValue('Valor_Ejecutado'),
        'Saldo'=>  ($form_state->getValue('Valor_Programado') + $form_state->getValue('Valor_Adicion')) - $form_state->getValue('Valor_Ejecutado'),
        'Cod_Operacion'=> $form_state->getValue('Cod_Operacion'),
        'ConsecutivoPresupuestoxVigenciaxLineaPresupuestal'=>  $ConsecutivoPresupuestoxVigenciaxLineaPresupuestal,
        'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodDistribucionPAC', $id_edit, '=')->execute();
  } 
}
  /***
   * funcincion para editar DescripcionAreaFuncionals plan de desarrollo
   * 
   */
  public static function deleteDescripcionAreaFuncionals($form, $delete){

    $id= \Drupal::database()->update('TblSubprograma_Linea_Pac')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('CodAreaFuncional', $delete, '=')->execute();
  
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