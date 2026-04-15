/**
 * File main.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 * 
 * Also handles all the search forms and block animations.
 */

const Alpha1Million = {};
Alpha1Million.viewedStories = 0;
Alpha1Million.viewedVideos = 0;

let activeGradientIndex = 1;
const isTouchMobile =
  "ontouchstart" in window ||
  navigator.maxTouchPoints > 0 ||
  window.innerWidth <= 1024;

class GA4Analytics {
  constructor(measurementId) {
    this.measurementId = measurementId;

    if (!window.gtag) {
      console.warn("gtag is not available. Did you include the GA4 script?");
    }
  }

  /**
   * Logs an event to Google Analytics 4
   * @param {string} eventName - The name of the event (e.g., 'button_click')
   * @param {object} [params={}] - Optional event parameters (e.g., { label: 'Subscribe Button' })
   */
  sendEvent(eventName, params = {}) {
    if (typeof gtag !== "function") {
      console.warn("gtag is not defined. Shame. Skipping event:", eventName, params);
      return;
    }

    gtag("event", eventName, params);
  }
}

const ga_id = "G-VR5S0S5WTH";
const analytics = new GA4Analytics(ga_id);

class ChurchAwesomenessCards {
  constructor({
    itemSelector,
    containerSelector,
    templateSelector,
    maxCards = 5,
    analytics = null
  }) {
    this.itemSelector = itemSelector;
    this.container = document.querySelector(containerSelector);
    this.template = document.querySelector(templateSelector);
    this.cardHistory = [];
    this.hoverTimers = new WeakMap(); // Track timers per item
    this.maxCards = maxCards;
    this.lastScrollItem = null;

    this.handleScroll = this.handleScroll.bind(this);

    this.loadingTemplate = ` <div class="church-popup__content">
            <h5 class="loading text-red text-2xl font-instrument"></h5>
            <h2 class="loading text-red font-poppins bold text-4xl"></h2>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading mid"></p>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading short"></p>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading mid"></p>
            <p class="loading line"></p>
            <p class="loading line"></p>
            <p class="loading mid"></p>
        </div>`;

    this.analytics = analytics || new GA4Analytics(ga_id);

    this.init();

    this.setupModal();
  }

  init() {
    const items = document.querySelectorAll(this.itemSelector);
    this.items = items;

    items.forEach((item) => {
      item.addEventListener("mouseenter", () => this.handleMouseEnter(item));
      item.addEventListener("mouseleave", () => this.handleMouseLeave(item));
      item.addEventListener("click", () => this.handleClick(item));
    });

    if (isTouchMobile) {
      window.addEventListener("scroll", this.handleScroll, { passive: true });
      this.handleScroll(); // run once on load
    }
  }

  handleMouseEnter(item) {
    // Cancel any pending timer
    const existing = this.hoverTimers.get(item);
    if (existing?.card && existing.timeoutId) {
      clearTimeout(existing.timeoutId);
      this.hoverTimers.delete(item);
    }

    const that = this;

    // Create card if not already created
    if (!this.cardHistory.find((c) => c.source === item)) {
      const card = this.createCardFromItem(item);
      this.container.insertBefore(card, this.container.firstChild);
      this.cardHistory.unshift({ el: card, source: item });
      this.enforceCardLimit();
      this.updateCardIndexes();

      /*
      console.log('Card Item', item, that);
      that.analytics.sendEvent("church_hover", {
        church_story: item.dataset.churchName,
        church_name: item.dataset.churchChurch,
      });
      */
    }

    if (isTouchMobile) {
      this.items.forEach((i) => i.classList.remove("active"));
      item.classList.add("active");
    }
  }

  handleMouseLeave(item) {
    const cardObj = this.cardHistory.find((c) => c.source === item);
    if (!cardObj) return;

    const timeoutId = setTimeout(() => {
      this.fadeOutAndRemove(cardObj.el);
      this.cardHistory = this.cardHistory.filter((c) => c.el !== cardObj.el);
      this.updateCardIndexes();
    }, 1000);

    this.hoverTimers.set(item, { card: cardObj.el, timeoutId });

    item.classList.add("active");

    if (isTouchMobile) return;

    setTimeout(() => {
      item.classList.remove("active");
    }, 250);
  }

