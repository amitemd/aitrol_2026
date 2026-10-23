<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REDIRECCION</title>
</head>
<style>
    *, *:before, *:after {
  box-sizing: border-box;
  position: relative;
}

* {
  -webkit-transform-style: preserve-3d;
          transform-style: preserve-3d;
}

:root {
  --duration: 3.2s;
  --stagger: .65s;
  --easing: cubic-bezier(.36,.07,.25,1);
  --offscreen: 130vmax;
  --color-bg: #EF735A;
  --color-blue: #384969;
  --color-shadow: #211842;
}

html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  overflow: hidden;
}

body {
  display: -webkit-box;
  display: flex;
  -webkit-box-pack: center;
          justify-content: center;
  -webkit-box-align: center;
          align-items: center;
  background: var(--color-bg);
}

#app {
  height: 70vmin;
  width: 40vmin;
  display: -webkit-box;
  display: flex;
  -webkit-box-pack: center;
          justify-content: center;
  -webkit-box-align: center;
          align-items: center;
  -webkit-transform: translateX(25vw) rotateX(-20deg) rotateY(-55deg);
          transform: translateX(25vw) rotateX(-20deg) rotateY(-55deg);
  background: var(--color-blue);
  border-radius: 2vmin;
  -webkit-perspective: 10000px;
          perspective: 10000px;
}
#app:before {
  border: 10vmin solid white;
  border-left-width: 2vmin;
  border-right-width: 2vmin;
  border-radius: inherit;
  content: '';
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  left: 0;
  border: 10vmin solid white;
  border-left-width: 2vmin;
  border-right-width: 2vmin;
  background: var(--color-blue);
}
#app > .papers, #app:before {
  -webkit-transform: translateZ(3vmin);
          transform: translateZ(3vmin);
}
#app:after {
  content: '';
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  left: 0;
  background: inherit;
  border-radius: inherit;
  -webkit-transform: translateZ(1.5vmin);
          transform: translateZ(1.5vmin);
}
#app > .shadow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  -webkit-transform-origin: bottom center;
          transform-origin: bottom center;
  -webkit-transform: rotateX(90deg);
          transform: rotateX(90deg);
  background: var(--color-shadow);
  border-radius: inherit;
}

.paper-shadow {
  background: var(--color-shadow);
  height: 50%;
  width: 100%;
  position: absolute;
  top: calc(100% + 3vmin);
  left: 0;
  -webkit-transform-origin: top center;
          transform-origin: top center;
  -webkit-animation: shadow-in var(--duration) var(--easing) infinite;
          animation: shadow-in var(--duration) var(--easing) infinite;
  -webkit-animation-delay: calc(var(--i) * var(--stagger));
          animation-delay: calc(var(--i) * var(--stagger));
  -webkit-animation-fill-mode: both;
          animation-fill-mode: both;
}
@-webkit-keyframes shadow-in {
  0%,5% {
    -webkit-transform: scale(0.8, 1) translateY(var(--offscreen));
            transform: scale(0.8, 1) translateY(var(--offscreen));
  }
  100% {
    -webkit-transform: scale(0.8, 0);
            transform: scale(0.8, 0);
  }
}
@keyframes shadow-in {
  0%,5% {
    -webkit-transform: scale(0.8, 1) translateY(var(--offscreen));
            transform: scale(0.8, 1) translateY(var(--offscreen));
  }
  100% {
    -webkit-transform: scale(0.8, 0);
            transform: scale(0.8, 0);
  }
}
.papers {
  width: 30vmin;
  height: 40vmin;
  background: white;
}

.paper {
  --segments: 5;
  --segment: calc(100% * 1 / var(--segments));
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  -webkit-animation: fly-in var(--duration) var(--easing) infinite;
          animation: fly-in var(--duration) var(--easing) infinite;
  -webkit-animation-delay: calc( (var(--i) * var(--stagger)) );
          animation-delay: calc( (var(--i) * var(--stagger)) );
}
@-webkit-keyframes fly-in {
  0%, 2% {
    -webkit-transform: translateZ(var(--offscreen)) translateY(80%) rotateX(30deg);
            transform: translateZ(var(--offscreen)) translateY(80%) rotateX(30deg);
  }
  80%, 100% {
    -webkit-transform: translateZ(0px) translateY(0%) rotateX(0deg);
            transform: translateZ(0px) translateY(0%) rotateX(0deg);
  }
}
@keyframes fly-in {
  0%, 2% {
    -webkit-transform: translateZ(var(--offscreen)) translateY(80%) rotateX(30deg);
            transform: translateZ(var(--offscreen)) translateY(80%) rotateX(30deg);
  }
  80%, 100% {
    -webkit-transform: translateZ(0px) translateY(0%) rotateX(0deg);
            transform: translateZ(0px) translateY(0%) rotateX(0deg);
  }
}
.paper > .segment {
  height: var(--segment);
}

