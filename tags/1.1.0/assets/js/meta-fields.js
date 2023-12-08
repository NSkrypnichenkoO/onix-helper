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


    let item = list.find('.item-' + slug).last().clone();
    item.find('.remove-fields-block').on("click", function (e) {
      remove_block($(this));
    })

    //need to create names on fly
    let inputs = item.find('input');
    let index = parseInt(split_to_find_index(inputs.first().attr('name'))) + 1;
    inputs.val(''); // clear the value

    inputs.each(function () {
      let name = $(this).attr('name');
      let s1 = name.split('][');
      let s2 = s1[0].split('[');
      let new_name = s2[0] + "[" + index + "][" + s1[1];
      console.log(new_name);
      $(this).attr('name', new_name);
    })

    let img = item.find('img');
    let src = img.attr('data-src');
    img.attr('src', src);
    list.append(item);

    hideAddButtonIfTooMuchElements();
  })

  function split_to_find_index(name) {
    let s1 = name.split('][');
    console.log('s1')
    console.log(s1)
    let s2 = s1[0].split('[');
    return s2[1];
  }

  $('.remove-fields-block').on("click", function (e) {
    remove_block($(this));
  })

  function remove_block(e) {
    // lets take parent
    let section = e.closest('.oh-list-of-section-fields');

    let fields_count = section.find('.oh-field-item').length;
    let current_fields_block = e.closest('.oh-field-item');
    // console.log('slug');
    // console.log(current_fields_block);

    if(fields_count > 1){
      current_fields_block.remove();
    } else{
      //clean inputs
      let inputs = current_fields_block.find('input');
      inputs.val('');
      //clean src
      let images = current_fields_block.find('img');
      images.attr('src', images.attr('data-src')); // set default image icon
    }
  }


  $(".first-screen").on('mousemove', function (event) {
    var eye = $(".boot-eyes");
    var x = (eye.offset().left) + (eye.width() / 2);
    var y = (eye.offset().top) + (eye.height() / 2);
    var rad = Math.atan2(event.pageX - x, event.pageY - y);
    var rot = (rad * (180 / Math.PI) * -1) + 180;
    // rot = Math.max(225, Math.min(rot, 315));
    rot = Math.max(135, Math.min(rot, 225));
    eye.css({
      '-webkit-transform': 'rotate(' + rot + 'deg)',
      '-moz-transform': 'rotate(' + rot + 'deg)',
      '-ms-transform': 'rotate(' + rot + 'deg)',
      'transform': 'rotate(' + rot + 'deg)'
    });
  });

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