  fadeOutAndRemove(card) {
    card.classList.add("fade-out");
    card.addEventListener(
      "transitionend",
      () => {
        card.remove();
      },
      { once: true }
    );
  }

  createCardFromItem(item) {
    const clone = this.template.content.cloneNode(true);
    const card = clone.querySelector(".church-awesomeness__card");

    card.querySelector(".name").textContent = item.dataset.churchName;
    card.querySelector(".preview").textContent = item.dataset.churchPreview;
    card.querySelector(".church").textContent = item.dataset.churchChurch;

    card.dataset.cardIndex = 0;
    card.style.setProperty("--_card-index", 0);

    return card;
  }

  updateCardIndexes() {
    this.cardHistory.forEach((c, i) => {
      const index = i + 1;
      c.el.dataset.cardIndex = index;
      c.el.style.setProperty("--_card-index", index);
    });
  }

  enforceCardLimit() {
    while (this.cardHistory.length > this.maxCards) {
      const oldest = this.cardHistory.pop();
      this.fadeOutAndRemove(oldest.el);
    }
  }

  handleClick(item) {
    const church = {
      church: item.dataset.churchChurch,
      name: item.dataset.churchName,
    };
    this.openModalWithChurch(item.dataset.churchId, church);
  }

  setupModal() {
    // Basic modal structure
    this.modal = document.createElement("dialog");
    this.modal.classList.add("church-popup");
    this.modal.innerHTML = `
      <button class="church-popup__close">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
          <rect width="32" height="32" rx="16" fill="black"/>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M16 17.5556L22.2222 23.7778L23.7778 22.2222L17.5556 16L23.7778 9.77778L22.2222 8.22223L16 14.4444L9.77778 8.22222L8.22222 9.77778L14.4444 16L8.22222 22.2222L9.77778 23.7778L16 17.5556Z" fill="white"/>
        </svg>
      </button>
      <div class="church-popup__body" data-lenis-prevent>
       
      </div>
    `;
    document.body.appendChild(this.modal);

    this.modal
      .querySelector(".church-popup__close")
      .addEventListener("click", () => this.closeModal());

    // Escape key support
    this._handleEscape = (e) => {
      if (e.key === "Escape") {
        this.closeModal();
      }
    };
    document.addEventListener("keydown", this._handleEscape);
  }

  openModalWithChurch(churchId, church) {
    const contentEl = this.modal.querySelector(".church-popup__body");
    contentEl.innerHTML = this.loadingTemplate;

    if (!this.modal.open) this.modal.showModal();

    setTimeout(() => {
      this.modal.classList.add("active");
    }, 250);

    const { church: churchName, name: personName } = church;

    Alpha1Million.viewedStories++;

    this.analytics.sendEvent("church_modal_open", {
      church_story: personName,
      church_name: churchName,
      viewed_stories: Alpha1Million.viewedStories,
    });

    fetch(`/wp-json/wp/v2/posts/${churchId}`)
      .then((res) => {
        if (!res.ok) throw new Error("Post not found");
        return res.json();
      })
      .then((data) => {
        //<h2>${data.title.rendered}</h2>;
        this.modal.dataset.churchName = personName;
        this.modal.dataset.churchChurch = churchName;

        contentEl.innerHTML = `
          <div class="church-popup__content">
            <h5 class="text-red text-2xl font-instrument">${personName}'s story</h5>
            <h2 class="text-red font-poppins bold text-4xl">${churchName}</h2>
            ${data.content.rendered}
          </div>
        `;

        if (window.locomotiveScroll) {
          //console.log("Stopping locomotive scroll");
          window.locomotiveScroll.stop();
        }
      })
      .catch((err) => {
        contentEl.innerHTML = `<p>Error loading content.</p>`;
        console.error(err);
      });
  }

