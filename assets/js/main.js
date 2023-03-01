window.addEventListener("load", (event) => {
  let tabs = document.querySelectorAll('ul.nav-tabs > li');

  for (let i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener('click', omb_switch_tab)
  }

  const form = document.querySelector("#tab-2.tab-pane > form");

  if (form) {
    const default_inputs = form.querySelectorAll("input.cpt-default-setter");

    default_inputs.forEach(function (elem) {
      elem.addEventListener('change', function () {
        //get parent container
        let parent = this.parentElement.parentElement;

        if (parent) {
          //get settings inputs in parent container
          let inputs = parent.querySelectorAll("label.cpt-checkbox-container > input");

          inputs.forEach(function (input) {
            let disabled = input.disabled;
            let checked = input.checked;

            if (checked){
              input.checked = !checked;
            }
            input.disabled = !disabled;
          });
        }
      });
    });
  }
});

function omb_change_setting_status() {
  // alert('dfjdhbhbfhdfdfh');
}

function omb_switch_tab(e) {
  e.preventDefault();

  let currentTab = e.currentTarget;
  let link = e.target;
  let activePainId = link.getAttribute('href');
  console.log(document.querySelector(activePainId));

  //remove previous active tab before makes active the new one
  document.querySelector('ul.nav-tabs > li.active').classList.remove('active');
  document.querySelector('.tab-pane.active').classList.remove('active');

  //add active class to current tab and its section
  currentTab.classList.add('active');
  document.querySelector(activePainId).classList.add('active');
}


//код для того, что бы получить все матабоксы из админки, пока полежит тут.
// jQuery(function($) {
//   for (var i = 0; i < localizedData.postTypes.length; ++i) {
//     $.ajax({
//       type: 'post',
//       url: 'post-new.php?post_type='+localizedData.postTypes[i],
//       data: {
//         action: 'my-plugin-action',
//         _ajax_nonce: localizedData.nonce
//       },
//       success: function(data) {
//         if (data) {
//           // doSomethingWithTheBoxes(data.metaBoxes);
//           console.log(data)
//         }
//       }
//     });
//   }
// });
