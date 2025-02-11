jQuery(document).ready(function ($) {
  function loadPosts(numPosts = 5) {
    $.ajax({
      url: cps_ajax.ajaxurl,
      type: "POST",
      data: {
        action: "cps_get_latest_posts",
        num_posts: numPosts,
      },
      success: function (response) {
        $(".cps-slides").html(response);
        initSlider();
      },
    });
  }

  loadPosts();

  function initSlider() {
    let currentIndex = 0;
    const slides = $(".cps-slide");
    const totalSlides = slides.length;
    let timer;

    function updateSlides() {
      slides.removeClass("active prev next");
      slides.each(function (index) {
        if (index === currentIndex) {
          $(this).addClass("active");
        } else if (index === (currentIndex - 1 + totalSlides) % totalSlides) {
          $(this).addClass("prev");
        } else if (index === (currentIndex + 1) % totalSlides) {
          $(this).addClass("next");
        }
      });
      $(".cps-dot").removeClass("active");
      $(".cps-dot").eq(currentIndex).addClass("active");
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % totalSlides;
      updateSlides();
    }

    function prevSlide() {
      currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
      updateSlides();
    }

    function resetTimer() {
      clearInterval(timer);
      timer = setInterval(nextSlide, 5000);
    }

    // Crear dots de navegación si no existen
    if ($("#cps-slider .cps-dots").length === 0) {
      const dotsContainer = $('<div class="cps-dots"></div>');
      for (let i = 0; i < totalSlides; i++) {
        const dot = $(`<button class="cps-dot" data-index="${i}"></button>`);
        dotsContainer.append(dot);
      }
      $("#cps-slider").append(dotsContainer);

      $(".cps-dot").on("click", function () {
        currentIndex = parseInt($(this).data("index"));
        updateSlides();
        resetTimer();
      });
    }

    $("#cps-next")
      .off("click")
      .on("click", function () {
        nextSlide();
        resetTimer();
      });

    $("#cps-prev")
      .off("click")
      .on("click", function () {
        prevSlide();
        resetTimer();
      });

    updateSlides();
    timer = setInterval(nextSlide, 5000);
  }
});