  closeModal() {
    this.modal.classList.remove("active");

    setTimeout(() => {
      this.modal.close();
    }, 250);

    const dataset = this.modal.dataset;

    this.analytics.sendEvent("church_modal_close", {
      church_story: dataset.churchName,
      church_name: dataset.churchChurch,
    });

    if (window.locomotiveScroll) {
      window.locomotiveScroll.start();
    }
  }

  // For tablet and mobile
  handleScroll() {
    if (this.scrollAnimationFrame) return;

    this.scrollAnimationFrame = requestAnimationFrame(() => {
      this.scrollAnimationFrame = null;

      const items = document.querySelectorAll(this.itemSelector);

      // Cleanup first and last items if they're out of view
      const firstItem = document.querySelector(this.itemSelector);
      const lastItem = [...document.querySelectorAll(this.itemSelector)].pop();

      let closestItem = null;
      let closestDistance = Infinity;

      const viewportCenter = window.innerHeight / 2;

      items.forEach((item) => {
        const rect = item.getBoundingClientRect();
        const itemCenter = rect.top + rect.height / 2;
        const distance = Math.abs(viewportCenter - itemCenter);

        if (distance < closestDistance) {
          closestDistance = distance;
          closestItem = item;
        }
      });

      if (closestItem && closestItem !== this.lastScrollItem) {
        if (this.lastScrollItem) {
          this.handleMouseLeave(this.lastScrollItem); // clean up previous
        }
        this.handleMouseEnter(closestItem); // simulate hover
        this.lastScrollItem = closestItem;
      }


      [firstItem, lastItem].forEach((edgeItem) => {
        if (!edgeItem) return;

        const rect = edgeItem.getBoundingClientRect();
        const isVisible = rect.bottom > 0 && rect.top < window.innerHeight;

        if (!isVisible) {
          // Remove any associated card
          const cardObj = this.cardHistory.find((c) => c.source === edgeItem);
          if (cardObj) {
            this.fadeOutAndRemove(cardObj.el);
            this.cardHistory = this.cardHistory.filter(
              (c) => c.el !== cardObj.el
            );
            this.updateCardIndexes();
          }

          // Clear hover timer if any
          if (this.hoverTimers.has(edgeItem)) {
            const existing = this.hoverTimers.get(edgeItem);
            clearTimeout(existing.timeoutId);
            this.hoverTimers.delete(edgeItem);
          }

          // Deactivate if necessary
          edgeItem.classList.remove("active");
        }
      });

      
    });
  }
}

class ChurchAwsomenessScrollers {
  constructor({analytics = null}) {
    this.analytics = analytics || new GA4Analytics(ga_id);
    this.init();
  }

  init() {
    this.tracks = document.querySelectorAll(".church-awesomeness__track");
    
    window.addEventListener("churchAwesomenessTrack", (e) => {
      e.preventDefault();
      //console.log("AWESOMENESS WILL PREVAIL: ", e.detail);

      const el = e.detail.target;
      const progress = e.detail.progress;

      const maxScroll = el.scrollWidth - el.clientWidth;
      const scrollLeft = maxScroll * progress;

      el.scrollLeft = scrollLeft;
    });
  }
}

class GradientPicker {
  constructor() {
    this.el = document.querySelector(".gradient-picker");
    this.buttons = this.el.querySelectorAll("button");
    this.stories = document.querySelector("#stories");
    this.body = document.querySelector("body");
    this.overlay = this.stories.querySelector(".gradient-overlay");

    this.currentGradient = 1; // track the current gradient class

    this.analytics = new GA4Analytics(ga_id);

    this.init();
  }

  init() {
    this.addListeners();
  }

