(function ($, Drupal) {
  'use strict';

  console.log('toto')
  var componentForm = {
    administrative_area_level_1: 'long_name',
    administrative_area_level_2: 'long_name',
    postal_code: 'short_name',
    locality: 'long_name'
  };

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

    //reset des champs
    for (var component in componentForm) {
      document.getElementById(component).value = '';
      document.getElementById(component).disabled = false;
    }

    for (var i in place.address_components) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById(addressType).value = val;
      }
    }

    var longitude = document.getElementById("longitude");
    var latitude = document.getElementById("latitude");
    longitude.value = place.geometry.location.lng();
    latitude.value = place.geometry.location.lat();
  }

  // Initialisation du champs autocomplete
  google.maps.event.addDomListener(window, 'load', function() {
    //initializeAutocomplete('locality');
    initializeAutocomplete('postal_code');
  });

})(jQuery, Drupal);
