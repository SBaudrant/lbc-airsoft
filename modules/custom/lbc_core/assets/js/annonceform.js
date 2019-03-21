(function ($, Drupal) {
  'use strict';
  
  console.log('toto')
  function delay(callback, ms) {
    let timer = 0;
    return function() {
      let context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  function initializeAutocomplete(id) {
    var element = document.getElementById(id);
    if (element) {
     var autocomplete = new google.maps.places.Autocomplete(element, { types: ['geocode'] });
     google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);
    }
  }

  // Injecte les données dans les champs du formulaire lorsqu'une adresse est sélectionnée
  function onPlaceChanged() {
    var place = this.getPlace();

    for (var i in place.address_components) {
      var component = place.address_components[i];
      for (var j in component.types) {
        var type_element = document.getElementById(component.types[j]);
        if (type_element) {
          type_element.value = component.long_name;
        }
      }
    }

    var longitude = document.getElementById("longitude");
    var latitude = document.getElementById("latitude");
    longitude.value = place.geometry.location.lng();
    latitude.value = place.geometry.location.lat();
  }

  // Initialisation du champs autocomplete
  google.maps.event.addDomListener(window, 'load', function() {
    initializeAutocomplete('ville_autocomplete_address');
  });

})(jQuery, Drupal);