  addListeners() {
    this.buttons.forEach((button) => {
      button.addEventListener("click", () => {
        this.buttons.forEach((btn) => {
          btn.classList.remove("active");
        });

        button.classList.add("active");

        const newGradient = button.dataset.gradient;

        this.analytics.sendEvent("gradient_picked", { gradient: newGradient });

        // Remove the current gradient
        if (this.currentGradient) {
          this.fadeOutAndRemoveGradient();
        }

        this.addNewGradient(newGradient);
      });
    });
  }

  addNewGradient(newGradient) {
    // Create an overlay to transition out the old background
    const gradientOverlay = document.createElement("div");
    gradientOverlay.classList.add(
      `gradient-overlay`,
      `bg-gradient-${newGradient}`
    );

    // Apply the current gradient to the overlay
    this.stories.appendChild(gradientOverlay);
    setTimeout(() => gradientOverlay.classList.add("fade-in"), 10);

    // Add the new gradient class to #stories (this is handled with the body class)
    //this.stories.classList.add(`bg-gradient-${newGradient}`);
    //console.log("New gradient added!", this.currentGradient, newGradient);
    this.currentGradient = newGradient;

    this.body.classList.remove(
      "active:gradient-1",
      "active:gradient-2",
      "active:gradient-3",
      "active:gradient-4",
      "active:gradient-5",
      "active:gradient-6",
      "active:gradient-7"
    );
    this.body.classList.add(`active:gradient-${newGradient}`);
  }

  fadeOutAndRemoveGradient() {
    const gradientOverlay = document.querySelector(".gradient-overlay");

    if (gradientOverlay) {
      //console.log("REMOVE end!", gradientOverlay);

      gradientOverlay.classList.add("fade-out");

      setTimeout(() => {
        gradientOverlay.remove();
      }, 300);

    }
  }
}

let sectionTopDone = false;

class StoriesScroller {
  constructor() {
    this.init();

    this.section = document.querySelector("#stories");
    this.churchNames = this.section.querySelector(".church-names");
  }

  init() {
    window.addEventListener("storiesSectionScroll", (e) => {
      e.preventDefault();

      this.updateChurchNamesV2(e.detail.progress);
      //this.updateChurchNames(e.detail.progress);
    })
  }

  updateChurchNamesV2(progress) {
    const churchNames = this.churchNames;
    if(!churchNames) {
      return;
    }

    const churchNamesInner = churchNames.querySelector(".church-names__inner");

    if(!churchNamesInner) {
      return;
    }
    const totalWidth = churchNames.offsetWidth;

    const totalTravelDistance = totalWidth * 0.25;
    const travelDistance = totalTravelDistance * progress;

    churchNamesInner.style.transform = `translateX(-${travelDistance}px)`;

  }

  updateChurchNames(progress) {
    const churchNames = this.churchNames;
    const churchNamesHeight = churchNames.offsetHeight;
    const churchNamesWidth = churchNames.offsetWidth;

    const churchNamesInner = churchNames.querySelector(".church-names__inner");
    const churchNamesInnerWidth = churchNamesWidth * 1.1; // target distance
    const churchNamesInnerHeight= churchNamesHeight * 1.1;

    const innerTravel = (churchNamesWidth - churchNamesInnerWidth) * progress;
    const innerVerticalTravel = (churchNamesHeight - churchNamesInnerHeight) * progress;
    
    const sectionRec = this.section.getBoundingClientRect();

    const sectionTop = sectionRec.top;
    const sectionBottom = sectionRec.bottom;

    const windowHeight = window.innerHeight;
    
    // START - -1784px
    // END - 3515px

    let translateY = 0;

    if (sectionTop < 0 && !sectionTopDone) {
      //console.log("Section TOP!!!", sectionTopDone);

      this.section.classList.add("at-top");
    }

    if ((sectionTop < (windowHeight * -0.4)) && !sectionTopDone ) {
      //console.log("Section TOP IS NOW DONE!!!", sectionTopDone);
      sectionTopDone = true;
    }

    //console.log(sectionTop, windowHeight, (windowHeight * -0.4) );
    if ( (sectionTop > windowHeight * -0.4) && sectionTopDone) {
      //console.log("Section BOTTOM!!!", sectionTopDone);
      this.section.classList.remove("at-top");
    }

    if (sectionTop > 0 && sectionTopDone) {
      //console.log("Section BOTTOM IS NOW DONE!!!", sectionTopDone);
      sectionTopDone = false;
    }

    if (sectionBottom > windowHeight && sectionTop < 0) {
      // Only move it within the viewporrtHeight
      translateY = -sectionTop + windowHeight - churchNamesHeight;
      //const translateY = -sectionTop;

      churchNames.style.transform = `translateY(${translateY}px)`;

      churchNamesInner.style.transform = `translateX(${innerTravel}px) translateY(${innerVerticalTravel}px)`;
    } else if (sectionBottom < windowHeight && sectionTop < 0) {
      //console.log("Section Rec2");
      translateY = -sectionTop + windowHeight - churchNamesHeight;
      //translateY = translateY - (windowHeight - sectionBottom);

      churchNames.style.transform = `translateY(${translateY}px)`;
    }

  }
}

