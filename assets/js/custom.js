const Luxit = (function () {
  const handleCursorsection = () => {
    let cursor = document.querySelector(".cursor");
    let cursor2 = document.querySelector(".cursor2");
    let cursorScale = document.querySelectorAll(".cursor-scale");
    let mouseX = 0;
    let mouseY = 0;

    gsap.to({}, 0.016, {
      repeat: -1,
      onRepeat: function () {
        gsap.set(cursor, {
          css: {
            left: mouseX,
            top: mouseY,
          },
        });
        gsap.set(cursor2, {
          css: {
            left: mouseX,
            top: mouseY,
          },
        });
      },
    });

    window.addEventListener("mousemove", (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    cursorScale.forEach((link) => {
      link.addEventListener("mousemove", () => {
        cursor.classList.add("grow");
        if (link.classList.contains("small")) {
          cursor.classList.remove("grow");
          cursor.classList.add("grow-small");
        }
      });

      link.addEventListener("mouseleave", () => {
        cursor.classList.remove("grow");
        cursor.classList.remove("grow-small");
      });
    });
  };

  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $(".sticky-header").addClass("is-fixed");
    } else {
      $(".sticky-header").removeClass("is-fixed");
    }
  });

  const handleColorfillheader = () => {
    const scroll = window.scrollY;
    const header = document.querySelector(".is-fixed");

    if (!header) return;

    if (scroll >= 100) {
      header.classList.add("color-fill");
    } else {
      header.classList.remove("color-fill");
    }
  };

  function handleMobilesidedrawer() {
    jQuery("#mobile-side-drawer").on("click", function () {
      jQuery(".mobile-sider-drawer-menu").toggleClass("active");
    });
  }

  function handleSitesearch() {
    jQuery('a[href="#search"]').on("click", function (event) {
      jQuery("#search").addClass("open");
      jQuery('#search > form > input[type="search"]').focus();
    });

    jQuery("#search, #search .close-btn").on("click keyup", function (event) {
      if (event.target === this || event.target.className === "close-btn") {
        jQuery(this).removeClass("open");
      }
    });
  }

  function handleMagnificvideo() {
    if (jQuery(".popup-youtube").length) {
      jQuery(".popup-youtube").magnificPopup({
        type: "iframe",
      });
    }
  }

  const handleCounterJS = () => {
    const counters = document.querySelectorAll(".value");
    const speed = 200;

    const runCounter = (counter) => {
      const target = +counter.getAttribute("data-value");
      let current = 0;
      const increment = target / speed;

      const update = () => {
        current += increment;
        if (current < target) {
          counter.innerText = Math.ceil(current);
          requestAnimationFrame(update);
        } else {
          counter.innerText = target;
        }
      };

      update();
    };

    const isInViewport = (el) => {
      const rect = el.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.bottom <=
          (window.innerHeight || document.documentElement.clientHeight)
      );
    };

    const handleScroll = () => {
      counters.forEach((counter) => {
        if (!counter.classList.contains("counted") && isInViewport(counter)) {
          counter.classList.add("counted");
          runCounter(counter);
        }
      });
    };

    window.addEventListener("scroll", handleScroll);
    handleScroll();
  };

  if (jQuery(".emblem").length) {
    const Emblem = {
      init: function (el, str) {
        const element = document.querySelector(el);
        const text = str ? str : element.innerHTML;
        element.innerHTML = "";
        for (let i = 0; i < text.length; i++) {
          const letter = text[i];
          const span = document.createElement("span");
          const node = document.createTextNode(letter);
          const r = (360 / text.length) * i;
          const x = (Math.PI / text.length).toFixed(0) * i;
          const y = (Math.PI / text.length).toFixed(0) * i;
          span.appendChild(node);
          span.style.webkitTransform =
            "rotateZ(" + r + "deg) translate3d(" + x + "px," + y + "px,0)";
          span.style.transform =
            "rotateZ(" + r + "deg) translate3d(" + x + "px," + y + "px,0)";
          element.appendChild(span);
        }
      },
    };

    Emblem.init(".emblem");
  }

  const handleSetCurrentYear = () => {
    const currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let elements = document.getElementsByClassName("current-year");

    for (const element of elements) {
      element.innerHTML = currentYear;
    }
  };

  function handleTvrRainEffetctfunction() {
    $(".rain").empty();

    let increment = 0;
    let drops = "";
    let backDrops = "";

    while (increment < 100) {
      const randoHundo = Math.floor(Math.random() * (98 - 1 + 1) + 1);
      const randoFiver = Math.floor(Math.random() * (5 - 2 + 1) + 2);

      increment += randoFiver;

      drops +=
        '<div class="drop" style="left: ' +
        increment +
        "%; bottom: " +
        (randoFiver + randoFiver - 1 + 100) +
        "%; animation-delay: 0." +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;">' +
        '<div class="stem" style="animation-delay: 0.' +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;"></div>' +
        '<div class="splat" style="animation-delay: 0.' +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;"></div>' +
        "</div>";

      backDrops +=
        '<div class="drop" style="right: ' +
        increment +
        "%; bottom: " +
        (randoFiver + randoFiver - 1 + 100) +
        "%; animation-delay: 0." +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;">' +
        '<div class="stem" style="animation-delay: 0.' +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;"></div>' +
        '<div class="splat" style="animation-delay: 0.' +
        randoHundo +
        "s; animation-duration: 0.5" +
        randoHundo +
        's;"></div>' +
        "</div>";
    }

    $(".rain.front-row").append(drops);
    $(".rain.back-row").append(backDrops);
  }

  function handleScrollTop() {
    jQuery("button.scroltop").on("click", function () {
      jQuery("html, body").animate(
        {
          scrollTop: 0,
        },
        1000,
      );
      return false;
    });

    jQuery(window).on("scroll", function () {
      const scroll = jQuery(window).scrollTop();

      if (scroll > 900) {
        jQuery("button.scroltop").fadeIn(1000);
      } else {
        jQuery("button.scroltop").fadeOut(1000);
      }
    });
  }

  const handleCustomSelects = () => {
    document.querySelectorAll(".dynamic-select").forEach((selectElement) => {
      createCustomSelectFromSelect(selectElement);
    });
  };

  const createCustomSelectFromSelect = (selectElement) => {
    const selectId =
      selectElement.id || `select-${Math.random().toString(36).substr(2, 9)}`;
    const customSelectDiv = document.createElement("div");
    customSelectDiv.id = `custom-${selectId}`;
    customSelectDiv.className = "custom-select";

    const selectedDiv = document.createElement("div");
    selectedDiv.className = "select-selected";
    selectedDiv.textContent = (
      selectElement.querySelector("option[selected]") ||
      selectElement.options[0]
    ).textContent;

    const labelText = selectElement.parentElement?.dataset?.label || "";
    if (labelText) {
      const label = document.createElement("span");
      label.textContent = labelText;
      selectedDiv.appendChild(label);
    }

    customSelectDiv.appendChild(selectedDiv);

    const itemsDiv = document.createElement("div");
    itemsDiv.className = "select-items select-hide";
    customSelectDiv.appendChild(itemsDiv);

    Array.from(selectElement.options).forEach((option) => {
      const customOptionDiv = document.createElement("div");
      customOptionDiv.className = "select-item";
      customOptionDiv.setAttribute("data-value", option.value);
      customOptionDiv.textContent = option.textContent;
      if (option.selected) customOptionDiv.classList.add("active");

      customOptionDiv.addEventListener("click", function () {
        selectedDiv.childNodes[0].textContent = this.textContent;
        selectElement.value = this.getAttribute("data-value");
        selectElement.dispatchEvent(new Event("change"));
        selectElement.dispatchEvent(new Event("click"));

        itemsDiv.classList.add("select-hide");
        selectedDiv.classList.remove("select-active");

        itemsDiv
          .querySelectorAll(".select-item")
          .forEach((item) => item.classList.remove("active"));
        this.classList.add("active");
      });

      itemsDiv.appendChild(customOptionDiv);
    });

    selectElement.style.display = "none";
    selectElement.parentNode.insertBefore(
      customSelectDiv,
      selectElement.nextSibling,
    );

    selectedDiv.addEventListener("click", function (e) {
      e.stopPropagation();
      itemsDiv.classList.toggle("select-hide");
      selectedDiv.classList.toggle("select-active");
    });

    document.addEventListener("click", function (e) {
      if (!customSelectDiv.contains(e.target)) {
        itemsDiv.classList.add("select-hide");
        selectedDiv.classList.remove("select-active");
      }
    });
  };

  const handleAccordion = (container = document) => {
    const accordionContainers = container.querySelectorAll(".myAccordion");

    accordionContainers.forEach((accordion) => {
      if (accordion.dataset.bound === "true") return;
      accordion.dataset.bound = "true";

      accordion.addEventListener("click", function (e) {
        const header = e.target.closest(".accordion-header");
        if (!header || !accordion.contains(header)) return;

        const item = header.parentElement;
        const content = item.querySelector(".accordion-content");
        const arrow = header.querySelector(".arrow");
        const isOpen = header.classList.contains("open");

        accordion.querySelectorAll(".accordion-header").forEach((h) => {
          if (h !== header) {
            h.classList.remove("open");
            h.querySelector(".arrow")?.classList.remove("active");
            const c = h.parentElement.querySelector(".accordion-content");
            if (c) c.style.maxHeight = null;
          }
        });

        if (!isOpen) {
          header.classList.add("open");
          content.style.maxHeight = content.scrollHeight + "px";
          arrow?.classList.add("active");
        } else {
          header.classList.remove("open");
          content.style.maxHeight = null;
          arrow?.classList.remove("active");
        }
      });
    });

    container.querySelectorAll(".accordion-header.open").forEach((header) => {
      const content = header.parentElement.querySelector(".accordion-content");
      const arrow = header.querySelector(".arrow");
      if (content) {
        content.style.maxHeight = content.scrollHeight + "px";
        arrow?.classList.add("active");
      }
    });
  };

  function handleLightboxPopup() {
    lc_lightbox(".elem", {
      wrap_class: "lcl_fade_oc",
      gallery: true,
      thumb_attr: "data-lcl-thumb",

      skin: "minimal",
      radius: 0,
      padding: 0,
      border_w: 0,
    });
  }

  const handleTouchSpin = () => {
    function incrementValue(e) {
      e.preventDefault();
      const button = e.target.closest("[data-field]");
      const fieldName = button.getAttribute("data-field");

      const parent = button.closest("div") || button.closest("td");
      const input = parent.querySelector(`input[name="${fieldName}"]`);

      let currentVal = parseInt(input.value, 10);
      input.value = !isNaN(currentVal) ? currentVal + 1 : 0;
    }

    function decrementValue(e) {
      e.preventDefault();
      const button = e.target.closest("[data-field]");
      const fieldName = button.getAttribute("data-field");

      const parent = button.closest("div") || button.closest("td");
      const input = parent.querySelector(`input[name="${fieldName}"]`);

      let currentVal = parseInt(input.value, 10);
      input.value = !isNaN(currentVal) && currentVal > 0 ? currentVal - 1 : 0;
    }

    document.querySelectorAll(".input-group").forEach((group) => {
      group.addEventListener("click", function (e) {
        const target = e.target.closest(".button-plus, .button-minus");
        if (!target) return;

        if (target.classList.contains("button-plus")) {
          incrementValue(e);
        } else if (target.classList.contains("button-minus")) {
          decrementValue(e);
        }
      });
    });
  };

  const handleflatpickr = () => {
    if (jQuery(".flatpickr1").length > 0) {
      flatpickr(".flatpickr1", {});
    }
    if (jQuery(".time-picker").length > 0) {
      flatpickr(".time-picker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "13:45",
      });
    }
  };

  const handleTagSlider = () => {
    if (jQuery("#tagSlider").length > 0) {
      $("#tagSlider").grouploop({
        velocity: 1,
        forward: false,
        pauseOnHover: true,
        childNode: ".item",
        childWrapper: ".item-wrap",
      });
    }
    if (jQuery("#tagSlider2").length > 0) {
      $("#tagSlider2").grouploop({
        velocity: 1,
        forward: true,
        pauseOnHover: true,
        childNode: ".item",
        childWrapper: ".item-wrap",
      });
    }
  };

  const handleShopProductPrice = () => {
    const directionSlider = document.getElementById("slider-direction");
    const directionField = document.getElementById("field");

    if (!directionSlider || !directionField) return;

    noUiSlider.create(directionSlider, {
      start: 2,
      connect: [true, false],
      range: {
        min: 0,
        max: 10,
      },
    });

    directionSlider.noUiSlider.on("update", (values, handle) => {
      directionField.innerHTML = values[handle];
    });
  };

  const handlePageLoader = () => {
    $(".loading-area").fadeOut(1500);
  };

  function masonryBox() {
    if (jQuery().isotope) {
      var $container = jQuery(".masonry-wrap");

      $container.isotope({
        itemSelector: ".masonry-item",
        transitionDuration: "1s",
        originLeft: true,
        stamp: ".stamp",
        percentPosition: true,
        layoutMode: "masonry",
      });

      $container.imagesLoaded().progress(function () {
        $container.isotope("layout");
      });

      jQuery(".masonry-filter li").on("click", function () {
        var selector = jQuery(this).find("a").attr("data-filter");
        jQuery(".masonry-filter li").removeClass("active");
        jQuery(this).addClass("active");
        $container.isotope({ filter: selector });
        return false;
      });
    }
  }

  function sticky_sidebar() {
    $(".rightSidebar").theiaStickySidebar({
      additionalMarginTop: 100,
    });
  }

  const handleMasonry = () => {
    const masonryContainer = document.querySelector(".masonry-grid");
    if (masonryContainer) {
      new Masonry(masonryContainer, {
        itemSelector: ".card-container",
        columnWidth: ".grid-sizer",
        percentPosition: true,
      });
    }
  };

  document.addEventListener("DOMContentLoaded", function () {
    function handleGallerySlider(topId, thumbsId) {
      var galleryThumbs = new Swiper(thumbsId, {
        centeredSlides: false,
        centeredSlidesBounds: true,
        loop: true,
        spaceBetween: 30,
        slidesPerView: 4,
        freeMode: false,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        watchOverflow: true,
        breakpoints: {
          0: { slidesPerView: 2 },
          480: { slidesPerView: 3 },
          768: { slidesPerView: 4 },
          1024: { slidesPerView: 4 },
          1365: { slidesPerView: 4 },
        },
      });

      var galleryTop = new Swiper(topId, {
        direction: "horizontal",
        spaceBetween: 0,
        loop: true,

        // ✅ Navigation Buttons
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },

        // ✅ Pagination Added Here
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },

        thumbs: {
          swiper: galleryThumbs,
        },
      });

      galleryTop.on("slideChangeTransitionStart", function () {
        galleryThumbs.slideTo(galleryTop.activeIndex);
      });

      galleryThumbs.on("transitionStart", function () {
        galleryTop.slideTo(galleryThumbs.activeIndex);
      });
    }

    // Initialize sliders
    handleGallerySlider(".twm-gallery-top1", ".twm-gallery-thumbs1");
    handleGallerySlider(".twm-gallery-top2", ".twm-gallery-thumbs2");
    handleGallerySlider(".twm-gallery-top3", ".twm-gallery-thumbs3");
    handleGallerySlider(".twm-gallery-top4", ".twm-gallery-thumbs4");
  });

  return {
    init: function () {
      handleCursorsection();
      handleColorfillheader();
      handleMobilesidedrawer();
      handleSitesearch();
      handleTvrRainEffetctfunction();
      handleMagnificvideo();
      handleScrollTop();
      handleCounterJS();
      handleSetCurrentYear();
      handleCustomSelects();
      handleAccordion();
      handleLightboxPopup();
      handleTouchSpin();
      handleflatpickr();
      handleTagSlider();
      handleShopProductPrice();
      masonryBox();
      handleMasonry();
      sticky_sidebar();
      handlePageLoader();
    },
  };
})();

