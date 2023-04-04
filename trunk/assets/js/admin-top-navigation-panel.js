document.addEventListener("DOMContentLoaded", ready);

function ready() {
  let switcher = document.querySelector('#oh-description-switcher');
  if (switcher) {
    let checkbox = switcher.querySelector("input[type=checkbox]");

    checkbox.addEventListener('change', function () {

      let descriptions_list = document.querySelectorAll('.onix-helper-description');

      if (this.checked) {
        descriptions_list.forEach((button) => {
          button.classList.remove('hide-description')
        });
      } else {
        descriptions_list.forEach((button) => {
          button.classList.add('hide-description')
        });
      }

    });
  }
}