class StoriesModals {
  constructor() {
    this.section = document.querySelector("section#stories");
    this.stories = this.section.querySelectorAll(".alpha-story");
    this.analytics = new GA4Analytics(ga_id);
    this.init();
  }

  init() {
    //console.log("Stories Modals is ready!", this.section, this.stories);
    this.setupModal();

    this.stories.forEach((story) => {
      story.addEventListener("click", () => {
        this.handleClick(story);
      });
    });
  }

  handleClick(story) {
    this.openModal(story);
  }

  openModal(story) {
    const contentEl = this.modal.querySelector(".story-popup__body");
    const { videoId } = story.dataset;

    if(!videoId) return;

    setTimeout(() => {
      this.modal.classList.add("active");
    }, 10);


     contentEl.innerHTML = `
          <div class="story-popup__content">
            <iframe class='video youtube' id='video' width='100%' height='' src='https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&color=009999&autoplay=1' title='YouTube video player' frameborder='0' allow='autoplay; accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
          </div>
        `;
      
     this.modal.showModal();
     this.modal.dataset.videoName = story.dataset.videoName;
     this.modal.dataset.videoChurch = story.dataset.videoChurch;

     Alpha1Million.viewedVideos++;

     this.analytics.sendEvent("story_clicked", {
       church_story: story.dataset.videoName,
       church_name: story.dataset.videoChurch,
       viewed_videos: Alpha1Million.viewedVideos
     });

     if (window.locomotiveScroll) {
       //console.log("Stopping locomotive scroll");
       window.locomotiveScroll.stop();
     }
  }

  setupModal() {
    // Basic modal structure
    this.modal = document.createElement("dialog");
    this.modal.classList.add("story-popup");
    this.modal.innerHTML = `
      <button class="story-popup__close">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
          <rect width="32" height="32" rx="16" fill="black"/>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M16 17.5556L22.2222 23.7778L23.7778 22.2222L17.5556 16L23.7778 9.77778L22.2222 8.22223L16 14.4444L9.77778 8.22222L8.22222 9.77778L14.4444 16L8.22222 22.2222L9.77778 23.7778L16 17.5556Z" fill="white"/>
        </svg>
      </button>
      <div class="story-popup__body" data-lenis-prevent></div>
    `;
    document.body.appendChild(this.modal);

    this.modal.classList.remove("active");

    setTimeout(() => {
      this.modal
        .querySelector(".story-popup__close")
        .addEventListener("click", () => { 
          this.closeModal();
          this.modal.classList.remove("active");
        });
    }, 10);

    // Escape key support
    this._handleEscape = (e) => {
      this.modal.classList.remove("active");
      if (e.key === "Escape") {
        this.closeModal();
      }
    };
    document.addEventListener("keydown", this._handleEscape);
  }

