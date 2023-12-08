window.addEventListener("load", (event) => {
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
        if(input.min){
          input.value = input.min;
        }

      })
    }
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






