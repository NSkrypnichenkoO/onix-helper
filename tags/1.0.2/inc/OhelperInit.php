<?php
/**
 * @package onix-meta-box
 */

namespace Onixhelper;

/**
 * Class Init
 * @package Inc
 *
 * need this class to initialisation all classes in the plugin
 * after create new class should write its name to the array of get_services() method
 * should use register() method instead __construct
 */
final class OhelperInit
{

  /**
   * all classes in the plugin that must be initial to work
   * after create new class you just should add its name to thr array bellow
   *
   * @return array full list of classes
   */
  public static function get_services(): array
  {
    return [
      Pages\OhelperAdmin::class,
      System\OhelperEnqueue::class,
      System\OhelperSettingsLinks::class,
      System\OhelperCPTControllerOhelper::class,
      System\OhelperTaxControllerOhelper::class,
      System\OhelperFieldsControllerOhelper::class,
    ];
  }

  /**
   * method to create instance of all classes and call register() method, if exist
   */
  public static function register_services()
  {
    foreach (self::get_services() as $class) {
      $service = self::instantiate($class);

      if (method_exists($service, 'register')) {
        $service->register();
      }
    }
  }

  /**
   * just create instance of class
   * @param $class
   *
   * @return mixed
   */
  private static function instantiate($class)
  {
    return new $class();
  }

}