  closeModal() {
    this.modal.classList.remove("active");

    setTimeout(() => {
      this.modal.close();
      const contentEl = this.modal.querySelector(".story-popup__body");
      contentEl.innerHTML = '';
    }, 250);

    this.analytics.sendEvent("story_closed", {
      church_story: this.modal.dataset.videoName,
      church_name: this.modal.dataset.videoChurch,
    });

    if (window.locomotiveScroll) {
      //console.log("Start the scroll again");
      window.locomotiveScroll.start();
    }
  }
}
/**
 * Marquee Class
 * Rebuilt and better than ever.
 */
class Marquee {
  constructor(selector) {
    this.selector = selector;
    this.marquees = document.querySelectorAll(this.selector);
    this.init();
    this.bindResizeEvent();
  }

  // Initialize all marquees
  init() {
    this.marquees.forEach((marquee) => {
      this.setupMarquee(marquee);
    });
  }

  // Setup an individual marquee
  setupMarquee(marquee) {
    const items = marquee.querySelectorAll(".marquee__item");
    const groups = marquee.querySelectorAll(".marquee__group");

    // Set CSS properties
    marquee.style.setProperty("--_marquee-viewport-width", `${marquee.offsetWidth}`);
    marquee.style.setProperty("--_marquee-item-count", `${items.length}`); // For potential use elsewhere

    let innerWidth = 0;
    let innerChild = null;

    groups.forEach((group) => {
      innerWidth = group.offsetWidth;
      innerChild = group;
      marquee.style.setProperty("--_marquee-content-width", `${group.offsetWidth}px`);
    });

    if (innerChild) {
      this.fillParentWithClones(marquee, innerChild);

      // Calculate and set the speed modifier
      const speedModifier = Math.round((marquee.offsetWidth / innerWidth) * 100) / 100;
      marquee.style.setProperty("--_marquee-speed-modifier", `${speedModifier}`);
    }
  }

  // Clone child elements to fill the parent
  fillParentWithClones(parent, child) {
    if (!parent || !child) {
      console.warn("Parent or child element not found!");
      return;
    }

    const parentWidth = parent.offsetWidth;
    const childWidth = child.offsetWidth;
    const targetWidth =
      childWidth > parentWidth ? childWidth * 2 : parentWidth * 2;

    let totalWidth = childWidth;

    // Clone until the desired width is achieved
    while (totalWidth < targetWidth) {
      const clone = child.cloneNode(true);
      parent.appendChild(clone);
      totalWidth += childWidth;
    }
  }

  // Reinitialize marquees on window resize
  reinitialize() {
    console.log("Reinitializing marquees...");
    this.init();
  }

  // Bind resize event to reinitialize marquees
  bindResizeEvent() {
    window.addEventListener("resize", () => this.reinitialize());
  }
}

class CursorFollower {
  constructor(followerEl, speed = 0.1) {
    this.follower = followerEl;
    this.speed = speed;
    this.mouseX = 0;
    this.mouseY = 0;
    this.currentX = 0;
    this.currentY = 0;
    this.enabled = false;

    this._onMouseMove = this._onMouseMove.bind(this);
    this._animate = this._animate.bind(this);

    if (!isTouchMobile) {
      requestAnimationFrame(this._animate);
    }
  }

  _onMouseMove(e) {
    if (!this.enabled) return;
    this.mouseX = e.clientX;
    this.mouseY = e.clientY;
  }

  _animate() {
    if (this.enabled) {
      this.currentX += (this.mouseX - this.currentX) * this.speed;
      this.currentY += (this.mouseY - this.currentY) * this.speed;

      const halfWidth = window.innerWidth / 2;
      const mouseXPercent = (this.mouseX - halfWidth) / halfWidth;

      const rotate = 21.5 * mouseXPercent;

      this.follower.style.left = `${this.currentX}px`;
      this.follower.style.top = `${this.currentY}px`;
      this.follower.style.rotate = `${rotate}deg`;
    } 

    requestAnimationFrame(this._animate);
  }

  enable() {
    if (!this.enabled) {
      this.enabled = true;
      document.addEventListener("mousemove", this._onMouseMove);
    }
  }

