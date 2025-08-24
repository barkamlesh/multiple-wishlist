
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/template',
    'Magento_Customer/js/customer-data',
    'text!Kamlesh_MultipleWishlist/templates/hidden-inputs.html',
], function($, modal, $t, mageTemplate, customerData, inputsTemplate) {
    'use strict';

    $.widget('kamlesh.multipleWishlistModal', {
        options: {
            hiddenInputs: '.multiple-wishlist-hidden',
            modalOptions: {
                title: $t('Add to wishlist'),
                modalClass: 'multiple-wishlist-modal-popup',
                responsive: true,
                form: '#multiple-wishlist-form',
                trigger: '[data-action=add-to-wishlist]',
                buttons: [
                    {
                        text: $t('Close')
                    },
                    {
                        text: $t('Add Item to the Wishlist'),
                        class: 'action primary',
                        click: function () {
                            $(this.options.form).submit();
                        }
                    }
                ]
            },
        },

        _create: function() {
            this._prepareElements();

            // Inject scoped CSS once to make this modal slightly narrower without
            // affecting other modals or global styles. Uses the modalClass set
            // in options to target only this widget's popup.
            var styleId = 'kamlesh-multiple-wishlist-modal-style';
            if (!document.getElementById(styleId)) {
                try {
                    var css = '.multiple-wishlist-modal-popup .modal-inner-wrap { max-width: 620px; width: 90%; }\n' +
                              '.multiple-wishlist-modal-popup .modal { box-sizing: border-box; }';
                    var styleEl = document.createElement('style');
                    styleEl.type = 'text/css';
                    styleEl.id = styleId;
                    styleEl.appendChild(document.createTextNode(css));
                    document.getElementsByTagName('head')[0].appendChild(styleEl);
                } catch (e) {
                    // Non-fatal: if injecting style fails, continue without blocking modal
                    console.warn('[MultipleWishlist] Failed to inject scoped modal CSS', e);
                }
            }

            modal(this.options.modalOptions, $(this.element));
        },

        /**
         * Remove default data attribute and create new from in the modal
         * @private
         */
        _prepareElements: function () {
            let widget = this;

            // Helper: wait for UI component content to render inside the form
            function waitForContent(formEl, callback, maxAttempts, interval) {
                var attempts = 0;
                maxAttempts = maxAttempts || 10; // ~500ms by default
                interval = interval || 50;
                var iv = setInterval(function () {
                    attempts++;
                    var present = formEl.find('.fieldset-multiple-wishlist, .multiple-wishlist-item, .field-multiple-wishlist').length > 0;
                    if (present || attempts >= maxAttempts) {
                        clearInterval(iv);
                        callback(present);
                    }
                }, interval);
            }

            // Initial conversion of existing buttons (present at DOM ready)
            $(widget.options.modalOptions.trigger).each(function () {
                const post = $(this).data('post');
                if (post) {
                    $(this).attr('data-multiple', JSON.stringify(post));
                }
            });

            $(document).on('click', widget.options.modalOptions.trigger, function (e) {
                e.preventDefault();

                let postData = $(this).data('multiple');
                // (debug logs removed)

                // If data-multiple missing (button loaded later via AJAX) try to build it now
                if (!postData) {
                    const originalPost = $(this).data('post') || $(this).attr('data-post');
                    if (originalPost) {
                        if (typeof originalPost === 'string') {
                            try {
                                postData = JSON.parse(originalPost);
                            } catch (e) {
                                console.error('[MultipleWishlist] Failed to parse late data-post JSON', e, originalPost);
                            }
                        } else {
                            postData = originalPost;
                        }
                        if (postData) {
                            $(this).attr('data-multiple', JSON.stringify(postData));
                        }
                    }
                }

                // Ensure postData is an object (jQuery may leave it as a raw string depending on timing)
                if (typeof postData === 'string') {
                    try {
                        postData = JSON.parse(postData);
                    } catch (err) {
                        // Abort gracefully – prevents opening an empty grey modal
                        // and hints developer in console.
                        console.error('[MultipleWishlist] Failed to parse data-multiple JSON', err, postData);
                        return;
                    }
                }

                if (!postData || typeof postData !== 'object') {
                    console.warn('[MultipleWishlist] Missing post data for wishlist action; modal will not open.');
                    return;
                }

                // Guard against unexpected structure
                if (!postData.action || !postData.data) {
                    console.warn('[MultipleWishlist] Incomplete wishlist post structure', postData);
                }

                let open = function () {
                    let inputsHidden, multipleWishlistForm = $(widget.element).find('form');
                    try {
                        inputsHidden = $(mageTemplate(inputsTemplate, { data: postData }));
                    } catch (tmplErr) {
                        console.error('[MultipleWishlist] Failed to render hidden inputs template', tmplErr);
                        return; // Don't open modal with broken content
                    }

                    multipleWishlistForm.attr('action', postData.action || '#');
                    multipleWishlistForm.find(widget.options.hiddenInputs).remove();
                    multipleWishlistForm.append(inputsHidden);

                    // If UI component templates didn't render anything, append a small fallback
                    var contentPresent = multipleWishlistForm.find('.fieldset-multiple-wishlist, .multiple-wishlist-item, .field-multiple-wishlist').length > 0;
                    if (!contentPresent) {
                        console.warn('[MultipleWishlist] UI content missing in modal — appending fallback markup');
                        var fallback = '<fieldset class="fieldset fieldset-multiple-wishlist">' +
                            '<div class="field field-multiple-wishlist">' +
                            '<div class="label" style="text-align:left; margin-bottom:6px;">' +
                            'New Wishlist Name' +
                            '</div>' +
                            '<input type="text" name="muttiple_wishlist_name" style="width:100%; max-width:360px; box-sizing:border-box;" />' +
                            '</div>' +
                            '</fieldset>';
                        multipleWishlistForm.append(fallback);
                    }

                    $(widget.element).modal('openModal');
                };

                // Ensure customer-data section is present (so knockout templates have data ready)
                try {
                    let mwData = customerData.get('multiple-wishlist')();
                    if (!mwData || (!mwData.items && !mwData.createUrl)) {
                        console.warn('[MultipleWishlist] customerData empty or incomplete, reloading sections');
                        customerData.reload(['multiple-wishlist'], true).done(function() {
                            // After reload, inspect data and then open
                            // customerData reloaded
                            // Wait for UI content to render before opening modal
                            waitForContent($(widget.element).find('form'), function() {
                                open();
                            });
                        });
                        return;
                    }
                } catch (e2) {
                    console.warn('[MultipleWishlist] customerData access issue', e2);
                }
                // Before opening, render inputs and inspect DOM
                try {
                    var testInputs = mageTemplate(inputsTemplate, { data: postData });
                } catch (renderErr) {
                    console.error('[MultipleWishlist] template render test failed', renderErr);
                }
                // Wait briefly for UI/knockout to render template content, then open
                waitForContent($(widget.element).find('form'), function (present) {
                    if (!present) {
                        console.warn('[MultipleWishlist] UI content not present after wait; fallback will be appended if needed');
                    }
                    open();
                });
            });

            $(widget.options.modalOptions.trigger).removeAttr('data-post');
        },
    })

    return $.kamlesh.multipleWishlistModal;
});
