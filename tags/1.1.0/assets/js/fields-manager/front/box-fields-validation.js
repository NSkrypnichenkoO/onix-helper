// window.addEventListener("load", (event) => {
// setTimeout(function (){
//   let update = document.querySelector('.editor-post-publish-button');
 

// }, 3000)
//   let update = document.querySelector('.editor-post-publish-button');



//   if (update) {
//     update.addEventListener('click', function (e) {

//       // urls validation
//       let urls = document.querySelectorAll('.omb-section-fields');

//       // urls.forEach(function (elem) {
//       //   console.log(elem.value);
//       // })

//       e.stopPropagation();
//     })
//   }
// });

let WordButton = document.getElementsByClassName("editor-post-publish-button");

function checkButton() {
  if (WordButton.length > 0) {
    
    let WordButton = document.querySelector(".editor-post-publish-button");
    WordButton.addEventListener("click", function() {

        const block = document.querySelector('.oh-fields-block');
        const inputs = document.querySelectorAll('.oh-fields-block input');
        const emailInputs = document.querySelectorAll('.oh-fields-block input[type="email"]');
        const urlInputs = document.querySelectorAll('.oh-fields-block input[type="url"]');

        let emailMessage = "";
        let urlMessage = "";

        emailInputs.forEach(input => {
            const value = input.value;
            const emailPattern = /^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;

            if (!emailPattern.test(value)) {
                input.setCustomValidity('Введите действительный email-адрес');
                emailMessage = "email ерор";
            } else {
                input.setCustomValidity('');
                emailMessage = "email гуд";
            }
        });

        urlInputs.forEach(input => {
            const value = input.value;
            const urlPattern = /^(http|https):\/\/[^ "]+$/;
    
            if (!urlPattern.test(value)) {
                input.setCustomValidity('Введите действительный URL');
                urlMessage = "url ерор";
            } else {
                input.setCustomValidity('');
                urlMessage = "url гуд";
            }
        });
        
    });
    return;
  }
  setTimeout(checkButton, 250);
}

checkButton();