.segment {
  --rotate: 20deg;
  height: 100%;
  -webkit-transform-origin: top center;
          transform-origin: top center;
  background: white;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-top: none;
  border-bottom: none;
  -webkit-animation: inherit;
          animation: inherit;
  -webkit-animation-name: curve-paper;
          animation-name: curve-paper;
}
.segment > .segment {
  top: 98%;
}
@-webkit-keyframes curve-paper {
  0%, 2% {
    -webkit-transform: rotateX(var(--rotate, 0deg));
            transform: rotateX(var(--rotate, 0deg));
  }
  90%, 100% {
    -webkit-transform: rotateX(0deg);
            transform: rotateX(0deg);
  }
}
@keyframes curve-paper {
  0%, 2% {
    -webkit-transform: rotateX(var(--rotate, 0deg));
            transform: rotateX(var(--rotate, 0deg));
  }
  90%, 100% {
    -webkit-transform: rotateX(0deg);
            transform: rotateX(0deg);
  }
}
/* ---------------------------------- */
.paper.-rogue {
  -webkit-transform-origin: top center -5vmin;
          transform-origin: top center -5vmin;
}
.paper.-rogue .segment {
  --rotate: 30deg;
  -webkit-animation-name: curve-rogue-paper;
          animation-name: curve-rogue-paper;
}
@-webkit-keyframes curve-rogue-paper {
  0%, 50% {
    -webkit-transform: rotateX(var(--rotate));
            transform: rotateX(var(--rotate));
  }
  100% {
    -webkit-transform: rotateX(0deg);
            transform: rotateX(0deg);
  }
}
@keyframes curve-rogue-paper {
  0%, 50% {
    -webkit-transform: rotateX(var(--rotate));
            transform: rotateX(var(--rotate));
  }
  100% {
    -webkit-transform: rotateX(0deg);
            transform: rotateX(0deg);
  }
}
.paper.-rogue > .segment {
  -webkit-animation: inherit;
          animation: inherit;
  -webkit-animation-name: rogue-paper;
          animation-name: rogue-paper;
  -webkit-transform-origin: left top 20vmin;
          transform-origin: left top 20vmin;
}
@-webkit-keyframes rogue-paper {
  0%, 2% {
    -webkit-transform: rotateX(1.5turn);
            transform: rotateX(1.5turn);
  }
  80%, 100% {
    -webkit-transform: rotateX(0turn);
            transform: rotateX(0turn);
  }
}
@keyframes rogue-paper {
  0%, 2% {
    -webkit-transform: rotateX(1.5turn);
            transform: rotateX(1.5turn);
  }
  80%, 100% {
    -webkit-transform: rotateX(0turn);
            transform: rotateX(0turn);
  }
}   
</style>
<body>
<script src="https://codepen.io/shshaw/pen/QmZYMG.js"></script>

<div id="app">
  <div class="papers" style="--total: 5">
    
    <div class="paper -rogue" style="--i: 0">
      <div class="segment">
        <div class="segment">
          <div class="segment">
            <div class="segment">
              <div class="segment"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="paper" style="--i: 1">
      <div class="segment">
        <div class="segment">
          <div class="segment">
            <div class="segment">
              <div class="segment"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="paper" style="--i: 2">
      <div class="segment">
        <div class="segment">
          <div class="segment">
            <div class="segment">
              <div class="segment"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
        
    <div class="paper" style="--i: 3">
      <div class="segment">
        <div class="segment">
          <div class="segment">
            <div class="segment">
              <div class="segment"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="paper" style="--i: 4">
      <div class="segment">
        <div class="segment">
          <div class="segment">
            <div class="segment">
              <div class="segment"></div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
  <div class="shadow">
    <div class="paper-shadow" style="--i: 0"></div>
    <div class="paper-shadow" style="--i: 1"></div>
    <div class="paper-shadow" style="--i: 2"></div>
    <div class="paper-shadow" style="--i: 3"></div>
    <div class="paper-shadow" style="--i: 4"></div>
  </div>
</div>
</body>
<!-- <script>
    var url = "https://play.google.com/store/apps/details?id=com.labsapp&hl=es";
    var urlios = "https://apps.apple.com/us/app/labs/id1587751232";

    function reconify() {

        var userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Windows Phone debe ir primero porque su UA tambien contiene "Android"
        if (/windows phone/i.test(userAgent)) {
            return "Windows Phone";
        }

        if (/android/i.test(userAgent)) {
            return "Android";
        }

        if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            return "iOS";

        }
        return "desconocido";
    }
    function android(url){
        
    }
</script> -->
<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function (){
        if(navigator.userAgent.toLowerCase().indexOf("android") > -1){
            window.location.href = 'https://play.google.com/store/apps/details?id=com.labsapp&hl=es';
        }
        if(navigator.userAgent.toLowerCase().indexOf("iphone") > -1){
            window.location.href = 'https://apps.apple.com/us/app/labs/id1587751232';
        }
    });
</script>
</html>