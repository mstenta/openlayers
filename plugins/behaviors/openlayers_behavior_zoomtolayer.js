/**
 * @file
 * JS Implementation of OpenLayers behavior.
 */

/**
 * OpenLayers Zoom to Layer Behavior
 */
Drupal.openlayers.addBehavior('openlayers_behavior_zoomtolayer', function (data, options) {
  var map = data.openlayers;
  var layers = map.getLayersBy('drupalID', {
    test: function(id) {
      for (var i in options.zoomtolayer) {
        if (options.zoomtolayer[i] == id && options.layer_handler == 'vector') {
          return true;
        }
      }
      return false;
    }
  });

  // Go through selected layers to get full extent.
  map.fullExtent = new OpenLayers.Bounds();
  for (var i in layers) {
    if (layers[i].features !== undefined) {
      layers[i].events.register('loadend', layers[i], function() {
        var zoomtolayer_scale = data.map.behaviors['openlayers_behavior_zoomtolayer'].zoomtolayer_scale;
        var layerextent = layers[i].getDataExtent().scale(zoomtolayer_scale);
        map.fullExtent.extend(layerextent);
        map.zoomToExtent(map.fullExtent);
        if (layerextent.getWidth() == 0.0) {
          map.zoomTo(options.point_zoom_level);
        }
      });
    } else {
      var layerextent = layers[i].getDataExtent();
      // Check for valid layer extent
      if (layerextent != null) {
        map.fullExtent.extend(layerextent);
        map.zoomToExtent(map.fullExtent);
      }
    }
  }

  // If unable to find width due to single point,
  // zoom in with point_zoom_level option.
  if (map.fullExtent.getWidth() == 0.0) {
    map.zoomTo(options.point_zoom_level);
  }
  else {
    map.zoomToExtent(map.fullExtent.scale(zoomtolayer_scale));
  }
});
