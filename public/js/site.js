document.addEventListener("DOMContentLoaded", () => {
    const counters = Array.from(document.querySelectorAll("[data-countup]"));
    const header = document.querySelector("[data-header]");
    const mapElement = document.querySelector("[data-map]");
    const menuToggle = document.querySelector("[data-menu-toggle]");
    const siteNav = document.querySelector("[data-site-nav]");
    const menuBackdrop = document.querySelector("[data-menu-backdrop]");
    const newsSidebar = document.querySelector(".site-news-sidebar");
    const newsBackdrop = document.querySelector("[data-news-backdrop]");
    const mailtoLinks = Array.from(document.querySelectorAll('a[href^="mailto:"]'));
    const navLinks = Array.from(document.querySelectorAll("[data-nav-link]"));
    const navDropdowns = Array.from(document.querySelectorAll("[data-nav-dropdown]"));
    const pointCards = Array.from(document.querySelectorAll("[data-map-point]"));
    const pointDataElement = document.querySelector("[data-map-points]");
    const externalMapLink = document.querySelector("[data-map-external]");
    const backgroundSlides = Array.from(document.querySelectorAll("[data-bg-slide]"));
    const backgroundTargets = Array.from(document.querySelectorAll("[data-bg-target]"));
    const brochureViewers = Array.from(document.querySelectorAll("[data-brochure-viewer]"));
    const galleryGroupToggles = Array.from(document.querySelectorAll("[data-gallery-group-toggle]"));
    const galleryExpandToggles = Array.from(document.querySelectorAll("[data-gallery-expand-toggle]"));
    const lightboxItems = Array.from(document.querySelectorAll("[data-lightbox-item]"))
        .filter((item) => (item.dataset.lightboxSrc || "").trim() !== "");
    const lightbox = document.querySelector("[data-lightbox]");
    const sections = Array.from(document.querySelectorAll("[data-section]"));
    let navDropdownCloseTimer = null;
    let mailtoNoticeTimer = null;
    let mailtoNotice = null;

    const cancelScheduledNavDropdownClose = () => {
        if (!navDropdownCloseTimer) {
            return;
        }

        window.clearTimeout(navDropdownCloseTimer);
        navDropdownCloseTimer = null;
    };

    const scheduleNavDropdownClose = () => {
        cancelScheduledNavDropdownClose();

        navDropdownCloseTimer = window.setTimeout(() => {
            closeNavDropdowns();
        }, 180);
    };

    const closeNavDropdowns = (activeDropdown = null) => {
        cancelScheduledNavDropdownClose();

        navDropdowns.forEach((dropdown) => {
            const toggle = dropdown.querySelector("[data-nav-dropdown-toggle]");
            const menu = dropdown.querySelector("[data-nav-dropdown-menu]");
            const isOpen = dropdown === activeDropdown;

            dropdown.classList.toggle("is-open", isOpen);

            if (toggle) {
                toggle.setAttribute("aria-expanded", String(isOpen));
            }

            if (menu) {
                menu.hidden = !isOpen;
            }
        });
    };

    const extractEmailAddress = (href) => {
        if (!href.toLowerCase().startsWith("mailto:")) {
            return "";
        }

        const rawAddress = href.slice("mailto:".length).split("?")[0] || "";

        try {
            return decodeURIComponent(rawAddress).trim();
        } catch (error) {
            return rawAddress.trim();
        }
    };

    const fallbackCopyText = (text) => {
        const helper = document.createElement("textarea");

        helper.value = text;
        helper.setAttribute("readonly", "readonly");
        helper.setAttribute("aria-hidden", "true");
        helper.style.position = "fixed";
        helper.style.opacity = "0";
        helper.style.pointerEvents = "none";

        document.body.appendChild(helper);
        helper.focus();
        helper.select();
        helper.setSelectionRange(0, helper.value.length);

        let copied = false;

        try {
            copied = document.execCommand("copy");
        } finally {
            document.body.removeChild(helper);
        }

        if (!copied) {
            throw new Error("Clipboard copy fallback failed.");
        }
    };

    const copyTextToClipboard = async (text) => {
        if (navigator.clipboard?.writeText) {
            try {
                await navigator.clipboard.writeText(text);
                return;
            } catch (error) {
                fallbackCopyText(text);
                return;
            }
        }

        fallbackCopyText(text);
    };

    const scrollElementIntoViewWithHeaderOffset = (element) => {
        if (!(element instanceof HTMLElement)) {
            return;
        }

        const headerHeight = header instanceof HTMLElement ? header.offsetHeight : 0;
        const top = element.getBoundingClientRect().top + window.scrollY - headerHeight - 24;

        window.scrollTo({
            top: Math.max(0, top),
            behavior: "smooth",
        });
    };

    const getMailtoNotice = () => {
        if (mailtoNotice instanceof HTMLElement) {
            return mailtoNotice;
        }

        mailtoNotice = document.createElement("div");
        mailtoNotice.className = "clipboard-toast";
        mailtoNotice.hidden = true;
        mailtoNotice.setAttribute("role", "status");
        mailtoNotice.setAttribute("aria-live", "polite");
        document.body.appendChild(mailtoNotice);

        return mailtoNotice;
    };

    const showMailtoNotice = (message, isError = false) => {
        const notice = getMailtoNotice();

        window.clearTimeout(mailtoNoticeTimer);
        notice.textContent = message;
        notice.hidden = false;
        notice.classList.toggle("is-error", isError);

        window.requestAnimationFrame(() => {
            notice.classList.add("is-visible");
        });

        mailtoNoticeTimer = window.setTimeout(() => {
            notice.classList.remove("is-visible");

            window.setTimeout(() => {
                notice.hidden = true;
            }, 180);
        }, 2200);
    };

    const formatCount = (value, decimals) =>
        new Intl.NumberFormat("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(value);

    const animateCounter = (element) => {
        const target = Number(element.dataset.countup || "0");
        const decimals = Number(element.dataset.decimals || "0");
        const suffix = element.dataset.suffix || "";
        const duration = 1400;
        const startTime = performance.now();

        const tick = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = target * eased;

            element.textContent = `${formatCount(value, decimals)}${suffix}`;

            if (progress < 1) {
                window.requestAnimationFrame(tick);
            } else {
                element.textContent = `${formatCount(target, decimals)}${suffix}`;
            }
        };

        window.requestAnimationFrame(tick);
    };

    const counterObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                animateCounter(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.45 },
    );

    counters.forEach((counter) => counterObserver.observe(counter));

    if (header) {
        const syncHeaderState = () => {
            header.classList.toggle("is-scrolled", window.scrollY > 18);
        };

        syncHeaderState();
        window.addEventListener("scroll", syncHeaderState, { passive: true });
    }

    if (mailtoLinks.length) {
        mailtoLinks.forEach((link) => {
            link.addEventListener("click", async (event) => {
                if (event.defaultPrevented || event.button !== 0) {
                    return;
                }

                const href = link.getAttribute("href") || "";
                const emailAddress = extractEmailAddress(href);

                if (!emailAddress) {
                    return;
                }

                event.preventDefault();

                try {
                    await copyTextToClipboard(emailAddress);
                    showMailtoNotice(`Email copied: ${emailAddress}`);
                } catch (error) {
                    console.error("Unable to copy email address.", error);
                    showMailtoNotice("Email app opening. Copy the address manually if needed.", true);
                } finally {
                    window.setTimeout(() => {
                        window.location.href = href;
                    }, 40);
                }
            });
        });
    }

    if (navDropdowns.length) {
        const isDesktopDropdownMode = () =>
            window.innerWidth > 960
            && window.matchMedia("(hover: hover) and (pointer: fine)").matches;

        const closeDropdownIfFocusLeaves = (dropdown) => {
            window.setTimeout(() => {
                const activeElement = document.activeElement;

                if (!(activeElement instanceof HTMLElement) || !dropdown.contains(activeElement)) {
                    closeNavDropdowns();
                }
            }, 0);
        };

        closeNavDropdowns();

        navDropdowns.forEach((dropdown) => {
            const dropdownLinks = Array.from(dropdown.querySelectorAll("[data-nav-link]"));
            const toggle = dropdown.querySelector("[data-nav-dropdown-toggle]");

            if (!toggle) {
                return;
            }

            toggle.addEventListener("click", (event) => {
                event.stopPropagation();

                const isOpen = dropdown.classList.contains("is-open");
                closeNavDropdowns(isOpen ? null : dropdown);
            });

            dropdown.addEventListener("mouseenter", () => {
                if (!isDesktopDropdownMode()) {
                    return;
                }

                cancelScheduledNavDropdownClose();
                closeNavDropdowns(dropdown);
            });

            dropdown.addEventListener("mouseleave", () => {
                if (!isDesktopDropdownMode()) {
                    return;
                }

                scheduleNavDropdownClose();
            });

            dropdown.addEventListener("focusin", () => {
                if (!isDesktopDropdownMode()) {
                    return;
                }

                cancelScheduledNavDropdownClose();
                closeNavDropdowns(dropdown);
            });

            dropdown.addEventListener("focusout", () => {
                closeDropdownIfFocusLeaves(dropdown);
            });

            dropdownLinks.forEach((link) => {
                link.addEventListener("click", () => {
                    closeNavDropdowns();
                });
            });
        });

        document.addEventListener("click", () => {
            closeNavDropdowns();
        });

        window.addEventListener("resize", () => {
            closeNavDropdowns();
        });
    }

    if (menuToggle && navLinks.length) {
        const isMobileMenuViewport = () => window.innerWidth <= 960;

        const syncMenuState = (isOpen) => {
            const shouldOpen = isMobileMenuViewport() && isOpen;

            document.body.classList.toggle("menu-open", shouldOpen);
            menuToggle.setAttribute("aria-expanded", String(shouldOpen));
            menuToggle.setAttribute("aria-label", shouldOpen ? "Close menu" : "Open menu");

            if (siteNav instanceof HTMLElement) {
                if (isMobileMenuViewport()) {
                    siteNav.setAttribute("aria-hidden", String(!shouldOpen));

                    if ("inert" in siteNav) {
                        siteNav.inert = !shouldOpen;
                    }
                } else {
                    siteNav.removeAttribute("aria-hidden");

                    if ("inert" in siteNav) {
                        siteNav.inert = false;
                    }
                }
            }

            if (menuBackdrop instanceof HTMLElement) {
                menuBackdrop.setAttribute("aria-hidden", String(!shouldOpen));
            }
        };

        const closeMenu = () => {
            syncMenuState(false);
            closeNavDropdowns();
        };

        menuToggle.addEventListener("click", () => {
            const isExpanded = menuToggle.getAttribute("aria-expanded") === "true";

            syncMenuState(!isExpanded);
        });

        syncMenuState(false);

        navLinks.forEach((link) => {
            link.addEventListener("click", closeMenu);
        });

        if (menuBackdrop instanceof HTMLElement) {
            menuBackdrop.addEventListener("click", closeMenu);
        }

        window.addEventListener("resize", () => {
            if (window.innerWidth > 960) {
                closeMenu();
                return;
            }

            syncMenuState(document.body.classList.contains("menu-open"));
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                closeMenu();
            }
        });
    }

    if (newsSidebar instanceof HTMLDetailsElement) {
        const closeNewsSidebar = () => {
            newsSidebar.open = false;
            newsSidebar.querySelector("summary")?.focus();
        };

        newsBackdrop?.addEventListener("click", closeNewsSidebar);

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && newsSidebar.open) {
                closeNewsSidebar();
            }
        });
    }

    if (sections.length && navLinks.length) {
        const visibleSections = new Map();

        const setCurrentNavLink = (sectionId) => {
            navLinks.forEach((link) => {
                const href = link.getAttribute("href") || "";
                const hashIndex = href.indexOf("#");
                const hash = hashIndex >= 0 ? href.slice(hashIndex) : "";
                const isCurrent = sectionId && hash === `#${sectionId}`;

                if (isCurrent) {
                    link.setAttribute("data-current", "true");
                } else {
                    link.removeAttribute("data-current");
                }
            });
        };

        const syncCurrentSection = () => {
            let activeSection = null;
            let highestRatio = 0;

            sections.forEach((section) => {
                const ratio = visibleSections.get(section) || 0;

                if (ratio > highestRatio) {
                    activeSection = section;
                    highestRatio = ratio;
                }
            });

            if (!activeSection) {
                activeSection =
                    sections.find((section) => {
                        const offsetTop = section.offsetTop - 180;
                        const offsetBottom = offsetTop + section.offsetHeight;

                        return window.scrollY >= offsetTop && window.scrollY < offsetBottom;
                    }) || null;
            }

            const sectionId = activeSection ? activeSection.dataset.navId || activeSection.id : "";
            setCurrentNavLink(sectionId);
        };

        const sectionObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    visibleSections.set(entry.target, entry.isIntersecting ? entry.intersectionRatio : 0);
                });

                syncCurrentSection();
            },
            {
                rootMargin: "-30% 0px -44% 0px",
                threshold: [0.15, 0.35, 0.55, 0.75],
            },
        );

        sections.forEach((section) => sectionObserver.observe(section));
        syncCurrentSection();
    }

    if (brochureViewers.length) {
        brochureViewers.forEach((viewer) => {
            const slides = Array.from(viewer.querySelectorAll("[data-brochure-slide]"));
            const previousButton = viewer.querySelector("[data-brochure-prev]");
            const nextButton = viewer.querySelector("[data-brochure-next]");
            const status = viewer.querySelector("[data-brochure-status]");
            let activeIndex = Math.max(0, slides.findIndex((slide) => !slide.hidden));

            if (!slides.length) {
                return;
            }

            const renderBrochureSlide = () => {
                slides.forEach((slide, index) => {
                    const isActive = index === activeIndex;

                    slide.hidden = !isActive;
                    slide.classList.toggle("is-active", isActive);
                    slide.setAttribute("aria-hidden", String(!isActive));
                    slide.tabIndex = isActive ? 0 : -1;
                });

                if (previousButton instanceof HTMLButtonElement) {
                    previousButton.disabled = activeIndex === 0;
                }

                if (nextButton instanceof HTMLButtonElement) {
                    nextButton.disabled = activeIndex === slides.length - 1;
                }

                if (status instanceof HTMLElement) {
                    status.textContent = `${activeIndex + 1} / ${slides.length}`;
                }
            };

            const stepBrochureSlide = (direction) => {
                const nextIndex = activeIndex + direction;

                if (nextIndex < 0 || nextIndex >= slides.length) {
                    return;
                }

                activeIndex = nextIndex;
                renderBrochureSlide();
            };

            previousButton?.addEventListener("click", () => {
                stepBrochureSlide(-1);
            });

            nextButton?.addEventListener("click", () => {
                stepBrochureSlide(1);
            });

            viewer.addEventListener("keydown", (event) => {
                if (event.key === "ArrowLeft") {
                    event.preventDefault();
                    stepBrochureSlide(-1);
                }

                if (event.key === "ArrowRight") {
                    event.preventDefault();
                    stepBrochureSlide(1);
                }
            });

            renderBrochureSlide();
        });
    }

    if (lightbox && lightboxItems.length) {
        const lightboxImage = lightbox.querySelector("[data-lightbox-image]");
        const lightboxMeta = lightbox.querySelector("[data-lightbox-meta]");
        const lightboxTitle = lightbox.querySelector("[data-lightbox-title]");
        const lightboxDescription = lightbox.querySelector("[data-lightbox-description]");
        const lightboxCreditWrap = lightbox.querySelector("[data-lightbox-credit-wrap]");
        const lightboxCredit = lightbox.querySelector("[data-lightbox-credit]");
        const lightboxFigure = lightbox.querySelector(".lightbox-figure");
        const lightboxCaption = lightbox.querySelector(".lightbox-caption");
        const lightboxStage = lightbox.querySelector(".lightbox-stage");
        const lightboxControls = lightbox.querySelector(".lightbox-controls");
        const previousButton = lightbox.querySelector("[data-lightbox-prev]");
        const nextButton = lightbox.querySelector("[data-lightbox-next]");
        const closeButtons = Array.from(lightbox.querySelectorAll("[data-lightbox-close]"));
        const lightboxItemGroups = new Map();
        let currentIndex = 0;
        let activeLightboxItems = lightboxItems;
        let lastFocusedElement = null;

        const syncLightboxFrameSizing = () => {
            if (
                !(lightboxImage instanceof HTMLImageElement)
                || !(lightboxFigure instanceof HTMLElement)
                || !(lightboxStage instanceof HTMLElement)
                || !lightboxImage.naturalWidth
                || !lightboxImage.naturalHeight
            ) {
                return;
            }

            const viewportPadding = window.innerWidth <= 820 ? 28 : 48;
            const maxFrameWidth = Math.max(280, window.innerWidth - viewportPadding);
            const modalMaxHeight = Math.max(320, window.innerHeight - viewportPadding);
            const staticControlsHeight = window.innerWidth <= 820
                && lightboxControls instanceof HTMLElement
                && !lightboxControls.hidden
                ? lightboxControls.offsetHeight + 14
                : 0;
            const figureMaxHeight = Math.max(260, modalMaxHeight - staticControlsHeight);
            const captionHeight = lightboxCaption instanceof HTMLElement
                ? lightboxCaption.offsetHeight
                : 0;
            const imageMaxHeight = Math.max(180, figureMaxHeight - captionHeight);
            const imageRatio = lightboxImage.naturalWidth / lightboxImage.naturalHeight;
            const frameWidth = Math.min(
                maxFrameWidth,
                Math.max(280, Math.round(imageMaxHeight * imageRatio)),
            );

            lightboxStage.style.setProperty("--lightbox-frame-width", `${frameWidth}px`);
            lightboxStage.style.setProperty("--lightbox-max-height", `${figureMaxHeight}px`);
            lightboxStage.style.setProperty("--lightbox-image-max-height", `${imageMaxHeight}px`);
        };

        lightboxItems.forEach((item) => {
            const groupKey = item.dataset.lightboxGroup || "__all__";
            const groupItems = lightboxItemGroups.get(groupKey) || [];

            groupItems.push(item);
            lightboxItemGroups.set(groupKey, groupItems);
        });

        const renderLightboxItem = (index) => {
            const item = activeLightboxItems[index];

            if (!item || !lightboxImage) {
                return;
            }
            const shouldHideCaption = item.dataset.lightboxHideCaption === "true";

            lightboxImage.src = item.dataset.lightboxSrc || "";
            lightboxImage.alt = item.dataset.lightboxAlt || item.dataset.lightboxTitle || "";

            if (lightboxStage instanceof HTMLElement) {
                lightboxStage.style.removeProperty("--lightbox-frame-width");
                lightboxStage.style.removeProperty("--lightbox-max-height");
                lightboxStage.style.removeProperty("--lightbox-image-max-height");
            }

            if (lightboxCaption instanceof HTMLElement) {
                lightboxCaption.hidden = shouldHideCaption;
            }

            if (lightboxTitle) {
                if (Object.hasOwn(item.dataset, "lightboxTitleHtml")) {
                    lightboxTitle.innerHTML = item.dataset.lightboxTitleHtml || "";
                } else {
                    lightboxTitle.textContent = item.dataset.lightboxTitle || "";
                }
            }

            if (lightboxDescription) {
                if (Object.hasOwn(item.dataset, "lightboxDescriptionHtml")) {
                    lightboxDescription.innerHTML = item.dataset.lightboxDescriptionHtml || "";
                } else {
                    lightboxDescription.textContent = item.dataset.lightboxDescription || "";
                }
            }

            if (lightboxMeta) {
                lightboxMeta.textContent = item.dataset.lightboxMeta || "Gallery image";
            }

            if (lightboxCreditWrap && lightboxCredit) {
                const hasCreditSlot = Object.hasOwn(item.dataset, "lightboxCredit");
                const creditText = item.dataset.lightboxCredit || "";

                lightboxCreditWrap.hidden = !hasCreditSlot;
                lightboxCredit.textContent = creditText || " ";
                lightboxCredit.classList.toggle("is-empty", !creditText.trim());
            }

            currentIndex = index;

            if (lightboxImage.complete) {
                syncLightboxFrameSizing();
            }
        };

        const syncLightboxNavigation = () => {
            const hasMultipleItems = activeLightboxItems.length > 1;

            if (lightboxControls instanceof HTMLElement) {
                lightboxControls.hidden = !hasMultipleItems;
            }

            if (previousButton instanceof HTMLButtonElement) {
                previousButton.disabled = !hasMultipleItems;
            }

            if (nextButton instanceof HTMLButtonElement) {
                nextButton.disabled = !hasMultipleItems;
            }
        };

        const openLightbox = (item) => {
            const groupKey = item.dataset.lightboxGroup || "__all__";
            const groupItems = lightboxItemGroups.get(groupKey) || [item];
            const itemIndex = groupItems.indexOf(item);

            lastFocusedElement = document.activeElement;
            activeLightboxItems = groupItems;
            syncLightboxNavigation();
            lightbox.hidden = false;
            lightbox.classList.add("is-open");
            document.body.classList.add("lightbox-open");
            renderLightboxItem(itemIndex >= 0 ? itemIndex : 0);
        };

        const closeLightbox = () => {
            lightbox.classList.remove("is-open");
            lightbox.hidden = true;
            document.body.classList.remove("lightbox-open");

            if (lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus();
            }
        };

        const moveLightbox = (direction) => {
            const nextIndex = (currentIndex + direction + activeLightboxItems.length) % activeLightboxItems.length;
            renderLightboxItem(nextIndex);
        };

        lightboxItems.forEach((item) => {
            item.addEventListener("click", () => {
                openLightbox(item);
            });

            item.addEventListener("keydown", (event) => {
                if (event.key === "Enter" || event.key === " ") {
                    event.preventDefault();
                    openLightbox(item);
                }
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener("click", closeLightbox);
        });

        lightboxImage?.addEventListener("load", syncLightboxFrameSizing);

        previousButton?.addEventListener("click", () => moveLightbox(-1));
        nextButton?.addEventListener("click", () => moveLightbox(1));
        window.addEventListener("resize", () => {
            if (!lightbox.hidden) {
                syncLightboxFrameSizing();
            }
        });

        document.addEventListener("keydown", (event) => {
            if (lightbox.hidden) {
                return;
            }

            if (event.key === "Escape") {
                closeLightbox();
            }

            if (event.key === "ArrowLeft") {
                moveLightbox(-1);
            }

            if (event.key === "ArrowRight") {
                moveLightbox(1);
            }
        });
    }

    if (galleryGroupToggles.length) {
        const setGalleryGroupState = (groupId, isOpen) => {
            const toggle = document.querySelector(`[data-gallery-group-toggle="${groupId}"]`);
            const panel = document.querySelector(`[data-gallery-group-panel="${groupId}"]`);

            if (!(toggle instanceof HTMLElement) || !(panel instanceof HTMLElement)) {
                return;
            }

            toggle.setAttribute("aria-expanded", String(isOpen));
            toggle.classList.toggle("is-open", isOpen);
            panel.hidden = !isOpen;
            panel.classList.toggle("is-open", isOpen);
        };

        const closeOtherGalleryGroups = (activeGroupId) => {
            galleryGroupToggles.forEach((toggle) => {
                const groupId = toggle.dataset.galleryGroupToggle || "";

                if (!groupId || groupId === activeGroupId) {
                    return;
                }

                setGalleryGroupState(groupId, false);
            });
        };

        galleryGroupToggles.forEach((toggle) => {
            const groupId = toggle.dataset.galleryGroupToggle || "";

            if (!groupId) {
                return;
            }

            toggle.addEventListener("click", () => {
                const isOpen = toggle.getAttribute("aria-expanded") === "true";
                const groupShell = toggle.closest(".gallery-group-shell");

                closeOtherGalleryGroups(groupId);
                setGalleryGroupState(groupId, !isOpen);

                if (!isOpen) {
                    window.requestAnimationFrame(() => {
                        scrollElementIntoViewWithHeaderOffset(
                            groupShell instanceof HTMLElement ? groupShell : toggle,
                        );
                    });
                }
            });
        });
    }

    if (galleryExpandToggles.length) {
        galleryExpandToggles.forEach((toggle) => {
            const groupShell = toggle.closest(".gallery-group-shell");

            if (!(groupShell instanceof HTMLElement)) {
                return;
            }

            toggle.addEventListener("click", () => {
                const isExpanded = toggle.getAttribute("aria-expanded") === "true";
                const nextExpandedState = !isExpanded;
                const extraPhotos = Array.from(groupShell.querySelectorAll("[data-gallery-extra]"));
                const collapsedLabel = toggle.dataset.collapsedLabel || "View more photos";
                const expandedLabel = toggle.dataset.expandedLabel || "Show fewer photos";

                extraPhotos.forEach((photo) => {
                    photo.hidden = !nextExpandedState;
                });

                toggle.setAttribute("aria-expanded", String(nextExpandedState));
                toggle.textContent = nextExpandedState ? expandedLabel : collapsedLabel;

                if (!nextExpandedState) {
                    window.requestAnimationFrame(() => {
                        scrollElementIntoViewWithHeaderOffset(groupShell);
                    });
                }
            });
        });

    }

    if (backgroundSlides.length && backgroundTargets.length) {
        const activateBackground = (targetId) => {
            backgroundSlides.forEach((slide) => {
                slide.classList.toggle("is-active", slide.dataset.bgSlide === targetId);
            });
        };

        const backgroundObserver = new IntersectionObserver(
            (entries) => {
                const visibleEntries = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort((left, right) => right.intersectionRatio - left.intersectionRatio);

                if (!visibleEntries.length) {
                    return;
                }

                activateBackground(visibleEntries[0].target.dataset.bgTarget);
            },
            {
                rootMargin: "-22% 0px -30% 0px",
                threshold: [0.2, 0.35, 0.55, 0.75],
            },
        );

        backgroundTargets.forEach((target) => backgroundObserver.observe(target));
        activateBackground(backgroundTargets[0].dataset.bgTarget);
    }

    if (mapElement && window.L) {
        const defaultLat = Number(mapElement.dataset.defaultLat || "12.82");
        const defaultLng = Number(mapElement.dataset.defaultLng || "120.97");
        const defaultZoom = Number(mapElement.dataset.defaultZoom || "9");
        const allowMapInteraction = mapElement.dataset.mapInteractive !== "false";
        const shouldFitPoints = mapElement.dataset.fitPoints !== "false";
        const lockView = mapElement.dataset.lockView === "true";
        const freezeView = mapElement.dataset.freezeView === "true";
        const limitZoomOutToBounds = mapElement.dataset.limitZoomOutToBounds === "true";
        const minZoom = Number(mapElement.dataset.minZoom || "8");
        const maxZoom = Number(mapElement.dataset.maxZoom || "12");
        const focusZoom = Number(mapElement.dataset.focusZoom || "11");
        const pointFitPadRatio = Number(mapElement.dataset.fitPadRatio || "0.25");
        const pointFitMaxZoom = Number(mapElement.dataset.fitMaxZoom || "13");
        const allowViewportMovement = allowMapInteraction && !lockView && !freezeView;
        const showPointLabels = !allowViewportMovement;
        const scrollWheelZoom = allowViewportMovement && mapElement.dataset.scrollWheelZoom === "true";
        const boundedFocusZoom = Math.min(Math.max(focusZoom, minZoom), maxZoom);
        const routeOriginId = mapElement.dataset.routeOrigin || "";
        const routeDataElement = document.querySelector("[data-map-routes]");
        const pointPanel = document.querySelector("[data-map-point-panel]");
        const pointPanelMedia = document.querySelector("[data-map-point-media]");
        const pointPanelImage = document.querySelector("[data-map-point-image]");
        const pointPanelType = document.querySelector("[data-map-point-type]");
        const pointPanelTitle = document.querySelector("[data-map-point-title]");
        const pointPanelLocation = document.querySelector("[data-map-point-location]");
        const pointPanelNote = document.querySelector("[data-map-point-note]");
        const routePanel = document.querySelector("[data-map-route-panel]");
        const routeTitle = document.querySelector("[data-map-route-title]");
        const routeSummary = document.querySelector("[data-map-route-summary]");
        const routeDistance = document.querySelector("[data-map-route-distance]");
        const routeElevationGain = document.querySelector("[data-map-route-elevation-gain]");
        const routeHighestPoint = document.querySelector("[data-map-route-highest-point]");
        const routeClearButton = document.querySelector("[data-map-route-clear]");
        const mindoroBounds = window.L.latLngBounds(
            [12.22, 120.43],
            [13.55, 121.45],
        );
        const hasLockedBounds = [
            mapElement.dataset.lockSwLat,
            mapElement.dataset.lockSwLng,
            mapElement.dataset.lockNeLat,
            mapElement.dataset.lockNeLng,
        ].every((value) => value !== undefined);
        const lockedBounds = hasLockedBounds
            ? window.L.latLngBounds(
                [
                    Number(mapElement.dataset.lockSwLat),
                    Number(mapElement.dataset.lockSwLng),
                ],
                [
                    Number(mapElement.dataset.lockNeLat),
                    Number(mapElement.dataset.lockNeLng),
                ],
            )
            : null;
        const interactionBounds = lockedBounds ?? mindoroBounds;
        const defaultStyle = {
            color: "#fff8ec",
            weight: 2,
            fillColor: "#8c6239",
            fillOpacity: 0.92,
            radius: 8,
        };
        const activeStyle = {
            color: "#fff8ec",
            weight: 3,
            fillColor: "#17392c",
            fillOpacity: 1,
            radius: 10,
        };
        const routeOriginStyle = {
            color: "#fff8ec",
            weight: 3,
            fillColor: "#d5b57a",
            fillOpacity: 1,
            radius: 9,
        };

        const map = window.L.map(mapElement, {
            center: [defaultLat, defaultLng],
            zoom: defaultZoom,
            scrollWheelZoom,
            minZoom,
            maxZoom,
            maxBounds: interactionBounds,
            maxBoundsViscosity: 1,
            zoomControl: allowViewportMovement,
            doubleClickZoom: allowViewportMovement,
            boxZoom: allowViewportMovement,
            keyboard: allowViewportMovement,
            dragging: allowViewportMovement,
            touchZoom: allowViewportMovement,
        });

        window.L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        const syncZoomOutLimit = () => {
            if (lockView || freezeView || !limitZoomOutToBounds) {
                return;
            }

            const boundsZoom = map.getBoundsZoom(interactionBounds, false, [24, 24]);
            const boundedMinZoom = Math.min(Math.max(boundsZoom, minZoom), maxZoom);

            map.setMinZoom(boundedMinZoom);

            if (map.getZoom() < boundedMinZoom) {
                map.setZoom(boundedMinZoom, { animate: false });
            }
        };

        const keepLockedViewport = () => {
            if (!lockView || !lockedBounds) {
                return;
            }

            map.fitBounds(lockedBounds, {
                animate: false,
                padding: [12, 12],
            });
        };

        const calculateRouteMetrics = (profile) => {
            let distanceMeters = 0;
            let elevationGainMeters = 0;
            let highestPointMeters = 0;

            profile.forEach((point, index) => {
                highestPointMeters = Math.max(highestPointMeters, point.elevation);

                if (index === 0) {
                    return;
                }

                const previousPoint = profile[index - 1];

                distanceMeters += window.L.latLng(previousPoint.lat, previousPoint.lng)
                    .distanceTo(window.L.latLng(point.lat, point.lng));

                const elevationDelta = point.elevation - previousPoint.elevation;

                if (elevationDelta > 0) {
                    elevationGainMeters += elevationDelta;
                }
            });

            return {
                distanceMeters,
                elevationGainMeters,
                highestPointMeters,
            };
        };

        const formatDistance = (meters) => `${(meters / 1000).toFixed(1)} km`;
        const formatHeight = (meters) => `${Math.round(meters)} m`;

        const routeDefinitions = (() => {
            if (!routeDataElement) {
                return [];
            }

            try {
                const parsedRoutes = JSON.parse(routeDataElement.textContent || "[]");

                if (!Array.isArray(parsedRoutes)) {
                    return [];
                }

                return parsedRoutes.map((route) => {
                    const profile = Array.isArray(route.profile)
                        ? route.profile
                            .map((point) => ({
                                lat: Number(point.lat),
                                lng: Number(point.lng),
                                elevation: Number(point.elevation || 0),
                            }))
                            .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng))
                        : [];

                    return {
                        from: String(route.from || ""),
                        to: String(route.to || ""),
                        title: String(route.title || ""),
                        note: String(route.note || ""),
                        profile,
                        metrics: calculateRouteMetrics(profile),
                    };
                }).filter((route) => route.from && route.to && route.profile.length > 1);
            } catch (error) {
                console.error("Unable to parse map routes.", error);
                return [];
            }
        })();

        const routeMap = new Map(
            routeDefinitions.map((route) => [`${route.from}:${route.to}`, route]),
        );

        const points = (() => {
            if (pointCards.length) {
                return pointCards.map((card) => ({
                    id: card.dataset.pointId,
                    label: card.dataset.pointLabel,
                    mapLabel: card.dataset.mapLabel || card.dataset.title,
                    title: card.dataset.title,
                    type: card.dataset.type,
                    location: card.dataset.location,
                    note: card.dataset.note,
                    lat: Number(card.dataset.lat),
                    lng: Number(card.dataset.lng),
                    markerColor: card.dataset.markerColor || "",
                    labelDirection: card.dataset.labelDirection || "top",
                    labelOffsetX: Number(card.dataset.labelOffsetX || 0),
                    labelOffsetY: Number(card.dataset.labelOffsetY || 0),
                    osmUrl: card.dataset.osmUrl,
                    image: card.dataset.image || "",
                    imageAlt: card.dataset.imageAlt || card.dataset.title || "",
                    card,
                }));
            }

            if (!pointDataElement) {
                return [];
            }

            try {
                const parsedPoints = JSON.parse(pointDataElement.textContent || "[]");

                if (!Array.isArray(parsedPoints)) {
                    return [];
                }

                return parsedPoints
                    .map((point) => ({
                        id: String(point.id || ""),
                        label: String(point.label || ""),
                        mapLabel: String(point.map_label || point.title || point.label || ""),
                        title: String(point.title || ""),
                        type: String(point.type || ""),
                        location: String(point.location || ""),
                        note: String(point.note || ""),
                        lat: Number(point.lat),
                        lng: Number(point.lng),
                        markerColor: String(point.marker_color || ""),
                        labelDirection: String(point.label_direction || "top"),
                        labelOffsetX: Number(point.label_offset_x || 0),
                        labelOffsetY: Number(point.label_offset_y || 0),
                        osmUrl: String(point.osm_url || ""),
                        image: String(point.image || ""),
                        imageAlt: String(point.image_alt || point.title || ""),
                        card: null,
                    }))
                    .filter((point) => point.id && Number.isFinite(point.lat) && Number.isFinite(point.lng));
            } catch (error) {
                console.error("Unable to parse map points.", error);
                return [];
            }
        })();

        if (!points.length) {
            return;
        }

        const pointsById = new Map(points.map((point) => [point.id, point]));
        const markerMap = new Map();
        let activeRoute = null;
        let activeRouteLayer = null;
        let activeRoutePopupLayer = null;
        let currentPointId = null;

        const getMarkerStyle = (point, baseStyle) => ({
            ...baseStyle,
            fillColor: point.markerColor || baseStyle.fillColor,
        });

        const routePopupMarkup = (route) => `
            <div class="geo-popup">
                <p>Route preview</p>
                <h3>${route.title}</h3>
                <p>Approx. distance: ${formatDistance(route.metrics.distanceMeters)}</p>
                <p>Height gain: ${formatHeight(route.metrics.elevationGainMeters)}</p>
                <p>High point: ${formatHeight(route.metrics.highestPointMeters)}</p>
                <p>Illustrative line only, not the exact trekking path.</p>
            </div>
        `;

        const updatePointPanel = (point) => {
            if (pointPanelImage && pointPanelMedia) {
                if (point.image) {
                    pointPanelImage.src = point.image;
                    pointPanelImage.alt = point.imageAlt || point.title || "";
                    pointPanelMedia.hidden = false;
                } else {
                    pointPanelImage.src = "";
                    pointPanelImage.alt = "";
                    pointPanelMedia.hidden = true;
                }
            }

            if (!pointPanel) {
                return;
            }

            if (pointPanelType) {
                pointPanelType.textContent = point.type || "Selected Point";
            }

            if (pointPanelTitle) {
                pointPanelTitle.textContent = point.title || "";
            }

            if (pointPanelLocation) {
                pointPanelLocation.textContent = point.location || "";
            }

            if (pointPanelNote) {
                pointPanelNote.textContent = point.note || "";
            }
        };

        const updateRoutePanel = (route = null, selectedPoint = null) => {
            if (!routePanel) {
                return;
            }

            if (!route) {
                const originPoint = pointsById.get(routeOriginId);
                const originTitle = originPoint ? originPoint.title : "Protected Area Management Office";
                const isNonOriginSelection = Boolean(
                    selectedPoint
                    && routeOriginId
                    && selectedPoint.id !== routeOriginId,
                );

                routeDistance.textContent = "--";
                routeElevationGain.textContent = "--";
                routeHighestPoint.textContent = "--";

                if (isNonOriginSelection) {
                    routeTitle.textContent = `Route preview unavailable for ${selectedPoint.title}`;
                    routeSummary.textContent = `A route preview from ${originTitle} to ${selectedPoint.title} is not available yet. Choose another station or return to ${originTitle}.`;

                    if (routeClearButton) {
                        routeClearButton.hidden = false;
                    }

                    return;
                }

                routeTitle.textContent = `Select a destination from ${originTitle}`;
                routeSummary.textContent = `${originTitle} is the route origin on this page. Click another station marker while it is active to preview an approximate route line, estimated distance, and elevation change.`;

                if (routeClearButton) {
                    routeClearButton.hidden = true;
                }

                return;
            }

            routeTitle.textContent = route.title;
            routeSummary.textContent = route.note;
            routeDistance.textContent = formatDistance(route.metrics.distanceMeters);
            routeElevationGain.textContent = formatHeight(route.metrics.elevationGainMeters);
            routeHighestPoint.textContent = formatHeight(route.metrics.highestPointMeters);

            if (routeClearButton) {
                routeClearButton.hidden = false;
            }
        };

        const removeActiveRoute = () => {
            if (!activeRouteLayer) {
                return;
            }

            map.removeLayer(activeRouteLayer);
            activeRouteLayer = null;
            activeRoutePopupLayer = null;
        };

        const clampRouteCurve = (value, minimum, maximum) => (
            Math.min(Math.max(value, minimum), maximum)
        );

        // Smooth the saved route profile into a more trail-like line while still
        // following the same overall waypoint path from PAMO to each station.
        const buildRouteTrailLatLngs = (profile) => {
            if (!Array.isArray(profile) || profile.length < 2) {
                return [];
            }

            const trailLatLngs = [[profile[0].lat, profile[0].lng]];

            profile.forEach((point, index) => {
                if (index === 0) {
                    return;
                }

                const previousPoint = profile[index - 1];
                const distanceMeters = window.L.latLng(previousPoint.lat, previousPoint.lng)
                    .distanceTo(window.L.latLng(point.lat, point.lng));

                if (!Number.isFinite(distanceMeters) || distanceMeters <= 0) {
                    trailLatLngs.push([point.lat, point.lng]);
                    return;
                }

                const averageLatitudeRadians = ((previousPoint.lat + point.lat) / 2) * (Math.PI / 180);
                const longitudeScale = Math.max(Math.cos(averageLatitudeRadians), 0.1);
                const start = {
                    x: previousPoint.lng * longitudeScale,
                    y: previousPoint.lat,
                };
                const end = {
                    x: point.lng * longitudeScale,
                    y: point.lat,
                };
                const deltaX = end.x - start.x;
                const deltaY = end.y - start.y;
                const segmentLength = Math.hypot(deltaX, deltaY);

                if (!segmentLength) {
                    trailLatLngs.push([point.lat, point.lng]);
                    return;
                }

                const curveOffsetDegrees = clampRouteCurve(
                    (distanceMeters / 111320) * 0.065,
                    0.00008,
                    0.00165,
                );
                const curveDirection = index % 2 === 0 ? -1 : 1;
                const controlPoint = {
                    x: ((start.x + end.x) / 2) + (((-deltaY) / segmentLength) * curveOffsetDegrees * curveDirection),
                    y: ((start.y + end.y) / 2) + (((deltaX) / segmentLength) * curveOffsetDegrees * curveDirection),
                };
                const stepCount = clampRouteCurve(
                    Math.round(distanceMeters / 900),
                    3,
                    7,
                );

                for (let stepIndex = 1; stepIndex <= stepCount; stepIndex += 1) {
                    const t = stepIndex / stepCount;
                    const inverseT = 1 - t;
                    const x = (inverseT * inverseT * start.x)
                        + (2 * inverseT * t * controlPoint.x)
                        + (t * t * end.x);
                    const y = (inverseT * inverseT * start.y)
                        + (2 * inverseT * t * controlPoint.y)
                        + (t * t * end.y);

                    trailLatLngs.push([y, x / longitudeScale]);
                }
            });

            return trailLatLngs;
        };

        const getRouteDisplayLatLngs = (route) => {
            if (route.profile.length > 1) {
                const trailLatLngs = buildRouteTrailLatLngs(route.profile);

                if (trailLatLngs.length > 1) {
                    return trailLatLngs;
                }
            }

            const originPoint = pointsById.get(route.from);
            const destinationPoint = pointsById.get(route.to);

            if (originPoint && destinationPoint) {
                return [
                    [originPoint.lat, originPoint.lng],
                    [destinationPoint.lat, destinationPoint.lng],
                ];
            }

            return [];
        };

        const renderActiveRoute = (selectedPoint = null) => {
            removeActiveRoute();

            if (!activeRoute) {
                updateRoutePanel(null, selectedPoint);
                return;
            }

            const latLngs = getRouteDisplayLatLngs(activeRoute);

            if (latLngs.length < 2) {
                updateRoutePanel(activeRoute);
                return;
            }

            const routeLine = window.L.polyline(latLngs, {
                color: "#17392c",
                weight: 5,
                opacity: 0.94,
                dashArray: "18 14",
                lineCap: "butt",
                lineJoin: "round",
            }).bindPopup(routePopupMarkup(activeRoute));

            activeRouteLayer = window.L.featureGroup(
                [routeLine],
            ).addTo(map);
            activeRoutePopupLayer = routeLine;
            updateRoutePanel(activeRoute);
        };

        const getViewportFitPadding = () => {
            const hasDesktopOverlays = window.innerWidth > 960 && (pointPanel || routePanel);
            const leftPadding = hasDesktopOverlays && pointPanel
                ? pointPanel.offsetWidth + 68
                : 24;
            const rightPadding = hasDesktopOverlays && routePanel
                ? routePanel.offsetWidth + 68
                : 24;
            const topPadding = hasDesktopOverlays ? 112 : 24;
            const bottomPadding = hasDesktopOverlays ? 96 : 24;

            return {
                paddingTopLeft: [leftPadding, topPadding],
                paddingBottomRight: [rightPadding, bottomPadding],
            };
        };

        const fitBoundsInViewport = (targetBounds, options = {}) => {
            if (!targetBounds) {
                return;
            }

            const {
                padRatio = 0,
                maxZoom: fitMaxZoom,
            } = options;
            const fitBounds = padRatio ? targetBounds.pad(padRatio) : targetBounds;

            map.fitBounds(fitBounds, {
                ...getViewportFitPadding(),
                animate: false,
                ...(typeof fitMaxZoom === "number" ? { maxZoom: fitMaxZoom } : {}),
            });
            map.panInsideBounds(interactionBounds, { animate: false });
        };

        const fitMapToRoute = () => {
            if (lockView) {
                keepLockedViewport();
                return;
            }

            if (!activeRouteLayer) {
                return;
            }

            fitBoundsInViewport(activeRouteLayer.getBounds(), {
                padRatio: 0.18,
                maxZoom: Math.min(boundedFocusZoom, 13),
            });
        };

        const syncSelectionState = (activePointId) => {
            points.forEach((entry) => {
                const isActive = entry.id === activePointId;
                const isRouteOrigin = Boolean(activeRoute && entry.id === activeRoute.from);

                if (entry.card) {
                    entry.card.classList.toggle("is-active", isActive);
                    entry.card.classList.toggle("is-route-origin", isRouteOrigin);
                    entry.card.setAttribute("aria-pressed", String(isActive));
                }

                const marker = markerMap.get(entry.id);
                const markerStyle = isActive
                    ? getMarkerStyle(entry, activeStyle)
                    : isRouteOrigin
                        ? getMarkerStyle(entry, routeOriginStyle)
                        : getMarkerStyle(entry, defaultStyle);

                marker.setStyle(markerStyle);
            });
        };

        points.forEach((point) => {
            const marker = window.L.circleMarker([point.lat, point.lng], {
                ...getMarkerStyle(point, defaultStyle),
                interactive: allowMapInteraction,
            }).addTo(map);

            if (showPointLabels && point.mapLabel) {
                marker.bindTooltip(point.mapLabel, {
                    permanent: true,
                    direction: point.labelDirection,
                    offset: window.L.point(point.labelOffsetX, point.labelOffsetY),
                    className: "geo-label-tooltip",
                    opacity: 1,
                });
            }

            if (allowMapInteraction) {
                marker.on("click", () => {
                    activatePoint(point, false);
                });
            }

            markerMap.set(point.id, marker);
        });

        const bounds = window.L.latLngBounds(points.map((point) => [point.lat, point.lng]));
        if (lockView && lockedBounds) {
            keepLockedViewport();
            const lockedZoom = map.getZoom();
            map.setMinZoom(lockedZoom);
            map.setMaxZoom(lockedZoom);
        } else if (shouldFitPoints) {
            fitBoundsInViewport(bounds, {
                padRatio: Number.isFinite(pointFitPadRatio) ? pointFitPadRatio : 0.25,
                maxZoom: Math.min(
                    boundedFocusZoom,
                    Number.isFinite(pointFitMaxZoom) ? pointFitMaxZoom : 13,
                ),
            });
        } else {
            map.setView([defaultLat, defaultLng], defaultZoom, { animate: false });
        }

        syncZoomOutLimit();
        map.panInsideBounds(interactionBounds, { animate: false });

        if (freezeView) {
            const frozenZoom = map.getZoom();
            map.setMinZoom(frozenZoom);
            map.setMaxZoom(frozenZoom);
        }

        if (limitZoomOutToBounds && !lockView && !freezeView) {
            window.addEventListener("resize", () => {
                window.requestAnimationFrame(syncZoomOutLimit);
            });
        }

        if (!allowMapInteraction) {
            return;
        }

        function activatePoint(point, shouldFly = true) {
            const isRouteOriginSelection = Boolean(routeOriginId && point.id === routeOriginId);
            currentPointId = point.id;

            if (routeOriginId && point.id !== routeOriginId) {
                activeRoute = routeMap.get(`${routeOriginId}:${point.id}`) ?? null;
            } else {
                activeRoute = null;
            }

            renderActiveRoute(point);
            syncSelectionState(point.id);
            updatePointPanel(point);

            if (externalMapLink && point.osmUrl) {
                externalMapLink.href = point.osmUrl;
            }

            if (freezeView) {
                return;
            }

            if (activeRoute && activeRouteLayer) {
                fitMapToRoute();
            } else if (lockView) {
                keepLockedViewport();
            } else if (shouldFly) {
                map.flyTo(
                    [point.lat, point.lng],
                    isRouteOriginSelection ? map.getZoom() : boundedFocusZoom,
                    { duration: 1.2 },
                );
                map.panInsideBounds(interactionBounds, { animate: false });
            }
        }

        pointCards.forEach((card) => {
            card.addEventListener("click", () => {
                const point = points.find((entry) => entry.id === card.dataset.pointId);

                if (point) {
                    activatePoint(point);
                }
            });
        });

        if (routeClearButton && routeOriginId) {
            routeClearButton.addEventListener("click", () => {
                const originPoint = pointsById.get(routeOriginId);

                if (originPoint) {
                    activatePoint(originPoint);
                }
            });
        }

        activatePoint(points[0], false);
    }
});
