<?php
?>
<ul class="lights">
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
  <li></li>
</ul>
<style>
  ul.lights{
    width: 100%;
    margin: 20px 0 0 ;
    padding: 0;
    border-top: 2px solid red;
    display: flex;
    justify-content: space-between;
  }

  ul.lights > li{
    list-style: none;
    height: 21px;
    width: 24px;
    border-radius: 50%;
    z-index: 1;
    background:rgba(255, 255, 255, .1);
  }

  ul.lights > li:before{
    content: '';
    position: absolute;
    width: 10px;
    height: 8px;
    background: #444;
    top: -7px;
    left: 7px;
  }

  ul.lights li:after{
    content: '';
    position: absolute;
    width: 74px;
    height: 26px;
    border-bottom: 2px solid #444;
    border-radius: 50%;
    top: -24px;
    left: 12px;
  }

  ul.lights li:last-child:after{
    border-bottom: none;
  }

  ul.lights li:nth-child(4n+1){
    background: rgba(255, 255, 0, 1);
    animation: animate1 1.5s linear infinite;
  }

  @keyframes animate1{
    0%{
      background: rgba(255, 255, 0, .2);
    }

    50%{
      background: rgba(255, 255, 0, 1);
      box-shadow: 0 0 25px rgba(255, 255, 0, 1),
      0 0 45px rgba(255, 255, 0, 1);
    }

    100%{
      background: rgba(255, 0, 255, .2);
    }
  }

  ul.lights li:nth-child(4n+2){
    background: rgba(255, 255, 0, 1);
    animation: animate2 1.5s linear infinite;
    animation-delay: .5s;
  }

  @keyframes animate2{
    0%{
      background: rgba(6, 111, 231, .2);
    }

    50%{
      background: rgba(6, 111, 231, 1);
      box-shadow: 0 0 25px rgba(6, 111, 231, 1),
      0 0 45px rgba(6, 111, 231, 1);
    }

    100%{
      background: rgba(255, 0, 255, .2);
    }
  }


  ul.lights li:nth-child(4n+3){
    background: rgba(255, 0, 0, 1);
    animation: animate3 1.5s linear infinite;
    animation-delay: .7s;
  }

  @keyframes animate3{
    0%{
      background: rgba(255, 0, 0, .2);
    }

    50%{
      background: rgba(255, 0, 0, 1);
      box-shadow: 0 0 25px rgba(255, 0, 0, 1),
      0 0 45px rgba(255, 0, 0, 1);
    }

    100%{
      background: rgba(255, 0, 255, .2);
    }
  }

  ul.lights li:nth-child(4n+4){
    background: rgba(63, 238, 10, 1);
    animation: animate4 1.5s linear infinite;
    animation-delay: .8s;
  }

  @keyframes animate4{
    0%{
      background: rgba(63, 238, 10, .2);
    }

    50%{
      background: rgba(63, 238, 10, 1);
      box-shadow: 0 0 25px rgba(63, 238, 10, 1),
      0 0 45px rgba(63, 238, 10, 1);
    }

    100%{
      background: rgba(255, 0, 255, .2);
    }
  }

</style>
