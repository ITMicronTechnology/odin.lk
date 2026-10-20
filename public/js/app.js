/* Odin.lk storefront interactions (jQuery) */
$(function () {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    const loginUrl = $('meta[name="login-url"]').attr('content');

    /* Toast notifications ---------------------------------------------------- */
    window.odinToast = function (message, type) {
        const $toast = $('<div class="odin-toast" role="status"></div>')
            .toggleClass('error', type === 'error')
            .append($('<i class="bi"></i>').addClass(type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill'))
            .append($('<div class="small fw-medium"></div>').text(message));

        $('#toastStack').append($toast);
        setTimeout(function () { $toast.fadeOut(300, function () { $toast.remove(); }); }, 3500);
    };

    $('[data-flash]').each(function () {
        odinToast($(this).data('flash'), $(this).data('type'));
    });

    function handleAjaxError(xhr) {
        if (xhr.status === 401) {
            window.location.href = loginUrl;
            return;
        }
        const response = xhr.responseJSON || {};
        odinToast(response.message || 'Something went wrong. Please try again.', 'error');
    }

    /* Header counters (wishlist / cart) with a small "bump" animation -------- */
    function setCounter(selector, count) {
        $(selector).each(function () {
            const $badge = $(this);
            const changed = $badge.text() !== String(count);
            $badge.text(count).toggleClass('is-empty', !count);
            if (changed && $badge.hasClass('badge-count')) {
                $badge.removeClass('bump');
                void this.offsetWidth; // restart the animation
                $badge.addClass('bump');
            }
        });
    }

    function updateCartBadges(response) {
        setCounter('.js-cart-count', response.count);
        $('.js-header-cart-amount').text(response.subtotal);
    }

    /* Add to cart ------------------------------------------------------------ */
    $(document).on('click', '.btn-add-cart', function (event) {
        event.preventDefault();
        const $button = $(this);
        const quantity = $($button.data('qty-input')).val() || 1;
        const original = $button.html();

        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.post($button.data('url'), { quantity: quantity })
            .done(function (response) {
                updateCartBadges(response);
                odinToast(response.message);
            })
            .fail(handleAjaxError)
            .always(function () { $button.prop('disabled', false).html(original); });
    });

    /* Wishlist toggle -------------------------------------------------------- */
    $(document).on('click', '.btn-wishlist, .js-wishlist', function (event) {
        event.preventDefault();
        const $button = $(this);

        $.post($button.data('url'))
            .done(function (response) {
                $('[data-url="' + $button.data('url') + '"]').toggleClass('active', response.wishlisted)
                    .find('i').toggleClass('bi-heart-fill', response.wishlisted).toggleClass('bi-heart', !response.wishlisted);
                setCounter('.js-wishlist-count', response.count);
                odinToast(response.message);

                if (!response.wishlisted && $button.closest('[data-wishlist-page]').length) {
                    $button.closest('.col').fadeOut(250);
                }
            })
            .fail(handleAjaxError);
    });

    /* Quantity steppers ------------------------------------------------------ */
    $(document).on('click', '[data-qty-step]', function () {
        const $input = $(this).closest('.qty-group').find('input');
        const min = parseInt($input.attr('min') || 1, 10);
        const max = parseInt($input.attr('max') || 10, 10);
        const next = Math.min(max, Math.max(min, (parseInt($input.val(), 10) || min) + parseInt($(this).data('qty-step'), 10)));
        $input.val(next).trigger('change');
    });

    /* Cart page: live quantity updates --------------------------------------- */
    let cartTimer;
    $(document).on('change', '.js-cart-qty', function () {
        const $input = $(this);
        clearTimeout(cartTimer);
        cartTimer = setTimeout(function () {
            $.ajax({ url: $input.data('url'), method: 'PATCH', data: { quantity: $input.val() } })
                .done(function (response) {
                    $.each(response.lines, function (id, line) {
                        $('#line-total-' + id).text(line.line_total);
                        $('#qty-' + id).val(line.quantity);
                    });
                    updateCartBadges(response);
                    $('.js-cart-subtotal').text(response.subtotal);
                    $('.js-cart-shipping').text(response.shipping);
                    $('.js-cart-total').text(response.total);
                })
                .fail(handleAjaxError);
        }, 350);
    });

    /* Header live search ----------------------------------------------------- */
    const $search = $('#headerSearch');
    const $suggest = $('#searchSuggest');
    let searchTimer;
    let searchRequest;

    $search.on('input', function () {
        const term = $.trim($search.val());
        clearTimeout(searchTimer);

        if (term.length < 2) {
            $suggest.hide().empty();
            return;
        }

        searchTimer = setTimeout(function () {
            if (searchRequest) { searchRequest.abort(); }
            searchRequest = $.getJSON($search.data('suggest-url'), { q: term }).done(function (items) {
                $suggest.empty();

                if (!items.length) {
                    $suggest.append($('<div class="p-3 small text-muted"></div>').text('No products found for "' + term + '"'));
                }

                $.each(items, function (_, item) {
                    $('<a></a>').attr('href', item.url)
                        .append($('<img alt="">').attr('src', item.image))
                        .append($('<div></div>')
                            .append($('<div class="s-name"></div>').text(item.name))
                            .append($('<div class="s-price"></div>').text(item.price)))
                        .appendTo($suggest);
                });

                $('<a class="s-all"></a>')
                    .attr('href', $search.closest('form').attr('action') + '?q=' + encodeURIComponent(term))
                    .text('View all results')
                    .appendTo($suggest);

                $suggest.show();
            });
        }, 250);
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.header-search').length) { $suggest.hide(); }
    });

    /* Product gallery -------------------------------------------------------- */
    $(document).on('click', '.gallery-thumbs button', function () {
        $('.gallery-thumbs button').removeClass('active');
        $(this).addClass('active');
        $('#galleryMain').attr('src', $(this).data('src'));
    });

    /* Product gallery arrows ---------------------------------------------------- */
    $(document).on('click', '[data-gallery-step]', function () {
        const $thumbs = $('.gallery-thumbs button');
        if (!$thumbs.length) { return; }
        const index = $thumbs.index($thumbs.filter('.active'));
        $thumbs.eq((index + parseInt($(this).data('gallery-step'), 10) + $thumbs.length) % $thumbs.length).trigger('click');
    });

    /* Auto-submit filter selects -------------------------------------------- */
    $(document).on('change', '[data-autosubmit]', function () { $(this).closest('form').trigger('submit'); });

    /* Deals countdown (ends Sunday midnight) --------------------------------- */
    const $countdown = $('#dealsCountdown');
    if ($countdown.length) {
        const end = new Date();
        end.setDate(end.getDate() + (7 - end.getDay()) % 7);
        end.setHours(23, 59, 59, 999);

        const tick = function () {
            const diff = Math.max(0, end - new Date());
            const parts = [
                Math.floor(diff / 86400000),
                Math.floor(diff / 3600000) % 24,
                Math.floor(diff / 60000) % 60,
                Math.floor(diff / 1000) % 60
            ];
            $countdown.find('.box').each(function (index) { $(this).text(String(parts[index]).padStart(2, '0')); });
        };
        tick();
        setInterval(tick, 1000);
    }

    /* Category bar: show full names that fit, move the rest into "More" ------ */
    const $navLinks = $('#navLinks');
    const $navMore = $('#navMore');
    const $navMoreMenu = $('#navMoreMenu');

    function fitCategoryBar() {
        if (!$navLinks.length) { return; }

        const $items = $navLinks.children('[data-nav-item]').show();
        $navMore.prop('hidden', true);
        $navMoreMenu.empty();

        const available = $navLinks[0].clientWidth;
        const fixedWidth = $navLinks.children().not('[data-nav-item], #navMore').toArray()
            .reduce(function (total, el) { return total + el.offsetWidth; }, 0);
        let used = fixedWidth;
        let overflowing = false;

        // First pass: does everything fit without a "More" button?
        const allWidth = $items.toArray().reduce(function (total, el) { return total + el.offsetWidth; }, 0);
        if (fixedWidth + allWidth <= available) { return; }

        $navMore.prop('hidden', false);
        used += $navMore[0].offsetWidth;

        $items.each(function () {
            if (!overflowing && used + this.offsetWidth <= available) {
                used += this.offsetWidth;
                return;
            }
            overflowing = true;
            $(this).hide();
            $('<li></li>').append(
                $('<a class="dropdown-item"></a>').attr('href', this.href).toggleClass('active', $(this).hasClass('active')).text($(this).text())
            ).appendTo($navMoreMenu);
        });
    }

    let navResizeTimer;
    fitCategoryBar();
    $(window).on('resize', function () { clearTimeout(navResizeTimer); navResizeTimer = setTimeout(fitCategoryBar, 120); });
    if (document.fonts && document.fonts.ready) { document.fonts.ready.then(fitCategoryBar); }

    /* Sliding product rows (arrows scroll one "page"; arrows hide at the ends) - */
    $('[data-slider]').each(function () {
        const $slider = $(this);
        const track = $slider.find('[data-track]')[0];
        if (!track) { return; }

        const update = function () {
            const max = track.scrollWidth - track.clientWidth - 2;
            $slider.find('[data-slide="-1"]').prop('disabled', track.scrollLeft <= 2);
            $slider.find('[data-slide="1"]').prop('disabled', track.scrollLeft >= max);
        };

        $slider.on('click', '[data-slide]', function () {
            track.scrollBy({ left: track.clientWidth * 0.9 * parseInt($(this).data('slide'), 10), behavior: 'smooth' });
        });
        $(track).on('scroll', function () { window.requestAnimationFrame(update); });
        $(window).on('resize', update);
        update();
    });

    /* Flash deal countdowns ---------------------------------------------------- */
    const $timers = $('[data-countdown]');
    if ($timers.length) {
        const pad = function (n) { return String(n).padStart(2, '0'); };
        const tickTimers = function () {
            $timers.each(function () {
                const diff = Math.max(0, new Date($(this).data('countdown')) - new Date());
                const $t = $(this);
                $t.find('[data-part="d"]').text(pad(Math.floor(diff / 86400000)));
                $t.find('[data-part="h"]').text(pad(Math.floor(diff / 3600000) % 24));
                $t.find('[data-part="m"]').text(pad(Math.floor(diff / 60000) % 60));
                $t.find('[data-part="s"]').text(pad(Math.floor(diff / 1000) % 60));
            });
        };
        tickTimers();
        setInterval(tickTimers, 1000);
    }

    /* Odin picks spotlight (thumbnails switch the big slide; auto-advances) -- */
    const $picks = $('.pick-slide');
    if ($picks.length > 1) {
        let current = 0;
        let picksTimer;
        const showPick = function (index) {
            current = (index + $picks.length) % $picks.length;
            $picks.removeClass('active').eq(current).addClass('active');
            $('[data-pick-to]').removeClass('active').eq(current).addClass('active');
        };
        const restart = function () { clearInterval(picksTimer); picksTimer = setInterval(function () { showPick(current + 1); }, 6000); };
        $(document).on('click', '[data-pick-to]', function () { showPick(parseInt($(this).data('pick-to'), 10)); restart(); });
        restart();
    }

    /* Logout confirmation ---------------------------------------------------- */
    let pendingLogoutForm = null;
    $(document).on('submit', '.js-logout-form', function (event) {
        if (!document.getElementById('logoutModal')) { return true; }
        event.preventDefault();
        pendingLogoutForm = this;
        bootstrap.Modal.getOrCreateInstance('#logoutModal').show();
    });
    $(document).on('click', '#confirmLogout', function () {
        if (!pendingLogoutForm) { return; }
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Logging out...');
        pendingLogoutForm.submit();
    });

    /* Copy / paste order numbers --------------------------------------------- */
    $(document).on('click', '[data-copy]', function () {
        const value = String($(this).data('copy'));
        const label = $(this).data('copy-label');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(value).then(function () { odinToast(label ? label + ' copied.' : 'Order number ' + value + ' copied.'); });
        }
    });

    $('#pasteOrderNumber').on('click', function () {
        const $input = $('#orderNumber');
        if (!navigator.clipboard || !navigator.clipboard.readText) {
            $input.trigger('focus');
            odinToast('Press Ctrl + V to paste your order number.');
            return;
        }
        navigator.clipboard.readText()
            .then(function (text) { $input.val($.trim(text).replace(/^#/, '').toUpperCase()).trigger('focus'); })
            .catch(function () { $input.trigger('focus'); odinToast('Press Ctrl + V to paste your order number.'); });
    });

    $('#orderNumber').on('input', function () {
        const clean = $(this).val().replace(/\s+/g, '').replace(/^#/, '');
        if (clean !== $(this).val()) { $(this).val(clean); }
    });

    /* Product ratings & reviews ------------------------------------------------ */
    const $reviews = $('[data-reviews]');
    if ($reviews.length) {
        const starLabels = { 1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Very good', 5: 'Excellent' };
        const $form = $reviews.find('[data-review-form]');
        const $list = $reviews.find('[data-review-list]');
        const $more = $reviews.find('[data-review-more]');
        const $hint = $reviews.find('[data-star-hint]');
        const $comment = $form.find('textarea[name="comment"]');
        const $alert = $form.find('[data-review-alert]');
        const $submit = $form.find('[data-review-submit]');
        const commentMin = parseInt($comment.attr('minlength'), 10) || 10;
        const commentMax = parseInt($comment.attr('maxlength'), 10) || 2000;

        // Same markup as resources/views/partials/stars.blade.php
        const starsHtml = function (rating, extraClass) {
            const rounded = Math.round(rating * 2) / 2;
            const $stars = $('<span class="stars" role="img"></span>').addClass(extraClass || '')
                .attr('aria-label', Number(rating).toFixed(1) + ' out of 5 stars');
            for (let star = 1; star <= 5; star++) {
                $stars.append($('<i class="bi"></i>').addClass(rounded >= star ? 'bi-star-fill' : (rounded >= star - 0.5 ? 'bi-star-half' : 'bi-star')));
            }
            return $stars;
        };

        const plural = function (count, word) { return count.toLocaleString() + ' ' + word + (count === 1 ? '' : 's'); };

        // Same markup as resources/views/partials/review-card.blade.php. User text is always inserted with .text().
        const reviewCard = function (review) {
            const $body = $('<div class="review-body"></div>');
            const $top = $('<div class="review-top"></div>').append($('<strong></strong>').text(review.user.name));
            if (review.is_mine) { $top.append('<span class="review-you">Your review</span>'); }
            $top.append($('<time></time>').attr('datetime', review.created_at).text(review.date));
            $body.append($top, starsHtml(review.rating), $('<p></p>').text(review.comment));
            if (review.is_edited) { $body.append('<small class="review-edited">Edited</small>'); }

            return $('<article class="review-card"></article>').toggleClass('mine', review.is_mine)
                .append($('<div class="review-avatar" aria-hidden="true"></div>').text(review.user.name.charAt(0).toUpperCase()), $body);
        };

        const renderSummary = function (summary) {
            const average = Number(summary.average).toFixed(1);
            $reviews.find('[data-rating-average]').text(average);
            $reviews.find('[data-rating-stars]').empty().append(starsHtml(summary.average, 'lg'));
            $reviews.find('[data-rating-count]').text(summary.count ? 'Based on ' + plural(summary.count, 'review') : 'No reviews yet');
            $reviews.find('[data-review-total]').text('(' + summary.count.toLocaleString() + ')');
            $.each(summary.distribution, function (_, row) {
                const $row = $reviews.find('[data-rating-row="' + row.stars + '"]');
                $row.find('.rating-bar').attr('aria-valuenow', row.percent).find('i').css('width', row.percent + '%');
                $row.find('[data-count]').text(row.count);
                $row.find('[data-percent]').text('(' + row.percent + '%)');
            });

            // Small rating line under the product title
            const $inline = $('[data-rating-inline]');
            $inline.find('.stars').replaceWith(starsHtml(summary.average));
            $inline.find('[data-inline-average]').text(average).toggleClass('d-none', !summary.count);
            $inline.find('[data-inline-count]').text(summary.count ? '(' + plural(summary.count, 'review') + ')' : 'No reviews yet - write the first one');
        };

        const loadReviews = function (page, replace) {
            const $spinner = $more.find('.spinner-border');
            if (replace) { $list.addClass('is-loading'); } else { $more.prop('disabled', true); $spinner.removeClass('d-none'); }

            return $.getJSON($reviews.data('list-url'), { page: page }).done(function (response) {
                const cards = $.map(response.data, reviewCard);
                if (replace) { $list.empty(); }
                $list.append(cards);
                $reviews.find('[data-review-empty]').toggleClass('d-none', response.meta.total > 0);
                $more.toggleClass('d-none', response.meta.current_page >= response.meta.last_page).data('next-page', response.meta.current_page + 1);
            }).fail(function () {
                odinToast('Could not load reviews. Please try again.', 'error');
            }).always(function () {
                $list.removeClass('is-loading');
                $more.prop('disabled', false);
                $spinner.addClass('d-none');
            });
        };

        $more.on('click', function () { loadReviews($more.data('next-page'), false); });

        // Interactive stars: hover previews a rating, click keeps it selected (CSS does the colouring).
        const showHint = function (value) {
            $hint.text(value ? starLabels[value] : 'Select a rating').toggleClass('active', !!value);
        };
        const selectedRating = function () { return parseInt($form.find('input[name="rating"]:checked').val(), 10) || 0; };

        $form.find('[data-star-input] label')
            .on('mouseenter', function () { showHint($(this).data('value')); })
            .on('mouseleave', function () { showHint(selectedRating()); });
        $form.on('change', 'input[name="rating"]', function () {
            showHint(selectedRating());
            setError('rating', '');
        });
        showHint(selectedRating());

        // Comment character counter
        const updateCount = function () {
            $form.find('[data-comment-count]').text(($comment.val() || '').length + ' / ' + commentMax);
        };
        $comment.on('input', function () { updateCount(); setError('comment', ''); });
        updateCount();

        function setError(field, message) {
            $form.find('[data-error="' + field + '"]').text(message);
            if (field === 'rating') { $form.find('[data-star-input]').toggleClass('is-invalid', !!message); }
            if (field === 'comment') { $comment.toggleClass('is-invalid', !!message); }
        }

        const showAlert = function (message, type) {
            $alert.removeClass('success error').addClass(type).text(message).prop('hidden', false);
        };

        const setLoading = function (loading) {
            $submit.prop('disabled', loading).find('.spinner-border').toggleClass('d-none', !loading);
            $submit.find('[data-label]').text(loading ? 'Submitting...' : ($form.data('has-review') ? 'Update Review' : 'Submit Review'));
        };

        const validate = function () {
            const comment = $comment.val().trim();
            let valid = true;
            if (!selectedRating()) { setError('rating', 'Please select a star rating.'); valid = false; }
            if (!comment) { setError('comment', 'Please write a few words about the product.'); valid = false; }
            else if (comment.length < commentMin) { setError('comment', 'Your review must be at least ' + commentMin + ' characters.'); valid = false; }
            else if (!/[a-zÀ-￿]/i.test(comment)) { setError('comment', 'Please write your review in words.'); valid = false; }
            return valid;
        };

        $form.on('submit', function (event) {
            event.preventDefault();
            $alert.prop('hidden', true);
            if (!validate()) { return; }

            setLoading(true);
            $.ajax({
                url: $reviews.data('store-url'),
                method: 'POST',
                data: { rating: selectedRating(), comment: $comment.val().trim() }
            }).done(function (response) {
                showAlert(response.message, 'success');
                odinToast(response.message);
                renderSummary(response.summary);
                loadReviews(1, true).done(function () {
                    $list.find('.review-card.mine').first().addClass('is-new');
                });
                $form.data('has-review', true);
                $reviews.find('[data-review-form-title]').text('Edit your review');
                $reviews.find('[data-review-form-note]').text('You have already reviewed this product. Changes replace your earlier review.');
            }).fail(function (xhr) {
                const response = xhr.responseJSON || {};
                if (xhr.status === 422 && response.errors) {
                    $.each(response.errors, function (field, messages) { setError(field, messages[0]); });
                    showAlert('Please fix the highlighted fields.', 'error');
                } else if (xhr.status === 401) {
                    showAlert('Please log in to write a review.', 'error');
                } else if (xhr.status === 419) {
                    showAlert('Your session has expired. Please refresh the page and try again.', 'error');
                } else if (xhr.status === 429) {
                    showAlert('Too many attempts. Please wait a minute and try again.', 'error');
                } else {
                    showAlert(response.message || 'Something went wrong. Please try again.', 'error');
                }
            }).always(function () {
                setLoading(false);
            });
        });
    }

    /* Back to top ------------------------------------------------------------ */
    const $top = $('.back-to-top');
    $(window).on('scroll', function () { $top.css('display', $(window).scrollTop() > 500 ? 'flex' : 'none'); });
    $top.on('click', function () { $('html, body').animate({ scrollTop: 0 }, 300); });
});
