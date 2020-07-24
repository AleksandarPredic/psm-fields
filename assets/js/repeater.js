/**
 * PSM Fields Repeater field
 */
(function ($) {

  "use strict";

  document.addEventListener('DOMContentLoaded', function () {

    $('.psmfields__field--repeater').each(function (index, element) {

      var repeater = $(this);
      var container = repeater.find('.psmfields__repeater');

      // Add new repeater group
      repeater.on('click', '.psmfields__repeater-add', function (event) {
        event.preventDefault();

        var group = container.find('> .psmfields__repeater-group:first-child').clone();

        // Reset all fields and trigger changes to trigger bind listeners
        group.find('input[type="text"]').val('').trigger('change');
        // TODO: Trigger some event here if we have more uploaders in the future to handle reset in it's script
        group.find('.psmfields__uploader-input').val('').trigger('change'); // Reset uploader value
        group.find('.psmfields__uploader-preview').html(''); // Reset uploader preview
        group.find('input[type="checkbox"]').prop("checked", false).trigger('change');
        group.find('select').prop('selectedIndex',0).trigger('change');
        group.find('textarea').val('').trigger('change');

        container.append(group);
      });

      // Remove repeater group
      repeater.on('click', '.psmfields__repeater-remove', function (event) {
        event.preventDefault();

        var $this = $(this);

        var groups = container.find('> .psmfields__repeater-group');
        if (groups.length < 2) {
          return false;
        }

        $(this).parent('div').remove();

      });

      repeater.on('click', '.psmfields__repeater-up, .psmfields__repeater-down', function (event) {
        event.preventDefault();
        var self = $(event.target);
        var group = self.parent('div');

        if (self.is('.psmfields__repeater-up')) {
          group.prev().before(group);
        } else {
          group.next().after(group);
        }
      });

    });

  });

})(jQuery);
