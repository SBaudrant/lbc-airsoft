(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.jvectormap = {
    attach: function(context, settings) {
      $('#map', context).once('jvectormap')
                        .vectorMap( {
                            map: $("#map").data('loadmap'),
                            regionsSelectable: true,
                            regionStyle: {
                                initial: {
                                    fill: '#B8E186'
                                },
                                selected: {
                                    fill: '#F4A582'
                                }
                            },
                            onRegionSelected: function(){
                              $('#research').val( JSON.stringify( $("#map").vectorMap("get" , "mapObject").getSelectedRegions()).replace(/[\[\]\"]/g, '') )
                            }
                        })

    }
  };
})(jQuery, Drupal);