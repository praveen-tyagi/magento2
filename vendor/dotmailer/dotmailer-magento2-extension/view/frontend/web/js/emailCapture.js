define(['jquery', 'domReady!'], function ($) {
    'use strict';

    var previousEmail;

    /**
     * Email validation
     * @param {String} sEmail
     * @returns {Boolean}
     */
    function validateEmail(sEmail) {
        return /^([+\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/
            .test(sEmail);
    }

    /**
     * Send captured email
     *
     * @param selectors
     * @param url
     */
    function emailCapture(selectors, url, captureEnabled) {
        $(document).on('blur', selectors.join(', '), function() {
            var email = $(this).val();
            if (!email || email === previousEmail || !validateEmail(email)) {
                return;
            }

            //Identify the user
            if (typeof window.dmPt !== 'undefined') {
                window.dmPt('identify', email);
            }

            //Check if Email Capture is Enabled
            if (captureEnabled !== "0") {
                $.post(url, {
                    email: email
                });
            }
        });
    }

    /**
     * Exported/return email capture
     * @param {Object} config
     */
    return function (config) {
        let selectors = [];
        switch (config.type) {
            case 'checkout' :
                selectors.push('input[id="customer-email"]');
                break;

            case 'newsletter' :
                selectors.push('input[id="newsletter"]');
                break;
        }

        if (selectors.length !== 0) {
            emailCapture(selectors, config.url, config.enabled);
        }
    };
});
