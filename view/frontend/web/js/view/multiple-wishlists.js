
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'underscore'
], function (Component, customerData, $, _) {
    'use strict';

    return Component.extend({
        /**
         * Checks if there are multiple wishlists
         * @returns {boolean}
         */
        hasWishlists: function () {
            let wishlist = customerData.get('multiple-wishlist')();

            return !!(wishlist.items && _.size(wishlist.items));
        },

        /**
         * Returns wishlists
         * @returns {Object}
         */
        getMultipleWishlists: function () {
            return customerData.get('multiple-wishlist')().items;
        },
    });
});
