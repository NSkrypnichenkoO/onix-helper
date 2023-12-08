//fields_section_screen

document.addEventListener("DOMContentLoaded", ready);

function ready() {

  let section = document.querySelector('#fields_section_screen');
  let select = document.querySelector('.row .onix-beautiful-select select')

  if (!section || !select) {
    return;
  }

  select.onchange = handleSelectChange;
}

function handleSelectChange(event) {
// ned to know what user choose in main multiselect to manage additional options

// get list of all selected items
  let values = getSelectValues(this);

  //work with page selector block
  let page_list_block = document.querySelector('.oh-pages-selector');

  if (values.includes('page')) {
    //need find parent block of additional settings and display it if need
    page_list_block.classList.remove('oh-hide-this-section');
    // need to enable select
    page_list_block.querySelector('select').disabled = false

  } else {
    page_list_block.classList.add('oh-hide-this-section');
    page_list_block.querySelector('select').disabled = true
    // need to disable select
  }
}


// Return an array of the selected option values
// select is an HTML select element
function getSelectValues(select) {
  var result = [];
  var options = select && select.options;
  var opt;

  for (var i = 0, iLen = options.length; i < iLen; i++) {
    opt = options[i];

    if (opt.selected) {
      result.push(opt.value || opt.text);
    }
  }
  return result;
}
