/**
 * Editorial front page: nav, scroll reveal, project modal, recognition / certificate modal.
 */
(function () {
	'use strict';

	var header = document.querySelector('[data-editorial-header]');
	if (header) {
		function onScroll() {
			if (window.scrollY > 40) {
				header.classList.add('is-scrolled');
			} else {
				header.classList.remove('is-scrolled');
			}
		}
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	var toggle = document.querySelector('[data-mobile-nav-toggle]');
	var panel = document.querySelector('[data-mobile-nav-panel]');
	if (toggle && panel) {
		var iconOpen = toggle.querySelector('.icon-open');
		var iconClose = toggle.querySelector('.icon-close');

		function setOpen(open) {
			panel.classList.toggle('is-open', open);
			panel.setAttribute('aria-hidden', open ? 'false' : 'true');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			if (iconOpen && iconClose) {
				iconOpen.classList.toggle('hidden', open);
				iconClose.classList.toggle('hidden', !open);
			}
		}

		toggle.addEventListener('click', function () {
			setOpen(!panel.classList.contains('is-open'));
		});

		panel.querySelectorAll('[data-mobile-nav-link]').forEach(function (link) {
			link.addEventListener('click', function () {
				setOpen(false);
			});
		});
	}

	var desktopNav = document.querySelector('[data-editorial-header] nav[aria-label]');
	if (desktopNav) {
		var dropdownParents = desktopNav.querySelectorAll('.menu-item-has-children');
		dropdownParents.forEach(function (parent) {
			var link = parent.querySelector(':scope > a');
			var submenu = parent.querySelector(':scope > .sub-menu');
			if (!link || !submenu) {
				return;
			}
			link.setAttribute('aria-expanded', 'false');
			link.addEventListener('click', function (e) {
				e.preventDefault();
				var willOpen = !parent.classList.contains('is-open');
				dropdownParents.forEach(function (other) {
					other.classList.remove('is-open');
					var otherLink = other.querySelector(':scope > a');
					if (otherLink) {
						otherLink.setAttribute('aria-expanded', 'false');
					}
				});
				parent.classList.toggle('is-open', willOpen);
				link.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
			});
		});

		document.addEventListener('click', function (e) {
			if (!desktopNav.contains(e.target)) {
				dropdownParents.forEach(function (parent) {
					parent.classList.remove('is-open');
					var link = parent.querySelector(':scope > a');
					if (link) {
						link.setAttribute('aria-expanded', 'false');
					}
				});
			}
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				dropdownParents.forEach(function (parent) {
					parent.classList.remove('is-open');
				});
			}
		});
	}

	var revealEls = document.querySelectorAll('.scroll-reveal');
	if (revealEls.length && 'IntersectionObserver' in window) {
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (e) {
					if (e.isIntersecting) {
						e.target.classList.add('is-inview');
						io.unobserve(e.target);
					}
				});
			},
			{ root: null, rootMargin: '-60px 0px', threshold: 0.05 }
		);
		revealEls.forEach(function (el) {
			io.observe(el);
		});
	} else {
		revealEls.forEach(function (el) {
			el.classList.add('is-inview');
		});
	}

	function lockScroll() {
		document.documentElement.style.overflow = 'hidden';
	}

	function unlockScroll() {
		document.documentElement.style.overflow = '';
	}

	/* ——— Carousel Control ——— */
	document.querySelectorAll('.carousel').forEach(function (carousel) {
		var carouselId = carousel.getAttribute('data-carousel-id');
		var items = carousel.querySelectorAll('.carousel-item');
		var indicators = carousel.querySelectorAll('.carousel-indicators li');
		var prevBtn = carousel.querySelector('[data-slide="prev"]');
		var nextBtn = carousel.querySelector('[data-slide="next"]');
		var currentIndex = 0;
		var autoplayInterval = null;

		function showSlide(index) {
			if (index < 0) {
				index = items.length - 1;
			}
			if (index >= items.length) {
				index = 0;
			}
			currentIndex = index;

			items.forEach(function (item) {
				item.classList.remove('active');
			});
			indicators.forEach(function (indicator) {
				indicator.classList.remove('active', 'bg-primary');
				indicator.classList.add('bg-muted');
			});

			items[currentIndex].classList.add('active');
			indicators[currentIndex].classList.add('active', 'bg-primary');
			indicators[currentIndex].classList.remove('bg-muted');
		}

		function nextSlide() {
			showSlide(currentIndex + 1);
			startAutoplay();
		}

		function prevSlide() {
			showSlide(currentIndex - 1);
			startAutoplay();
		}

		function startAutoplay() {
			if (autoplayInterval) {
				clearInterval(autoplayInterval);
			}
			autoplayInterval = setInterval(function () {
				nextSlide();
			}, 6000);
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', prevSlide);
		}
		if (nextBtn) {
			nextBtn.addEventListener('click', nextSlide);
		}

		indicators.forEach(function (indicator, index) {
			indicator.addEventListener('click', function () {
				showSlide(index);
				startAutoplay();
			});
		});

		carousel.addEventListener('mouseenter', function () {
			if (autoplayInterval) {
				clearInterval(autoplayInterval);
			}
		});

		carousel.addEventListener('mouseleave', function () {
			startAutoplay();
		});

		startAutoplay();
	});

	/* ——— Project Spotlight ——— */
	var spotlight = document.querySelector('[data-project-spotlight]');
	if (spotlight) {
		var grid = spotlight.querySelector('[data-project-grid]');
		var filterWrap = spotlight.querySelector('[data-project-filters]');
		var seeAllBtn = spotlight.querySelector('[data-project-see-all]');
		var expandedCategory = null;
		var visibleLimit = 6;

		function setFilterButtons(cat) {
			if (!filterWrap) {
				return;
			}
			filterWrap.querySelectorAll('[data-project-filter]').forEach(function (btn) {
				var isOn = btn.getAttribute('data-project-filter') === cat;
				btn.classList.toggle('bg-primary', isOn);
				btn.classList.toggle('text-primary-foreground', isOn);
				btn.classList.toggle('bg-secondary', !isOn);
				btn.classList.toggle('text-muted-foreground', !isOn);
				btn.classList.toggle('hover:text-foreground', !isOn);
			});
		}

		function updateSeeAllButton(cat, totalVisible, expanded) {
			if (!seeAllBtn) {
				return;
			}
			if (totalVisible <= visibleLimit) {
				seeAllBtn.classList.add('hidden');
				seeAllBtn.style.display = 'none';
				seeAllBtn.textContent = 'See all';
				return;
			}

			seeAllBtn.classList.remove('hidden');
			seeAllBtn.style.display = 'inline-flex';
			if (expanded) {
				seeAllBtn.textContent = 'Show less';
				return;
			}

			seeAllBtn.textContent = cat === 'All' ? 'See all projects' : 'See all in ' + cat;
		}

		function applyFilter(cat) {
			setFilterButtons(cat);
			if (!grid) {
				return;
			}
			var matchedCards = [];
			grid.querySelectorAll('[data-project-card]').forEach(function (card) {
				var c = card.getAttribute('data-category') || '';
				var matches = cat === 'All' || c === cat;
				card.classList.toggle('is-filtered-out', !matches);
				card.classList.remove('is-over-limit');
				if (matches) {
					matchedCards.push(card);
				}
			});

			var expanded = expandedCategory === cat;
			if (!expanded) {
				matchedCards.forEach(function (card, index) {
					if (index >= visibleLimit) {
						card.classList.add('is-over-limit');
					}
				});
			}

			updateSeeAllButton(cat, matchedCards.length, expanded);
		}

		if (filterWrap) {
			filterWrap.addEventListener('click', function (e) {
				var btn = e.target.closest('[data-project-filter]');
				if (!btn) {
					return;
				}
				expandedCategory = null;
				applyFilter(btn.getAttribute('data-project-filter') || 'All');
			});
		}

		if (seeAllBtn) {
			seeAllBtn.style.display = 'none';
			seeAllBtn.addEventListener('click', function () {
				if (!filterWrap) {
					return;
				}
				var activeBtn = filterWrap.querySelector('.bg-primary[data-project-filter]');
				var currentCategory = activeBtn ? activeBtn.getAttribute('data-project-filter') || 'All' : 'All';
				expandedCategory = expandedCategory === currentCategory ? null : currentCategory;
				applyFilter(currentCategory);
			});
		}

		applyFilter('All');
	}

	/* ——— Project Detail Page ——— */
	var projectDetail = document.querySelector('[data-project-detail]');
	if (projectDetail) {
		var commentsList = projectDetail.querySelector('[data-comments-list]');
		var commentForm = projectDetail.querySelector('[data-comment-form]');
		var projectTitle = document.querySelector('[data-project-detail] h1');
		var projectName = projectTitle ? projectTitle.textContent.trim() : '';

		function escapeHtml(text) {
			var div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		function loadComments() {
			if (!commentsList) {
				return;
			}
			commentsList.innerHTML = '';
			var comments = JSON.parse(localStorage.getItem('project-comments-' + projectName) || '[]');
			if (comments.length === 0) {
				var noComments = document.createElement('div');
				noComments.className = 'rounded-sm border border-border bg-secondary/20 p-6 text-center';
				noComments.innerHTML = '<p class="font-body text-muted-foreground">No comments yet. Be the first to share your thoughts!</p>';
				commentsList.appendChild(noComments);
			} else {
				comments.forEach(function (comment) {
					var commentEl = document.createElement('div');
					commentEl.className = 'rounded-sm border border-border bg-background p-6';
					commentEl.innerHTML =
						'<p class="mb-2 font-body font-semibold text-foreground">' + escapeHtml(comment.name) + '</p>' +
						'<p class="font-body text-muted-foreground leading-relaxed">' + escapeHtml(comment.text) + '</p>' +
						'<p class="mt-3 font-body text-xs text-muted-foreground">Just now</p>';
					commentsList.appendChild(commentEl);
				});
			}
		}

		if (commentForm) {
			commentForm.addEventListener('submit', function (e) {
				e.preventDefault();
				var nameInput = commentForm.querySelector('input[name="name"]');
				var textarea = commentForm.querySelector('textarea[name="comment"]');
				if (!nameInput || !nameInput.value.trim() || !textarea || !textarea.value.trim()) {
					return;
				}
				var comments = JSON.parse(localStorage.getItem('project-comments-' + projectName) || '[]');
				comments.unshift({
					name: nameInput.value.trim(),
					text: textarea.value.trim()
				});
				localStorage.setItem('project-comments-' + projectName, JSON.stringify(comments));
				nameInput.value = '';
				textarea.value = '';
				loadComments();
			});
		}

		loadComments();
	}

	/* ——— Recognition / certificate modal ——— */
	var recSection = document.querySelector('[data-recognition-section]');
	if (recSection) {
		var rModal = recSection.querySelector('[data-recognition-modal]');
		var rPanel = recSection.querySelector('[data-recognition-modal-panel]');
		var rClose = recSection.querySelector('[data-recognition-modal-close]');
		var rTitle = recSection.querySelector('[data-recognition-modal-title]');
		var rName = recSection.querySelector('[data-recog-name]');
		var rDesig = recSection.querySelector('[data-recog-designation]');
		var rImg = recSection.querySelector('[data-recog-cert-img]');
		var rMiss = recSection.querySelector('[data-recog-cert-missing]');
		var rCit = recSection.querySelector('[data-recog-citation]');
		var rSoc = recSection.querySelector('[data-recog-socials]');
		var rDl = recSection.querySelector('[data-recog-download]');

		var socialConfig = [
			{ key: 'facebook', label: 'Facebook', icon: 'social-facebook' },
			{ key: 'twitter', label: 'X', icon: 'social-twitter' },
			{ key: 'linkedin', label: 'LinkedIn', icon: 'social-linkedin' },
			{ key: 'whatsapp', label: 'WhatsApp', icon: 'social-whatsapp' },
		];

		var svgSprites = {
			'social-facebook':
				'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
			'social-twitter':
				'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
			'social-linkedin':
				'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>',
			'social-whatsapp':
				'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
		};

		function openRecognitionModal(data) {
			if (!rModal) {
				return;
			}
			if (rTitle) {
				rTitle.textContent = data.name || '';
			}
			if (rName) {
				rName.textContent = data.name || '';
			}
			if (rDesig) {
				rDesig.textContent = data.designation || '';
				rDesig.style.display = data.designation ? '' : 'none';
			}
			if (rCit) {
				rCit.textContent = data.citation || '';
				rCit.style.display = data.citation ? 'block' : 'none';
			}

			if (rImg && rMiss) {
				if (data.certificateUrl) {
					rImg.src = data.certificateUrl;
					rImg.alt = data.name || '';
					rImg.removeAttribute('hidden');
					rMiss.setAttribute('hidden', 'hidden');
				} else {
					rImg.removeAttribute('src');
					rImg.setAttribute('hidden', 'hidden');
					rMiss.removeAttribute('hidden');
				}
			}

			if (rSoc) {
				rSoc.innerHTML = '';
				var socials = data.socials || {};
				socialConfig.forEach(function (sc) {
					var url = socials[sc.key];
					if (!url) {
						return;
					}
					var a = document.createElement('a');
					a.href = url;
					a.target = '_blank';
					a.rel = 'noopener noreferrer';
					a.setAttribute('aria-label', sc.label);
					a.className =
						'inline-flex h-10 w-10 items-center justify-center rounded-sm bg-primary text-primary-foreground transition-opacity hover:opacity-90';
					a.innerHTML = svgSprites[sc.icon] || '';
					rSoc.appendChild(a);
				});
			}

			if (rDl) {
				if (data.downloadUrl) {
					rDl.href = data.downloadUrl;
					rDl.removeAttribute('hidden');
				} else {
					rDl.setAttribute('hidden', 'hidden');
					rDl.removeAttribute('href');
				}
			}

			rModal.removeAttribute('hidden');
			rModal.classList.add('is-active');
			rModal.style.display = 'flex';
			lockScroll();
		}

		function closeRecognitionModal() {
			if (!rModal) {
				return;
			}
			if (rImg) {
				rImg.removeAttribute('src');
				rImg.setAttribute('hidden', 'hidden');
			}
			if (rMiss) {
				rMiss.setAttribute('hidden', 'hidden');
			}
			rModal.setAttribute('hidden', 'hidden');
			rModal.classList.remove('is-active');
			rModal.style.display = '';
			unlockScroll();
		}

		recSection.querySelectorAll('[data-recognition-card]').forEach(function (card) {
			function openFromCard() {
				var raw = card.getAttribute('data-recognition');
				if (!raw) {
					return;
				}
				try {
					openRecognitionModal(JSON.parse(raw));
				} catch (err) {
					return;
				}
			}
			card.addEventListener('click', openFromCard);
			card.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					openFromCard();
				}
			});
		});

		if (rModal) {
			rModal.addEventListener('click', function (e) {
				if (e.target === rModal) {
					closeRecognitionModal();
				}
			});
		}
		if (rClose) {
			rClose.addEventListener('click', closeRecognitionModal);
		}
		if (rPanel) {
			rPanel.addEventListener('click', function (e) {
				e.stopPropagation();
			});
		}

		window._tgitCloseRecognitionModal = closeRecognitionModal;
	}



	document.addEventListener('keydown', function (e) {
		if (e.key !== 'Escape') {
			return;
		}
		var recM = document.querySelector('[data-recognition-modal]');
		if (recM && !recM.hasAttribute('hidden') && window._tgitCloseRecognitionModal) {
			window._tgitCloseRecognitionModal();
			return;
		}
		var pm = document.querySelector('[data-project-modal]');
		if (pm && !pm.hasAttribute('hidden') && window._tgitCloseProjectModal) {
			window._tgitCloseProjectModal();
		}
	});
})();
