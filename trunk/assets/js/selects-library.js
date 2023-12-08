window.addEventListener("load", (event) => {
  init_select_library();
});

window.addEventListener('click', function (e) {
  let selects = document.querySelectorAll('.onix-beautiful-select');
  selects.forEach(select => close_dropdown(select, e));
})

function init_select_library() {
  // find all select from our plugin (must have class .onix-beautiful-select select)
  let selects = document.querySelectorAll('.onix-beautiful-select:not(.oh-already-render)');

  selects.forEach(select => omb_render_select_skin(select));
}

function close_dropdown(select, e) {
  if (!select.contains(e.target)) {
    select.classList.remove('show_options');
  }
}

/**
 * render skin to all selects on the page? which parent hase class onix-beautiful-select
 * @param select
 */
function omb_render_select_skin(common_container) {
  common_container.classList.add('oh-already-render');
  // each select must be in the container, with class onix-beautiful-select
  let select = common_container.querySelector('select');
  let multiselect = select.getAttribute("multiple");

  // hide default select on the page
  select.classList.add('oh-hidden-select');

  //check if it multiselect to render correct title
  let title_text = "Select";
  if (!multiselect) {
    title_text = select.value;
  }

  //get all options from default select
  let options = select.options;

  common_container.appendChild(prepare_noselect(title_text));
  common_container.appendChild(prepare_options_container(options));

  common_container.addEventListener("click", omb_manage_menu);
}

/**
 * render <option> element in the div - beautiful onix skin
 *
 * @param option original option to get value and selected status
 * @param i index of original option in original select
 * @returns {*} html element - <option>
 */
function omb_manage_skin_option(option, i) {
  let skin_option = omb_create_element("option", " ", option.innerHTML);
  skin_option.setAttribute('option-number', i);
  skin_option.setAttribute('value', option.value);
  if (option.selected) {
    skin_option.classList.add('selected')
  }
  skin_option.addEventListener("click", omb_options_manager);
  return skin_option;
}

/**
 * prepare select title ad div with some spans
 *
 * @param title_text
 * @returns {*}
 */
function prepare_noselect(title_text) {
  let noselect = omb_create_element("div", "title noselect skin_select_header");
  noselect.appendChild(omb_create_element("span", "text skin_select_header", title_text));
  noselect.appendChild(omb_create_element("span", "icon corner-icon skin_select_header", ' '));
  return noselect;
}

/**
 * prepare container with copies of all select options
 *
 * @param options
 * @returns {*}
 */
function prepare_options_container(options) {
  let container = omb_create_element('div', 'container');
  for (let i = 0; i < options.length; i++) {
    let option = options[i];
    let skin_option = omb_manage_skin_option(option, i);
    skin_option.setAttribute('option-number', i);
    container.appendChild(skin_option);
    skin_option.addEventListener("click", omb_options_manager);
  }
  return container;
}

/**
 * custom function to create new element with class and inner text
 *
 * @param type
 * @param classes
 * @param inner_text
 * @returns {*}
 */
function omb_create_element(type, classes, inner_text = "") {
  let elem = document.createElement(type);
  elem.innerText = inner_text;
  elem.className = classes;
  return elem;
}

function omb_manage_menu(e) {
  e.preventDefault();
  let select = this.closest('.onix-beautiful-select');
  let need_open = !select.classList.contains('show_options');

  if (need_open) {
    select.classList.add('show_options');
    select.classList.add('something-else');
  } else {
    let multi_select = select.querySelector('select').getAttribute("multiple");
    let skin_select_header = e.target.classList.contains('skin_select_header');
    let need_close = multi_select && (!skin_select_header);

    if (!need_close) { // if it is multiselect we need close only on click outside
      select.classList.remove('show_options');
    }
  }
}

function omb_options_manager() {

  let container = this.closest('.onix-beautiful-select');
  let select = container.querySelector('select');

  // find actual select and mark needed option like selected
  omb_set_selected_true_option(container, this.getAttribute('option-number'));

  // mark clicked element in skin select as selected
  omb_mark_skin_element_as_selected(this, select, container);

  //trigger  event
  select.dispatchEvent(new Event('change'));
}

/**
 * mark as selected option in original <select>
 *
 * @param true_select original select
 * @param option_number selected option index
 */
function omb_set_selected_true_option(true_select, option_number) {
  // find select
  let tru_select = true_select.querySelectorAll('select option');
  // find option that must be selected now
  let option_to_select = tru_select[option_number];
  // if this option is already selected we should make it not select
  option_to_select.selected = !option_to_select.selected;
}

/**
 * mark <option> of skin div as selected
 *
 * @param element
 * @param select
 * @param container
 */
function omb_mark_skin_element_as_selected(element, select, container) {
  if (select.getAttribute("multiple")) { // check if it is multiselect
    change_status_skin_option(element);
  } else {
    // we can have just one active option here
    let previous_selected = container.querySelector('option.selected');
    previous_selected.classList.remove('selected');
    element.classList.add('selected');

    //change select title text
    container.querySelector('.title .text').innerText = element.innerText;
  }
}

/**
 * just make status of option in skin opposite to current
 *
 * @param element option in skin
 */
function change_status_skin_option(element) {
  let class_list = element.classList;
  if (class_list.contains('selected')) {
    class_list.remove('selected');
  } else {
    class_list.add('selected');
  }
}


