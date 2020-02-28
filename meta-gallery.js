  jQuery(document).ready(function($) {

    // clear image preview div
    function clearDivImages() {
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
    }

    // get default value of selected radio button
    var meta_radio_value = $('.radio-button__group label [name="your_fields[radio]"]:checked').val();

    // Update meta radio button value upon selection
    $('input[name="your_fields[radio]"]').on('change', function() {
      console.log('changed to: ' + $(this).val());
      meta_radio_value = $(this).val();
      console.log('Meta radio var changed to: ' + meta_radio_value);
    }); 
    
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
        console.log('image selected');
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

        // Comnpare selected image template with number of chosen images
        if(chosenImgs.length !== meta_radio_value.split(',').length) {
          alert(`Selected template needs ${meta_radio_value.split(',').length} image${meta_radio_value.split(',').length > 1 ? 's' : ''}. You need to reselect.`);
          clearDivImages();
          meta_image_frame.open();
          return;
        }

        // console.log(media_attachment);
        media_attachment.forEach(image => {

            // Adjust size if not on sidebar
            preview_html = "<div><img style='width: 100%;' src='" + image.url + "'/></div>";

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
    $('#clear-gallery').on('click', clearDivImages);

  });