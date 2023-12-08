window.addEventListener("load", (event) => {
  // tabs top panel
  let tabs = document.querySelectorAll('ul.nav-tabs > li');
  for (let i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener('click', omb_switch_tab)
  }

  //add new entity button
  let add_button = document.querySelector('.add-new-entity-button a');
  if (add_button) {
    add_button.addEventListener('click', function (e) {
      e.preventDefault();
      let activePainId = this.getAttribute('href');

      //remove previous active tab before makes active the new one
      let li_active = document.querySelector('ul.nav-tabs > li.active')
      if (li_active) {
        li_active.classList.remove('active');
      }
      let tab_active = document.querySelector('.tab-pane.active');
      if (tab_active) {
        tab_active.classList.remove('active');
      }
      document.querySelector(activePainId).classList.add('active');

    });
  }

  //
  // const form = document.querySelector("#tab-2.tab-pane > form");
  //
  // if (form) {
  //   const default_inputs = form.querySelectorAll("input.cpt-default-setter");
  //   console.log(default_inputs)
  //   default_inputs.forEach(function (elem) {
  //     elem.addEventListener('change', function () {
  //       //get parent container
  //       let parent = this.parentElement.parentElement;
  //
  //       if (parent) {
  //         //get settings inputs in parent container
  //         let inputs = parent.querySelectorAll("label.cpt-checkbox-container > input");
  //
  //         inputs.forEach(function (input) {
  //           let disabled = input.disabled;
  //           let checked = input.checked;
  //
  //           if (checked) {
  //             input.checked = !checked;
  //           }
  //           input.disabled = !disabled;
  //         });
  //       }
  //     });
  //   });
  // }

  // change position of dots in checkbox if checked
  let switchers = document.querySelectorAll('.switch');
  switchers.forEach(function (elem) {
    elem.addEventListener('click', function () {
      if (this.classList.contains("highlighting")) {
        this.classList.remove("highlighting");
      } else {
        this.classList.add("highlighting");
      }
    })
  });
});


function omb_switch_tab(e) {
  e.preventDefault();

  let currentTab = e.currentTarget;
  let link = e.target;
  let activePainId = link.getAttribute('href');

  //remove previous active tab before makes active the new one
  let li_active = document.querySelector('ul.nav-tabs > li.active')
  if (li_active) {
    li_active.classList.remove('active');
  }
  let tab_active = document.querySelector('.tab-pane.active');
  if (tab_active) {
    tab_active.classList.remove('active');
  }

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