  disable() {
    if (this.enabled) {
      this.enabled = false;
      document.removeEventListener("mousemove", this._onMouseMove);
    }
  }
}


class LazyMediaLoader {
  constructor() {
    document.addEventListener("DOMContentLoaded", () => {
      this.initLazyImages();
      this.initLazyVideos();
    });
  }

  initLazyImages() {
    const lazyImages = document.querySelectorAll("img.lazy");

    if ("IntersectionObserver" in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const img = entry.target;
            this.loadImage(img);
            observer.unobserve(img);
          }
        });
      });

      lazyImages.forEach((img) => imageObserver.observe(img));
    } else {
      // Fallback for browsers without IntersectionObserver
      lazyImages.forEach((img) => this.loadImage(img));
    }
  }

  loadImage(img) {
    img.src = img.dataset.src;
    if (img.dataset.srcset) {
      img.srcset = img.dataset.srcset;
    }

    img.onload = () => {
      img.classList.add("loaded");
    };

    img.onerror = () => {
      console.warn("Image failed to load:", img.dataset.src);
    };
  }

  initLazyVideos() {
    const videos = document.querySelectorAll("video");

    if (!("IntersectionObserver" in window)) return;

    const videoObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;
          if (entry.isIntersecting) {
            video.play().catch((err) => {
              console.warn("Could not autoplay:", err);
            });
          } else {
            video.pause();
          }
        });
      },
      { threshold: 0.4 }
    );

    videos.forEach((video) => {
      videoObserver.observe(video);

      const onLoaded = () => {
        video.classList.add("loaded");
        video.removeEventListener("loadeddata", onLoaded);
      };

      video.addEventListener("loadeddata", onLoaded);
    });
  }
}

// Activate lazy loading
new LazyMediaLoader();

let mouseX = 0, mouseY = 0;
let currentX = 0, currentY = 0;
const speed = 0.15; // Lower = slower, 0.1–0.2 is good range

let churchesActive = false;
let storiesActive = false;


document.addEventListener("DOMContentLoaded", () => {

  const followerEl = document.getElementById("cursor-follower");
  const follower = new CursorFollower(followerEl, speed);
  
  const cards = new ChurchAwesomenessCards({
    itemSelector: ".church-awesomeness__item",
    containerSelector: ".church-awesomeness__cards",
    templateSelector: "#church-awesomeness__card",
    analytics : analytics
  });

  const churchScrollers = new ChurchAwsomenessScrollers({
    analytics: analytics,
  });

  const gradientPicker = new GradientPicker({ analytics: analytics });

  const storiesSection = new StoriesScroller({ analytics: analytics });

  const storiesModals = new StoriesModals({ analytics: analytics });

  const marquee = new Marquee(".marquee");

  window.addEventListener("sectionScroll", (e) => {
    e.preventDefault();

    const { way, from, target } = e.detail;
    const order = target.dataset.sectionOrder;

    if(target.id === 'churches' && way === 'enter') {
      churchesActive = true;
    } 
    if(target.id === 'churches' && way === 'leave') { 
      churchesActive = false;
    }
    if(target.id === 'stories' && way === 'enter') {
      storiesActive = true;
    }
    if(target.id === 'stories' && way === 'leave') {
      storiesActive = false;
    }

    if(churchesActive || storiesActive) {
      follower.enable();
    } else {
      follower.disable();
    }

    //onsole.log("Section Scroll!", way, target.id);

    analytics.sendEvent("section_scroll", {
      scroll_way : way,
      scroll_from : from,
      scroll_section_id : target.id,
      scroll_section_order : order
    });
  });


  Alpha1Million.cards = cards;
  Alpha1Million.churchScrollers = churchScrollers;
  Alpha1Million.gradientPicker = gradientPicker;
  Alpha1Million.storiesSection = storiesSection;
  Alpha1Million.storiesModals = storiesModals;
  Alpha1Million.follower = follower;
  Alpha1Million.marquee = marquee;

});


