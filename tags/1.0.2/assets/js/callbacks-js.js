window.addEventListener("load", (event) => {
  // select
  let selects = document.querySelectorAll('.onix-beautiful-select');
  selects.forEach(select => omb_render_select_skin(select));

  // switcher
  let switchers = document.querySelectorAll('.switch');
  switchers.forEach(switcher => switcher_behavior(switcher));

  //radio buttons show/(disable) elements
  let radio_with_dependencies = document.querySelectorAll('.oh-radio-with-options [type="radio"]');
  radio_with_dependencies.forEach(button => omb_radio_show_its_block(button))
});

function omb_radio_show_its_block(button) {
  button.addEventListener('click', omb_click_on_radio);
}

function omb_click_on_radio() {
  let marker = this.getAttribute("data-show-if-active");

  //need to find already enabled container, if exist. can be only one ore null
  let parent = this.closest('.oh-field-content');
  let container_to_manage = parent.querySelector('.oh-radio-block-to-show.oh-already-enabled');

  if (container_to_manage) {
    let cont_marker = container_to_manage.getAttribute("data-depends-of");

    // if user make click on the same element we dont need to do anything
    if (marker !== cont_marker) {
      container_to_manage.classList.remove('oh-already-enabled')
      let inputs = container_to_manage.querySelectorAll('input');
      inputs.forEach(function (input) {
        input.disabled = true;
        input.value = '';
      })
    }
  } else {
    if (marker) {
      // ned to find element with marker and enabled all inputs
      let container_to_show = parent.querySelector('.oh-radio-block-to-show[data-depends-of = "' + marker + '"]');
      container_to_show.classList.add('oh-already-enabled');
      let inputs = container_to_show.querySelectorAll('input');
      inputs.forEach(function (input) {
        input.disabled = false;
        input.value = 2;
      })
    }
  }
}

function omb_render_select_skin(select) {
  // hide default select on the page
  // select.querySelector('select').style.display = 'none';
  select.querySelector('select').classList.add('oh-hidden-select');

  let noselect = omb_create_element("div", "title noselect");

  noselect.appendChild(omb_create_element("span", "text", "Select"));
  noselect.appendChild(omb_create_element("span", "icon corner-icon", ' '));

  let container = omb_create_element('div', 'container');

  //get all options from default select
  let options = select.querySelectorAll('select > option');

  let i = 0;
  options.forEach(option => {
    let skin_option = omb_manage_skin_option(option, i);
    // let skin_option = omb_create_element("option", " ", option.value);
    // skin_option.setAttribute('option-number', i);
    container.appendChild(skin_option);
    // skin_option.addEventListener("click", omb_options_manager);
    i++;
  })

  select.appendChild(noselect);
  noselect.addEventListener("click", omb_display_date)
  select.appendChild(container);
}

function omb_manage_skin_option(option, i) {
  let skin_option = omb_create_element("option", " ", option.value);
  skin_option.setAttribute('option-number', i);
  if (option.selected) {
    skin_option.classList.add('selected')
  }
  skin_option.addEventListener("click", omb_options_manager);
  return skin_option;
}


function omb_create_element(type, classes, inner_text = "") {
  let elem = document.createElement(type);
  elem.innerText = inner_text;
  elem.className = classes;
  return elem;
}

function omb_display_date(e) {
  let classes = e.target.classList;
  if (classes.contains('close-icon')) {
    omb_remove_all_options();
  } else {
    omb_manage_menu(this)
  }
}

function omb_manage_menu(element) {
  let select = element.closest('.onix-beautiful-select');
  let need_open = !select.classList.contains('show_options');

  if (need_open) {
    select.classList.add('show_options');
    // console.log('need open');
  } else {
    select.classList.remove('show_options');
    // console.log('need close');
  }
}

function omb_options_manager() {

  let value = this.value;

  let container = this.closest('.onix-beautiful-select');
  let select = container.querySelector('select');

  // find actual select and mark needed option like selected
  omb_set_selected_true_option(container, this.getAttribute('option-number'));

  // mark clicked element in skin select as selected
  omb_mark_skin_element_as_selected(this);

  //trigger  event
  select.dispatchEvent(new Event('change'));
}

function omb_set_selected_true_option(true_select, option_number) {
  // find select
  let tru_select = true_select.querySelectorAll('select option');
  // find option that must be selected now
  let option_to_select = tru_select[option_number];
  // if this option is already selected we should make it not select
  option_to_select.selected = !option_to_select.selected;
}

function omb_mark_skin_element_as_selected(element) {
  let class_list = element.classList;
  if (class_list.contains('selected')) {
    element.classList.remove('selected');
  } else {
    element.classList.add('selected');
  }
}

function omb_remove_all_options() {
  console.log('remove all');
}


//checkbox-switcher
function switcher_behavior(switcher) {
  switcher.addEventListener("click", (e) => {
    e.preventDefault();

    let targetElement = e.target || e.srcElement;
    let checkbox = targetElement.querySelector('input[type=checkbox]');
    checkbox.checked = !checkbox.checked;

    let parent = targetElement.closest('.onix-helper-field-block');

    if (parent) {
      let fields_block = parent.querySelector('.oh-hide-on-default');
      manage_asses_to_options(fields_block);
    }
  });
}

function manage_asses_to_options(element) {
  if (element.style.display === "none") {
    let inputs = element.querySelectorAll('.manage-by-default-switcher input');
    //already used default value, but we should change it
    element.style.display = "block";

    // need to find all inputs inside and make them not disable
    inputs.forEach(function (input) {
      input.disabled = false;
    })

  } else {
    let inputs = element.querySelectorAll('.oh-hide-on-default input');
    // user want to use default wp value
    element.style.display = "none";
    inputs.forEach(function (input) {
      input.disabled = true;
    })
  }
}






