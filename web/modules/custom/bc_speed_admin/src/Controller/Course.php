<?php

namespace Drupal\bc_speed_admin\Controller;

use Drupal\bc_speed_admin\Controller\Request;

class Course extends Request {

  public static function get($id = NULL) {
    $obj = new \stdClass();
    $obj->tree = self::getRequest('/v1/courses/tree' . ($id ? '/' . $id : ''));
    $obj->courses = self::getRequest('/v1/courses' . ($id ? '/' . $id : ''));

    if (!empty($obj->courses)) {
      $_courses = array();
      foreach ($obj->courses AS $course ) {
        $_courses[$course->CouseId] = $course;
      }
      $obj->courses = $_courses;
    }

    return $obj;
  }


}
