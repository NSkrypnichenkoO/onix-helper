jQuery(document).ready(function ($) {

  // button on the create new section page, that open popup with new field form
  $('#omb_add_new_field_to_section').on("click", function (e) {
    e.preventDefault();

    let container = $(this).closest('.onix-helper-field-block').find('.oh-field-additional-content');

    $.ajax({
      type: "POST",
      url: omb_ajax_object.ajax_url,
      data: {
        action: 'get_new_field_form_template',
        dataType: 'json',
      },
      beforeSend: function (xhr) {
      },
      success: function (response) {
        // console.log(response);
        container.append(response);
      },
      error: function (error) {
        console.log(error);
      },
      complete: function (rsp) {
        init_select_library();
      }
    });
  });
})

/**
 * Close popup that allow added new fields to the section
 *
 * @param element
 */
function close_modal_popup(element) {
  element.closest('.onix-beautiful-popup').remove();
}

/**
 button add field allow create new hidden input and visual table row with according information.
 data will be processed after form submit.
 */
function add_field_to_form(element) {
  let form = element.closest('#oh-add-new-field-form');
  let inputs = form.querySelectorAll("input, select");
  let fields_data = [];
  let empty_data = false;
  console.log("Log");
  console.log(inputs);


  inputs.forEach((element) => {
    let value = element.value;
    if (!value) {
      empty_data = true;
      let single_field_param = element.closest('.oh-single-field-param');
      if (!single_field_param.classList.contains('oh-find-error')) {
        single_field_param.append(create_error_message());
        single_field_param.classList.add('oh-find-error')
      } else {
        single_field_param.classList.remove('oh-find-error');
        setTimeout(() => {
          single_field_param.classList.add('oh-find-error')
        }, "500");
      }
    } else {
      fields_data[element.name] = element.value;
    }
  })

  if (!empty_data) {
    // find table to insert row
    let table_body = document.querySelector('#oh-fields-list tbody');
    let row = create_new_field_table_row(fields_data, table_body);
    console.log(row)
    if (row) {
      // let parser = new DOMParser();
      // row = parser.parseFromString(row, 'text/html');
      var dom = document.createElement('tr');
      dom.innerHTML = row;
      table_body.append(dom);
      // table_body.innerHTML = row;
    } else {
      console.log('something went wrong, try again later')
    }

    close_modal_popup(element);
  }
}


/**
 in some reason we cant create this element one time and add to the different places. But we can every time create the
 same error message element
 */
function create_error_message() {
  let error = document.createElement("span");
  error.innerText = "Enter something";
  error.classList.add("oh-error-message");
  return error;
}

/**
 *
 */
function create_new_field_table_row(fields_data, table) {
//first part of each input name
  let common_name = table.getAttribute('data-input-name');
  let columns = ['slug', 'type', 'title'];

  remove_empty_row_if_exist(table);

  // //now, when we remove empty row, we should find all rows, and check with which index we will crete new one
  let new_index = table.querySelectorAll('tr').length;
  let params = {};

  for (let i = 0; i < columns.length; i++) {
    params[columns[i]] = fields_data[columns[i]];
  }

  let postObj = {}

  postObj["fields"] = params;
  postObj["index"] = new_index;
  postObj["input_name"] = common_name;

  let post = JSON.stringify(postObj);
  const siteUrl = omb_ajax_object.ajax_url+'?action=get_full_row_template&dataType=json';
  // const url = "http://metafields-guru/wp-admin/admin-ajax.php?action=get_full_row_template&dataType=json" // admin-ajax.php?action=get_new_field_form_template&dataType=json


  // let xhr = new XMLHttpRequest()
  let xhr = create_request();
  xhr.open('POST', siteUrl, false)
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8')
  xhr.send(post);

  let answer = JSON.parse(xhr.responseText);
  if (xhr.status === 200 && answer['status'] === '200') {
    return answer['template'];
  } else {
    return false;
  }
}

/**
 * when we have no data - we render empty th element just with message. When we create new field - we should remove
 * empty row and create new one
 * @param table
 */
function remove_empty_row_if_exist(table) {
  let empty_row = table.querySelector('tr.first-empty-one')
  if (empty_row) {
    empty_row.remove();
  }
}

/**
 * press on button just removed from html tr.
 * @param element
 */
function remove_single_row(element) {
  let el = element.target ? element.target : element;
  let row = el.closest('tr');
  row.remove();
}


/**
 * создание данного объекта для каждого типа браузера — уникальный процесс.
 */
function create_request() {
  var Request = false;

  if (window.XMLHttpRequest) {
    //Gecko-совместимые браузеры, Safari, Konqueror
    Request = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    //Internet explorer
    try {
      Request = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (CatchException) {
      Request = new ActiveXObject("Msxml2.XMLHTTP");
    }
  }

  if (!Request) {
    console.log("Cant XMLHttpRequest");
  }

  return Request;
}

function oh_onkeydown_validation(e) {
  if (!/[a-z0-9\-_]/.test(e.key)) {
    e.preventDefault();
  }
}