document.addEventListener("DOMContentLoaded", () => {
  Luxit.init();
});

/* ─────────────────────────────────────────────────────────────
   MEGA-MENU UPGRADE  (runs synchronously — before xmenu init)
   Skips pages that already have header.php with .mm-parent
───────────────────────────────────────────────────────────── */
(function injectMegaMenus() {
  if (document.querySelector('.mm-parent')) return; // already done (index.php)
  const navUl = document.querySelector('.navbar-nav');
  if (!navUl) return;

  /* ── inject CSS ── */
  const s = document.createElement('style');
  s.textContent = `
    @media(min-width:1024px){
      li.mm-parent{position:static!important}
      li.mm-parent>.mega-menu{position:absolute;top:100%;left:0;right:0;z-index:99999;padding-top:8px}
      .mm-panel{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(6,97,104,.13),0 4px 20px rgba(0,0,0,.07);border:1px solid rgba(0,0,0,.07);overflow:hidden}
      .mm-topbar,.mm-botbar{display:flex!important;align-items:center;justify-content:space-between;padding:11px 32px}
      .mm-topbar{background:rgba(6,97,104,.04);border-bottom:1px solid rgba(0,0,0,.06)}
      .mm-botbar{background:#f9fafb;border-top:1px solid rgba(0,0,0,.06)}
      .mm-topbar-label{font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.14em}
      .mm-topbar-link{font-size:12px;font-weight:600;color:#066168;display:flex;align-items:center;gap:4px;transition:gap .2s}
      .mm-topbar-link:hover{gap:8px}
      .mm-body{padding:22px 8px}
      .mm-4col{display:grid;grid-template-columns:repeat(4,1fr)}
      .mm-3col{display:grid;grid-template-columns:1fr 2fr}
      .mm-col{padding:0 20px}
      .mm-col+.mm-col,.mm-col+.mm-col2{border-left:1px solid rgba(0,0,0,.07)}
      .mm-col2{padding:0 20px;display:grid;grid-template-columns:1fr 1fr;gap:0 12px}
      .mm-col-head{display:flex!important;align-items:center;gap:10px;margin-bottom:10px}
      .mm-icon{width:30px;height:30px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0}
      .mm-col-title{font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.14em}
      .mm-col-count{font-size:9.5px;color:#9ca3af;margin-top:1px}
      .mm-divider{display:block!important;width:18px;height:2px;border-radius:9999px;margin-bottom:12px}
      .mm-link{display:block;font-size:13.5px;font-weight:500;color:#374151;padding:5px 0;transition:color .15s,padding-left .15s}
      .mm-link:hover{color:#066168;padding-left:5px}
      .mm-plink{display:flex;align-items:flex-start;gap:12px;padding:7px 0;cursor:pointer}
      .mm-plink:hover .mm-picon{background:rgba(6,97,104,.12)}
      .mm-plink:hover .mm-ptitle{color:#066168}
      .mm-picon{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;transition:background .15s}
      .mm-ptitle{font-size:13.5px;font-weight:600;color:#1f2937;transition:color .15s;display:block;line-height:1.3}
      .mm-pdesc{font-size:10.5px;color:#9ca3af;display:block;margin-top:2px}
      .mm-xlink{display:flex;align-items:center;gap:9px;padding:6px 0;font-size:13.5px;font-weight:500;color:#374151;transition:color .15s}
      .mm-xlink:hover{color:#066168}
      .mm-xicon{width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
      .mm-btn{display:inline-flex;align-items:center;gap:7px;background:#066168;color:#fff!important;font-size:11.5px;font-weight:700;padding:7px 14px;border-radius:8px;transition:background .2s}
      .mm-btn:hover{background:#054f55}
      .mm-botbar-note{font-size:11.5px;color:#9ca3af}
    }
    .mm-topbar,.mm-botbar,.mm-col-head,.mm-divider,.mm-pdesc{display:none}
    .mm-moblabel{display:block;padding:9px 20px;font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;background:#f9fafb;border-bottom:1px solid rgba(0,0,0,.05)}
    @media(min-width:1024px){.mm-moblabel{display:none}}
    @media(max-width:1023px){
      .mm-col{padding:0}.mm-col+.mm-col,.mm-col+.mm-col2{border-left:none}
      .mm-col2{padding:0;display:block}
      .mm-link{padding:10px 20px;border-bottom:1px solid rgba(0,0,0,.05)}
      .mm-plink{padding:10px 20px;border-bottom:1px solid rgba(0,0,0,.05);gap:0}
      .mm-picon,.mm-xicon{display:none}
      .mm-xlink{padding:10px 20px;border-bottom:1px solid rgba(0,0,0,.05);gap:0}
      .mm-body{padding:0}
      .mm-ptitle{font-size:14px;font-weight:500;color:#1f2937}
    }
  `;
  document.head.appendChild(s);

  /* ── Destinations mega-menu HTML ── */
  const destHTML = `<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group mm-parent">
    <a class="lg:py-7.5 py-2 2xxl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium text-black hover:text-secondary" href="destinations.php">
      <span class="inline-block">Destinations</span>
      <i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-black float-end"></i>
    </a>
    <div class="mega-menu max-lg:hidden lg:opacity-0 lg:invisible lg:translate-y-10 duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0">
      <div class="mm-panel">
        <div class="mm-topbar">
          <div style="display:flex;align-items:center;gap:8px"><i class="fas fa-globe-africa" style="color:#066168;font-size:14px"></i><span class="mm-topbar-label">Explore World Destinations</span></div>
          <a href="destinations.php" class="mm-topbar-link">All Destinations <i class="fas fa-long-arrow-alt-right" style="font-size:11px"></i></a>
        </div>
        <div class="mm-body mm-4col">
          <div class="mm-col">
            <div class="mm-col-head"><span class="mm-icon" style="background:#f0fdf4;border:1px solid #bbf7d0"><i class="fas fa-leaf" style="color:#16a34a"></i></span><div><p class="mm-col-title" style="color:#15803d">Africa</p><p class="mm-col-count">13 destinations</p></div></div>
            <div class="mm-divider" style="background:#86efac"></div>
            <span class="mm-moblabel">Africa</span>
            <a class="mm-link" href="kenya.php">Kenya</a><a class="mm-link" href="uganda.php">Uganda</a><a class="mm-link" href="tanzania.php">Tanzania</a><a class="mm-link" href="seychelles.php">Seychelles</a><a class="mm-link" href="madagascar.php">Madagascar</a><a class="mm-link" href="zambia.php">Zambia</a><a class="mm-link" href="zimbabwe.php">Zimbabwe</a><a class="mm-link" href="rwanda.php">Rwanda</a><a class="mm-link" href="south-africa.php">South Africa</a><a class="mm-link" href="namibia.php">Namibia</a><a class="mm-link" href="botswana.php">Botswana</a><a class="mm-link" href="morocco.php">Morocco</a><a class="mm-link" href="egypt.php">Egypt</a>
          </div>
          <div class="mm-col">
            <div class="mm-col-head"><span class="mm-icon" style="background:#fffbeb;border:1px solid #fde68a"><i class="fas fa-moon" style="color:#d97706"></i></span><div><p class="mm-col-title" style="color:#b45309">Middle East</p><p class="mm-col-count">3 destinations</p></div></div>
            <div class="mm-divider" style="background:#fcd34d"></div>
            <span class="mm-moblabel">Middle East</span>
            <a class="mm-link" href="dubai.php">Dubai</a><a class="mm-link" href="oman.php">Oman</a><a class="mm-link" href="jordan.php">Jordan</a>
          </div>
          <div class="mm-col">
            <div class="mm-col-head"><span class="mm-icon" style="background:#eff6ff;border:1px solid #bfdbfe"><i class="fas fa-torii-gate" style="color:#2563eb"></i></span><div><p class="mm-col-title" style="color:#1d4ed8">Asia</p><p class="mm-col-count">8 destinations</p></div></div>
            <div class="mm-divider" style="background:#93c5fd"></div>
            <span class="mm-moblabel">Asia</span>
            <a class="mm-link" href="thailand.php">Thailand</a><a class="mm-link" href="singapore.php">Singapore</a><a class="mm-link" href="philippines.php">Philippines</a><a class="mm-link" href="maldives.php">Maldives</a><a class="mm-link" href="china.php">China</a><a class="mm-link" href="malaysia.php">Malaysia</a><a class="mm-link" href="india.php">India</a><a class="mm-link" href="indonesia.php">Indonesia</a>
          </div>
          <div class="mm-col">
            <div class="mm-col-head"><span class="mm-icon" style="background:#faf5ff;border:1px solid #e9d5ff"><i class="fas fa-landmark" style="color:#7c3aed"></i></span><div><p class="mm-col-title" style="color:#6d28d9">Europe</p><p class="mm-col-count">5 destinations</p></div></div>
            <div class="mm-divider" style="background:#c4b5fd"></div>
            <span class="mm-moblabel">Europe</span>
            <a class="mm-link" href="spain.php">Spain</a><a class="mm-link" href="france.php">France</a><a class="mm-link" href="italy.php">Italy</a><a class="mm-link" href="greece.php">Greece</a><a class="mm-link" href="turkey.php">Turkey</a>
          </div>
        </div>
        <div class="mm-botbar">
          <span class="mm-botbar-note"><i class="fas fa-shield-alt" style="color:#066168;margin-right:6px"></i>IATA accredited &amp; fully licensed agency</span>
          <a href="contact.php" class="mm-btn"><i class="fas fa-headset" style="font-size:11px"></i> Talk to an Expert</a>
        </div>
      </div>
    </div>
  </li>`;

  /* ── Tours mega-menu HTML ── */
  const toursHTML = `<li class="lg:inline-block block max-lg:border-b max-lg:border-gray-200 relative group mm-parent">
    <a class="lg:py-7.5 py-2 xl:px-5 lg:px-2 relative lg:inline-block block text-lg font-medium text-black hover:text-secondary" href="javascript:void(0);">
      <span class="inline-block">Tours</span>
      <i class="fas fa-chevron-right lg:!hidden !block size-7 !leading-7 text-center text-xs bg-black text-white float-end"></i>
    </a>
    <div class="mega-menu max-lg:hidden lg:opacity-0 lg:invisible lg:translate-y-10 duration-500 lg:group-hover:opacity-100 lg:group-hover:visible lg:group-hover:translate-y-0">
      <div class="mm-panel">
        <div class="mm-topbar">
          <div style="display:flex;align-items:center;gap:8px"><i class="fas fa-route" style="color:#066168;font-size:14px"></i><span class="mm-topbar-label">Our Tour Packages</span></div>
          <a href="local-packages.php" class="mm-topbar-link">Browse all packages <i class="fas fa-long-arrow-alt-right" style="font-size:11px"></i></a>
        </div>
        <div class="mm-body mm-3col">
          <div class="mm-col">
            <div class="mm-col-head"><span class="mm-icon" style="background:rgba(6,97,104,.08);border:1px solid rgba(6,97,104,.2)"><i class="fas fa-suitcase-rolling" style="color:#066168"></i></span><p class="mm-col-title" style="color:#066168">Package Types</p></div>
            <div class="mm-divider" style="background:rgba(6,97,104,.3)"></div>
            <span class="mm-moblabel">Package Types</span>
            <a href="local-packages.php" class="mm-plink"><span class="mm-picon" style="background:rgba(6,97,104,.06)"><i class="fas fa-map-marker-alt" style="color:#066168"></i></span><span><span class="mm-ptitle">Local Packages</span><span class="mm-pdesc">East Africa &amp; beyond</span></span></a>
            <a href="international-packages.php" class="mm-plink"><span class="mm-picon" style="background:#eff6ff"><i class="fas fa-plane" style="color:#2563eb"></i></span><span><span class="mm-ptitle">International Packages</span><span class="mm-pdesc">Worldwide destinations</span></span></a>
            <a href="safari-packages.php" class="mm-plink"><span class="mm-picon" style="background:#fffbeb"><i class="fas fa-paw" style="color:#d97706"></i></span><span><span class="mm-ptitle">Safari Packages</span><span class="mm-pdesc">Wildlife &amp; game drives</span></span></a>
          </div>
          <div class="mm-col2">
            <div style="grid-column:span 2" class="mm-col-head"><span class="mm-icon" style="background:#fefce8;border:1px solid #fef08a"><i class="fas fa-star" style="color:#ca8a04"></i></span><p class="mm-col-title" style="color:#92400e">Special Experiences</p></div>
            <div class="mm-divider" style="grid-column:span 2;background:#fcd34d"></div>
            <span class="mm-moblabel" style="grid-column:span 2">Special Packages</span>
            <div>
              <a href="halal-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#f0fdf4">🕌</span>Halal Travel</a>
              <a href="kosher-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#eff6ff">✡️</span>Kosher Travel</a>
              <a href="accessible-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#f0f9ff">♿</span>Accessible Travel</a>
              <a href="mice-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#f9fafb">🤝</span>MICE Travel</a>
            </div>
            <div>
              <a href="romantic-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#fff1f2">💑</span>Romantic Travel</a>
              <a href="wellness-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#f0fdf4">🌿</span>Wellness Travel</a>
              <a href="faith-travel.php" class="mm-xlink"><span class="mm-xicon" style="background:#fffbeb">✝️</span>Faith Travel</a>
            </div>
          </div>
        </div>
        <div class="mm-botbar">
          <span class="mm-botbar-note"><i class="fas fa-award" style="color:#066168;margin-right:6px"></i>Award-winning tours since 2010</span>
          <a href="contact.php" class="mm-btn"><i class="fas fa-calendar-check" style="font-size:11px"></i> Book a Tour</a>
        </div>
      </div>
    </div>
  </li>`;

  /* ── Find and replace the old nav items ── */
  const items = Array.from(navUl.querySelectorAll(':scope > li'));
  items.forEach(li => {
    const a = li.querySelector(':scope > a');
    if (!a) return;
    const href = (a.getAttribute('href') || '').toLowerCase();
    const text = a.textContent.trim();

    if (href === 'destinations.php' && li.querySelector('.sub-menu-down, .sub-menu')) {
      li.insertAdjacentHTML('afterend', destHTML);
      li.remove();
    } else if (text === 'Tours' && li.querySelector('.sub-menu')) {
      li.insertAdjacentHTML('afterend', toursHTML);
      li.remove();
    }
  });
})();
