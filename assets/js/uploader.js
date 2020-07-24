/**
 * PSMFields uploader using media library
 *
 * global psmfieldsUploaderField
 */
(function($){

  "use strict";

  // Metabox object
  var metabox = $('.psmfields');

  // Used fields classes
  var classButton = '.psmfields__uploader-add';
  var classInput = '.psmfields__uploader-input';
  var classPreview = '.psmfields__uploader-preview';
  var classWrapper = '.psmfields__field';

  // Handle widget admin screen init
  $(window).on('load', function(){

    metabox.on('click', classButton, uploadFile);

    // Clear preview image and value
    metabox.on('click', '.psmfields__uploader-remove', reset);

  });

  function uploadFile(event) {
    event.preventDefault();

    // Uploading files
    var file_frame;

    var self = $(this);
    var input = self.parent(classWrapper).find(classInput);
    var preview = self.parent(classWrapper).find(classPreview);
    var attachment;

    // If the media frame already exists, reopen it.
    if (file_frame) {
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: psmfieldsUploaderField.uploaderTitle,
      button: {
        text: psmfieldsUploaderField.buttonText,
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on('select', function () {

      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();

      // Get attachement id and set preview
      input.val(attachment.id);
      input.trigger('change');
      preview.html('<img src="' + attachment.url + '" height="50" />');

    });

    // Finally, open the modal
    file_frame.open();
  }

  function reset(event) {
    event.preventDefault();
    var self = $(event.target);
    var input = self.parent(classWrapper).find(classInput);

    input.val('');
    input.trigger('change');
    self.parent(classWrapper).find(classPreview).html('');
  }

})(jQuery);
