jQuery(document).ready(function ($) {
  let hideAddButtonIfTooMuchElements = function () {

    let sections = jQuery('.omb-section-fields');
    sections.each(function (index) {
      let max_count = parseInt($(this).attr('max-section-count'));
      let slug = $(this).attr('id');
      let rowsCount = $('.item-' + slug).length;
      if (rowsCount >= max_count && max_count !== -1) {
        $('.add-new-' + slug).hide();
      }

      //image uploader
      $(this).on('click', '.upload_image_button', function () {
        let send_attachment_bkp = wp.media.editor.send.attachment;
        let button = $(this);
        wp.media.editor.send.attachment = function (props, attachment) {
          $(button).parent().prev().attr('src', attachment.url);
          $(button).prev().val(attachment.id);
          wp.media.editor.send.attachment = send_attachment_bkp;
        }
        wp.media.editor.open(button);

        return false;
      });
    })
  }

  hideAddButtonIfTooMuchElements();

  $('.add-field-block').on("click", function (e) {
    // lets find parent and take its slug
    let slug = $(this).closest('.omb-section-fields').attr('id');
    let list = $('.' + slug + '-list');
    let item = list.find('.item-' + slug).first().clone();
    item.find('input').val(''); // clear the value

    let img = item.children().first().children().first();
    let src = img.attr('data-src');
    img.attr('src', src);
    list.append(item);

    hideAddButtonIfTooMuchElements();
  })

  $('.remove-fields-block').on("click", function (e) {
    // lets find parent and take its slug
    let slug = $(this).closest('.omb-section-fields').attr('id');
    if ($('.item-' + slug).length > 1) {
      $(this).closest('.item-' + slug).remove();
    }
    else {
     $(this).closest('.item-' + slug ).find('input').val('');

     // In case if there is image
     let img = $(this).prev().children().first();
     let src = img.attr('data-src');
     img.attr('src', src);
     let input = img.next().children().first();
     input.val('');
     return false;
   }

  })
//  // Удаляет секцию
//  $companyInfo.on('click', '.remove-new-<?//= $this->meta_key ?>//', function () {
//    if ($('.item-<?//= $this->meta_key ?>//').length > 1) {
//      $(this).closest('.item-<?//= $this->meta_key ?>//').remove();
//    } else {
//      $(this).closest('.item-<?//= $this->meta_key ?>//').find('input').val('');
//
//      // In case if there is image
//      let img = $(this).prev().children().first();
//      let src = img.attr('data-src');
//      img.attr('src', src);
//      let input = img.next().children().first();
//      input.val('');
//      return false;
//    }
//    $('.add-new-<?//= $this->meta_key ?>//').show();
//  });
//

});
