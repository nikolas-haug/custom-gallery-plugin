  jQuery(document).ready(function($) {
    
    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;
    // Runs when the image button is clicked.
    $('.image-upload').click(function(e) {
      // Get preview pane
      var meta_image_preview = $(this)
        .parent()
        .parent()
        .children('.image-preview')
      // Prevents the default action from occuring.
      e.preventDefault()
      var meta_image = $(this)
        .parent()
        .children('.meta-image')
      // If the frame already exists, re-open it.
      if (meta_image_frame) {
        meta_image_frame.open()
        return
      }
      // Sets up the media library frame
      meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
        title: meta_image.title,
        button: {
          text: meta_image.button,
        },
        multiple: 'add'
      });

      // Runs when an image is selected.
      meta_image_frame.on('select', function() {
        // Grabs the attachment selection and creates a JSON representation of the model.
        var media_attachment = meta_image_frame
          .state()
          .get('selection')
          .toJSON()
        
        //clear screenshot div so we can append new selected images
        meta_image_preview.empty();

        // create empty array for images
        const chosenImgs = [];

        // Sends the attachment URL to our custom image input field.
        const ids = media_attachment.map(image => {
            chosenImgs.push(image.url);
        });

        // console.log(media_attachment);
        media_attachment.forEach(image => {

            preview_html = "<div><img style='width: 100px;' src='" + image.url + "'/></div>";

            meta_image_preview.append(preview_html);

            // console.log(image);
        });
        meta_image.val(chosenImgs.join(','));
        // meta_image_preview.children('img').attr('src', media_attachment.url)
      })
      // Opens the media library frame.
      meta_image_frame.open()
    });

    // Clear selected images
    $('#clear-gallery').on('click', function() {
        // Get preview pane
        var image_div = $(this)
        .parent()
        .parent()
        .children('.image-preview');

        var meta_image = $(this)
        .parent()
        .children('.meta-image')

        image_div.empty();
        meta_image.val('');

    });

  });