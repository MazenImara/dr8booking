(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dr8booking = {
    attach: function (context, settings) {
      $('body', context).once('dr8booking').each(function () {//start
        var node = drupalSettings.dr8booking.node;
        if (node != null) {
          if (node.status == '0') {
            $('.book_link_node').after('<h1 style="color: red;">This item is not availble</h1>');
            $('.book_link_node').css('color', 'white');
          }
          else{
            $('.book_link_node').click(function(event) {
              window.location.href = '/booking/book/' + node.id + '/' +  'node';
            });
          }
        }

        if (node == null) {
          samePage = '/bookingitems';
        }
        else{
          samePage = window.location.pathname;
        }

        $('.book_link').click(function(event) {
          window.location.href = '/booking/book/' + $(this).attr('id') + samePage;
        });



console.log(node);



      }); //end
    }
  };
})(jQuery, Drupal, drupalSettings);


