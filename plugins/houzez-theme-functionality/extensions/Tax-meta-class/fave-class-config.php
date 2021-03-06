<?php
require_once("Tax-meta-class.php");
if (is_admin()){
  $prefix = 'fave_';

  $prop_type = array(
    'id' => 'fave_prop_type_meta',          // meta box id, unique per meta box
    'title' => 'Property Type',          // meta box title
    'pages' => array('property_type'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );

  $prop_type_meta =  new Tax_Meta_Class( $prop_type );
  $prop_type_meta->addImage($prefix.'prop_type_image',array('name'=> __('Property Type Image ','houzez')));
  $prop_type_meta->Finish();

  $prop_type_icon =  new Tax_Meta_Class( $prop_type );
  $prop_type_icon->addImage($prefix.'prop_type_icon',array('name'=> __('Google Map Marker Icon ','houzez') ));
  $prop_type_icon->Finish();

  $prop_type_icon_retina =  new Tax_Meta_Class( $prop_type );
  $prop_type_icon_retina->addImage($prefix.'prop_type_icon_retina',array('name'=> __('Google Map Marker Retina Icon ','houzez') ));
  $prop_type_icon_retina->Finish();

  $prop_city = array(
      'id' => 'fave_prop_type_meta',          // meta box id, unique per meta box
      'title' => 'Property City',          // meta box title
      'pages' => array('property_city', 'property_area', 'property_state'),        // taxonomy name, accept categories, post_tag and custom taxonomies
      'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
      'fields' => array(),            // list of meta fields (can be added by field arrays)
      'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
      'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );

  $prop_city_meta =  new Tax_Meta_Class( $prop_city );
  $prop_city_meta->addImage($prefix.'prop_type_image',array('name'=> __('Thumbnail ','houzez')));
  $prop_city_meta->Finish();

}