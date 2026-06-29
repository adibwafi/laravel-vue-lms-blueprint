<template>
  <div class="splashscreen" :class="{ fadeout: !isSplash }">
    <img src="@/assets/img/blue.png" alt="" class="bg-blue" />
    <img
      src="@/assets/img/yellow.png"
      alt=""
      class="bg-yellow"
      :class="{ zoomIn: isYellow }"
    />
    <img src="@/assets/img/startuplogo.png" alt="" class="logo" />
  </div>
</template>

<script>
export default {
  name: "LoadingScreen",
  props: ["isSplash"],
  data() {
    return { isYellow: false };
  },
  mounted() {
    setTimeout(() => {
      this.isYellow = true;
    }, 2000);
  },
};
</script>

<style lang="scss" scoped>
.splashscreen {
  //   max-width: 414px;
  margin: 0 auto;
  background: #0056d2;
  width: 100%;
  height: 100%;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  padding: 0px;
  top: 0;
  display: flex;
  align-items: center;
  justify-content: center;

  .bg-blue {
    height: 100vh;
    width: 100%;
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
  }

  .bg-yellow {
    position: absolute;
    z-index: 2;
    display: none;
  }

  .bg-yellow.zoomIn {
    -webkit-animation-name: zoomIn;
    animation-name: zoomIn;
    display: block;
    -webkit-animation-duration: 1s;
    animation-duration: 1s;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
  }

  .logo {
    position: absolute;
    z-index: 3;
    transform: translate(-50%, -50%);
    width: 100px;
    -webkit-animation-name: scaleIn;
    -webkit-animation-duration: 1.5s;
    animation-name: scaleIn;
    animation-duration: 1.5s;
    animation-iteration-count: infinite;
    -webkit-animation-iteration-count: infinite;
  }
}

.splashscreen.fadeout {
  animation: fadeout 0.4s forwards;
  display: none !important;
}

@keyframes fadeout {
  to {
    opacity: 0;
    visibility: hidden;
  }
}

@-webkit-keyframes zoomIn {
  from {
    opacity: 0;
    width: 0;
    height: 0;
    object-fit: cover;
    border-radius: 50%;
    -webkit-transform: scale3d(0.3, 0.3, 0.3);
    transform: scale3d(0.3, 0.3, 0.3);
  }

  50% {
    opacity: 1;
    height: 25%;
    width: 50%;
    border-radius: 50%;
  }

  80% {
    border-radius: 50%;
    height: 40%;
    width: 80%;
  }

  90% {
    border-radius: 50%;
  }

  100% {
    height: 100vh;
    width: 100%;
    transform: scale(3);
    border-radius: 0%;
  }
}

@keyframes zoomIn {
  from {
    opacity: 0;
    width: 0;
    height: 0;
    object-fit: cover;
    border-radius: 50%;
    -webkit-transform: scale3d(0.3, 0.3, 0.3);
    transform: scale3d(0.3, 0.3, 0.3);
  }

  50% {
    opacity: 1;
    height: 25%;
    width: 50%;
    border-radius: 50%;
  }

  80% {
    border-radius: 50%;
    height: 40%;
    width: 80%;
  }

  90% {
    border-radius: 50%;
  }

  100% {
    height: 100vh;
    width: 100%;
    transform: scale(3);
    border-radius: 0%;
  }
}

@media (min-width: 768px) {
  @-webkit-keyframes zoomIn {
    from {
      opacity: 0;
      width: 0;
      height: 0;
      object-fit: cover;
      border-radius: 50%;
      -webkit-transform: scale3d(0.3, 0.3, 0.3);
      transform: scale3d(0.3, 0.3, 0.3);
    }

    50% {
      opacity: 1;
      height: 250px;
      width: 250px;
      border-radius: 50%;
    }

    80% {
      border-radius: 50%;
      height: 500px;
      width: 500px;
    }

    90% {
      border-radius: 50%;
    }

    100% {
      transform: scale(3);
      border-radius: 0%;
    }
  }

  @keyframes zoomIn {
    from {
      opacity: 0;
      width: 0;
      height: 0;
      object-fit: cover;
      border-radius: 50%;
      -webkit-transform: scale3d(0.3, 0.3, 0.3);
      transform: scale3d(0.3, 0.3, 0.3);
    }

    50% {
      opacity: 1;
      height: 250px;
      width: 250px;
      border-radius: 50%;
    }

    80% {
      border-radius: 50%;
      height: 500px;
      width: 500px;
    }

    90% {
      border-radius: 50%;
    }

    100% {
      transform: scale(3);
      border-radius: 0%;
    }
  }
}
</style>
