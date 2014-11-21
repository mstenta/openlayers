<?php
/**
 * @file
 * Contains Openlayers
 */

use Drupal\openlayers\DependencyInjection\CachedContainerBuilder;
use Drupal\openlayers\DependencyInjection\ServiceProviderPluginManager;

/**
 * Static Service Container wrapper.
 *
 * Generally, code in Drupal should accept its dependencies via either
 * constructor injection or setter method injection. However, there are cases,
 * particularly in legacy procedural code, where that is infeasible. This
 * class acts as a unified global accessor to arbitrary services within the
 * system in order to ease the transition from procedural code to injected OO
 * code.
 *
 */
class Openlayers {

  /**
   * The currently active container object.
   *
   * @var \Drupal\openlayers\DependencyInjection\ContainerInterface
   */
  protected static $container;

  /**
   * Initializes the container.
   *
   * This can be safely called from hook_boot() because the container will
   * only be build if we have reached the DRUPAL_BOOTSTRAP_FULL phase.
   *
   * @return bool
   *   TRUE when the container was initialized, FALSE otherwise.
   */
  public static function init() {
    // If this is set already, just return.
    if (isset(static::$container)) {
      return TRUE;
    }

    $service_provider_manager = new ServiceProviderPluginManager();
    // This is an internal API, but we need the cache object.
    $cache = _cache_get_object('cache');

    $container_builder = new CachedContainerBuilder($service_provider_manager, $cache);

    if ($container_builder->isCached()) {
      static::$container = $container_builder->compile();
      return TRUE;
    }

    // If we have not yet fully bootstrapped, we can't build the container.
    if (drupal_bootstrap(NULL, FALSE) != DRUPAL_BOOTSTRAP_FULL) {
      return FALSE;
    }

    // Rebuild the container.
    static::$container = $container_builder->compile();

    return (bool) static::$container;
  }

  /**
   * Returns the currently active global container.
   *
   * @deprecated This method is only useful for the testing environment. It
   * should not be used otherwise.
   *
   * @return \Drupal\openlayers\DependencyInjection\ContainerInterface
   */
  public static function getContainer() {
    return static::$container;
  }

  /**
   * Retrieves a service from the container.
   *
   * Use this method if the desired service is not one of those with a dedicated
   * accessor method below. If it is listed below, those methods are preferred
   * as they can return useful type hints.
   *
   * @param string $id
   *   The ID of the service to retrieve.
   * @return mixed
   *   The specified service.
   */
  public static function service($id) {
    return static::$container->get($id);
  }

  /**
   * Returns an openlayers plugin.
   *
   * @param string $type
   *   The type of the controller plugin, e.g. block, entity, ...
   *
   * @return \Drupal\openlayers\Types\Object|NULL
   *   The instantiated controller with the given type or NULL.
   */
  public static function getComponent($type) {
    return static::$container->get('openlayers.component')->createInstance($type);
  }

  public static function getComponents() {
    $objects = array();
    foreach(static::$container->getDefinitions() as $id => $def) {
      if (!isset($def['plugin module']) || !isset($def['plugin type'])) {
        continue;
      }
      if ($def['plugin module'] == 'openlayers' && $def['plugin type'] == 'Component') {
        $objects[$id] = $def;
      }
    }
    return $objects;
  }

  public static function getOLObjects() {
    $objects = array();
    foreach(static::$container->getDefinitions() as $id => $def) {
      if (!isset($def['plugin module']) || !isset($def['plugin type'])) {
        continue;
      }
      if ($def['plugin module'] == 'openlayers') {
        $objects[$id] = $def;
      }
    }
    return $objects;
  }

}
