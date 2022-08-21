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
 * Implement MetasController 
 */

class MetasController extends ControllerBase {
    
   
 /**
   * Function.
   */
  public static function getReportData($excel = FALSE) {
    $session = \Drupal::request()->getSession();
   // $search_filter = trim($session->get(planForm::$filterSessionKey));
     $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
     $id_subprogramas = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
     $id_programas = !empty($request['id_programas']) ? $request['id_programas'] : NULL;
     $id_plan = !empty($request['id']) ? $request['id'] : NULL;
     $id_linea = !empty($request['id_linea']) ? $request['id_linea'] : NULL;
     $id_sector = !empty($request['id_sector']) ? $request['id_sector'] : NULL;
     $edit = !empty($request['id_metas']) ? $request['id_metas'] : NULL;
     $id_meta = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
     $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;

    $rows = [];
    $header = [
      t('#'),
      t('Código Meta productos'),
      t('Meta Producto'),
      t('Tipo'),
      t('Peso Metas'),
      t('created'),
      t('Volver sub programa'),
      t('Edit'),
      t('delete'),
      t(''),    
    ];

    $query = \Drupal::database()->select('TblMetasProducto', 'pt');
    $query->fields('pt', [
      'id_meta',
      'CodMetaProducto',
      'MetaProducto',
      'Tipo',
      'PesoMetas',
      'created',
      'id_subprogrma'
    ]);
    if (isset($excel) && isset($id_meta)) {
      $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender');
      $query
        ->condition('id_meta', $id_meta, '=')
        ->limit(20)
        ->element(0);
    }
    if(isset($id_subprogramas) || isset($delete)){
      $query->condition('id_subprogrma',   $id_subprogramas  != NULL ?   $id_subprogramas  : $delete, '=');

    }else if(isset($id_metas)) {
      $query->condition('id_subprogrma',   $id_metas, '='); 

    }
    if ($search_filter) {
      $or = $query->orConditionGroup();
      $or
        
        ->condition('MetaProducto', '%' . $search_filter . '%', 'LIKE');
        $query->condition($or);
    }

    if(isset($id_subprogrma)){
      
    }
    $query->condition('estado',   1, '<>');
    $result = $query->orderBy('MetaProducto', 'ASC')
    ->execute();

    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $page = !empty($request['page']) ? $request['page'] : 0;
    $date_formatter = \Drupal::service('date.formatter');

    foreach ($result as $item) {

     // $created = new DrupalDateTime($item->created);
      $delete = Url::fromUserInput('/metasproducto?delete_id='.$item->id_meta.'&id_subprogramas='.$id_subprogramas.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      $edit = Url::fromUserInput('/metasproducto?id_meta='.$item->id_meta.'&id_subprogramas='.$id_subprogramas.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      $url_indicadores = Url::fromUserInput('/indicadores?id_meta='.$item->id_meta.'&id_subprogramas='.$id_subprogramas.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      $atras_sub_programa = Url::fromUserInput('/subprogramas?id_subprogramas='.$id_subprogramas.'&id_programas='.$id_programas.'&id_sector='.$id_sector.'&id='.$id_plan.'&id_linea='.$id_linea.'&page='.$page);
      
      $row = [
     $item->id_meta,
     $item->CodMetaProducto,
     $item->MetaProducto,
     $item->Tipo,
     $item->PesoMetas,
     $date_formatter->format($item->created, 'custom', 'd/m/Y H:i:s'),
     Link::fromTextAndUrl(t('<i class="fas fa-arrow-left"></i> Sub Programa'), $atras_sub_programa),
     Link::fromTextAndUrl(t('<i class="fas fa-edit"></i>'), $edit),
     Link::fromTextAndUrl(t('<i class="fas fa-trash"></i>'), $delete),
     Link::fromTextAndUrl(t('Indicadores <i class="fas fa-arrow-right"></i>'), $url_indicadores),
      ];
      $rows[] = $row;
    }
 
    return [$header, $rows];
  }


  /**
   * consultar plan de desarrollo por id 
   * 
   */
  public static function getReporid_metasId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edit = !empty($request['id_meta']) ? $request['id_meta'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblMetasProducto', 'p');
    $query->condition('id_meta',   $edit  != NULL ?   $edit  : $delete, '=');
    $query->fields('p');
    $result = $query->execute()->fetchAll();

    return $result;

  }

 /**
   * consultar id_metass  de desarrollo por id 
   * 
   */
  public static function getReportDataId() {
    $request = array_merge(\Drupal::request()->request->all(), \Drupal::request()->query->all());
    $edi = !empty($request['id_subprogramas']) ? $request['id_subprogramas'] : NULL;
    $delete = !empty($request['delete_id']) ? $request['delete_id'] : NULL;
    $query = \Drupal::database()->select('TblMetasProducto', 'p');
    $query->condition('id',  $edi != NULL ?  $edi : $delete, '=')->condition('estado', 1, '<>');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  /***
   * funcion que permite agregar id_metass a planes de desarrollo
   * 
   */
public static function addMetas($form_state, $id_subprogrma){

  $id = \Drupal::database()->insert('TblMetasProducto')
      ->fields([
       'CodMetaProducto' => $form_state->getValue('CodMetaProducto'),
       'MetaProducto' => $form_state->getValue('MetaProducto'),
       'id_subprogrma' => $id_subprogrma,
       'Tipo' => $form_state->getValue('Tipo'),
       'PesoMetas' => $form_state->getValue('PesoMetas'),
        'created' => \Drupal::time()->getRequestTime(),
        'update' => \Drupal::time()->getRequestTime(),
  ])
    ->execute();

    return $id;
}

  /***
   * funcincion para editar id_metass id_subpro$id_subprogrma de desarrollo
   * 
   */
  public static function updateMetas($form_state, $id_meta){

    $id = \Drupal::database()->update('TblMetasProducto')
    ->fields([
      'CodMetaProducto' => $form_state->getValue('CodMetaProducto'),
      'MetaProducto' => $form_state->getValue('MetaProducto'),
      'Tipo' => $form_state->getValue('Tipo'),
      'PesoMetas' => $form_state->getValue('PesoMetas'),
      'CodSecretaria' => $form_state->getValue('CodSecretaria'), 
      'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_meta', $id_meta, '=')->execute();
  
      return $id;
  }

  /***
   * funcincion para editar id_metass id_subpro$id_subprogrma de desarrollo
   * 
   */
  public static function deleteMetas($form, $delete){

    $id= \Drupal::database()->update('TblMetasProducto')
    ->fields([
          'estado' => 1,
          'update' => \Drupal::time()->getRequestTime(),
    ])
    ->condition('id_meta', $delete, '=')->execute();
  
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


  /**
   * Function.
   */
  public static function getTaxonomyData($taxonomy_name) {
    $term_data = [];
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($taxonomy_name);
    foreach ($terms as $term) {
      $term_data[$term->name] = $term->name;
    }
    return $term_data;
  }

    /**
   * To get all cities from taxonomies.
   */
  public static function getIdTaxonomy($name_taxonomy, $name) {
    $vid = $name_taxonomy;
    $id_brand = '';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      if ($term->name == $name) {
        $id_brand = $term->tid;
        return $id_brand;
      }
    }
    return '';
  }



}