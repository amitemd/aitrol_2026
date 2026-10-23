<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
    <title>Configuración principal de {{config('data.info')->nombre_info}} V.{{config('data.info')->version_info}}</title>
    <link rel="preload" as="font" href="https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.woff?-fvbane" crossorigin>
    <link rel="preload" as="font" href="https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLDz8Z1xlFQ.woff2" crossorigin>
    <link rel="preload" as="font" href="https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2" crossorigin>
    <link rel="preload" as="font" href="https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2" crossorigin>
    <style id="wpr-usedcss">
        :where(.wp-block-button__link) {
            box-shadow: none;
            text-decoration: none;
            border-radius: 9999px;
            padding: calc(.667em + 2px) calc(1.333em + 2px)
        }

        :where(.wp-block-columns.has-background) {
            padding: 1.25em 2.375em
        }

        :where(.wp-block-post-comments input[type=submit]) {
            border: none
        }

        :where(.wp-block-file__button) {
            border-radius: 2em;
            padding: .5em 1em
        }

        :where(.wp-block-file__button):is(a):active,
        :where(.wp-block-file__button):is(a):focus,
        :where(.wp-block-file__button):is(a):hover,
        :where(.wp-block-file__button):is(a):visited {
            box-shadow: none;
            color: #fff;
            opacity: .85;
            text-decoration: none
        }

        ul {
            box-sizing: border-box;
            padding-left: 2rem;
            margin-top: 0;
            margin-bottom: 1rem;
            padding: 0;
            margin: 0
        }

        :where(.wp-block-navigation .wp-block-navigation__submenu-container .wp-block-navigation-item a:not(.wp-element-button)),
        :where(.wp-block-navigation .wp-block-navigation__submenu-container .wp-block-navigation-submenu a:not(.wp-element-button)),
        :where(.wp-block-navigation.has-background .wp-block-navigation-item a:not(.wp-element-button)),
        :where(.wp-block-navigation.has-background .wp-block-navigation-submenu a:not(.wp-element-button)) {
            padding: .5em 1em
        }

        :where(p.has-text-color:not(.has-link-color)) a {
            color: inherit
        }

        :where(.wp-block-search__button) {
            border: 1px solid #ccc;
            padding: .375em .625em
        }

        :where(.wp-block-search__button-inside .wp-block-search__inside-wrapper) {
            padding: 4px;
            border: 1px solid #949494
        }

        :where(.wp-block-search__button-inside .wp-block-search__inside-wrapper) .wp-block-search__input {
            border-radius: 0;
            border: none;
            padding: 0 0 0 .25em
        }

        :where(.wp-block-search__button-inside .wp-block-search__inside-wrapper) .wp-block-search__input:focus {
            outline: 0
        }

        :where(.wp-block-search__button-inside .wp-block-search__inside-wrapper) :where(.wp-block-search__button) {
            padding: .125em .5em
        }

        :root {
            --wp--preset--font-size--normal: 16px;
            --wp--preset--font-size--huge: 42px;
            --bs-blue: #0d6efd;
            --bs-indigo: #6610f2;
            --bs-purple: #6f42c1;
            --bs-pink: #d63384;
            --bs-red: #dc3545;
            --bs-orange: #fd7e14;
            --bs-yellow: #ffc107;
            --bs-green: #198754;
            --bs-teal: #20c997;
            --bs-cyan: #0dcaf0;
            --bs-white: #fff;
            --bs-gray: #6c757d;
            --bs-gray-dark: #343a40;
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
            --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, .15), rgba(255, 255, 255, 0))
        }

        html :where(.has-border-color) {
            border-style: solid
        }

        html :where([style*=border-top-color]) {
            border-top-style: solid
        }

        html :where([style*=border-right-color]) {
            border-right-style: solid
        }

        html :where([style*=border-bottom-color]) {
            border-bottom-style: solid
        }

        html :where([style*=border-left-color]) {
            border-left-style: solid
        }

        html :where([style*=border-width]) {
            border-style: solid
        }

        html :where([style*=border-top-width]) {
            border-top-style: solid
        }

        html :where([style*=border-right-width]) {
            border-right-style: solid
        }

        html :where([style*=border-bottom-width]) {
            border-bottom-style: solid
        }

        html :where([style*=border-left-width]) {
            border-left-style: solid
        }

        html :where(img[class*=wp-image-]) {
            height: auto;
            max-width: 100%
        }

        body {
            --wp--preset--color--black: #000000;
            --wp--preset--color--cyan-bluish-gray: #abb8c3;
            --wp--preset--color--white: #ffffff;
            --wp--preset--color--pale-pink: #f78da7;
            --wp--preset--color--vivid-red: #cf2e2e;
            --wp--preset--color--luminous-vivid-orange: #ff6900;
            --wp--preset--color--luminous-vivid-amber: #fcb900;
            --wp--preset--color--light-green-cyan: #7bdcb5;
            --wp--preset--color--vivid-green-cyan: #00d084;
            --wp--preset--color--pale-cyan-blue: #8ed1fc;
            --wp--preset--color--vivid-cyan-blue: #0693e3;
            --wp--preset--color--vivid-purple: #9b51e0;
            --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg, rgba(6, 147, 227, 1) 0%, rgb(155, 81, 224) 100%);
            --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg, rgb(122, 220, 180) 0%, rgb(0, 208, 130) 100%);
            --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg, rgba(252, 185, 0, 1) 0%, rgba(255, 105, 0, 1) 100%);
            --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg, rgba(255, 105, 0, 1) 0%, rgb(207, 46, 46) 100%);
            --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg, rgb(238, 238, 238) 0%, rgb(169, 184, 195) 100%);
            --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg, rgb(74, 234, 220) 0%, rgb(151, 120, 209) 20%, rgb(207, 42, 186) 40%, rgb(238, 44, 130) 60%, rgb(251, 105, 98) 80%, rgb(254, 248, 76) 100%);
            --wp--preset--gradient--blush-light-purple: linear-gradient(135deg, rgb(255, 206, 236) 0%, rgb(152, 150, 240) 100%);
            --wp--preset--gradient--blush-bordeaux: linear-gradient(135deg, rgb(254, 205, 165) 0%, rgb(254, 45, 45) 50%, rgb(107, 0, 62) 100%);
            --wp--preset--gradient--luminous-dusk: linear-gradient(135deg, rgb(255, 203, 112) 0%, rgb(199, 81, 192) 50%, rgb(65, 88, 208) 100%);
            --wp--preset--gradient--pale-ocean: linear-gradient(135deg, rgb(255, 245, 203) 0%, rgb(182, 227, 212) 50%, rgb(51, 167, 181) 100%);
            --wp--preset--gradient--electric-grass: linear-gradient(135deg, rgb(202, 248, 128) 0%, rgb(113, 206, 126) 100%);
            --wp--preset--gradient--midnight: linear-gradient(135deg, rgb(2, 3, 129) 0%, rgb(40, 116, 252) 100%);
            --wp--preset--duotone--dark-grayscale: url('#wp-duotone-dark-grayscale');
            --wp--preset--duotone--grayscale: url('#wp-duotone-grayscale');
            --wp--preset--duotone--purple-yellow: url('#wp-duotone-purple-yellow');
            --wp--preset--duotone--blue-red: url('#wp-duotone-blue-red');
            --wp--preset--duotone--midnight: url('#wp-duotone-midnight');
            --wp--preset--duotone--magenta-yellow: url('#wp-duotone-magenta-yellow');
            --wp--preset--duotone--purple-green: url('#wp-duotone-purple-green');
            --wp--preset--duotone--blue-orange: url('#wp-duotone-blue-orange');
            --wp--preset--font-size--small: 13px;
            --wp--preset--font-size--medium: 20px;
            --wp--preset--font-size--large: 36px;
            --wp--preset--font-size--x-large: 42px;
            --wp--preset--spacing--20: 0.44rem;
            --wp--preset--spacing--30: 0.67rem;
            --wp--preset--spacing--40: 1rem;
            --wp--preset--spacing--50: 1.5rem;
            --wp--preset--spacing--60: 2.25rem;
            --wp--preset--spacing--70: 3.38rem;
            --wp--preset--spacing--80: 5.06rem;
            margin: 0;
            font-family: var(--bs-font-sans-serif);
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent
        }

        :where(.is-layout-flex) {
            gap: .5em
        }

        :where(.wp-block-columns.is-layout-flex) {
            gap: 2em
        }

        *,
        ::after,
        ::before {
            box-sizing: border-box
        }

        @media (prefers-reduced-motion:no-preference) {
            :root {
                scroll-behavior: smooth
            }
        }

        h2,
        h4,
        h5 {
            margin-top: 0;
            margin-bottom: .5rem;
            font-weight: 500;
            line-height: 1.2
        }

        h2 {
            font-size: calc(1.325rem + .9vw)
        }

        @media (min-width:1200px) {
            h2 {
                font-size: 2rem
            }
        }

        h4 {
            font-size: calc(1.275rem + .3vw)
        }

        @media (min-width:1200px) {
            h4 {
                font-size: 1.5rem
            }
        }

        h5 {
            font-size: 1.25rem
        }

        ul ul {
            margin-bottom: 0
        }

        a {
            color: #0d6efd;
            text-decoration: underline
        }

        a:hover {
            color: #0a58ca
        }

        a:not([href]):not([class]),
        a:not([href]):not([class]):hover {
            color: inherit;
            text-decoration: none
        }

        img,
        svg {
            vertical-align: middle
        }

        table {
            caption-side: bottom;
            border-collapse: collapse
        }

        tbody,
        tr {
            border-color: inherit;
            border-style: solid;
            border-width: 0
        }

        label {
            display: inline-block
        }

        button {
            border-radius: 0
        }

        button:focus {
            outline: dotted 1px;
            outline: -webkit-focus-ring-color auto 5px
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit
        }

        button,
        select {
            text-transform: none
        }

        [role=button],
        [type=button]:not(:disabled),
        [type=submit]:not(:disabled),
        button:not(:disabled) {
            cursor: pointer
        }

        select {
            word-wrap: normal
        }

        [type=button],
        [type=submit],
        button {
            -webkit-appearance: button
        }

        ::-moz-focus-inner {
            padding: 0;
            border-style: none
        }

        textarea {
            resize: vertical
        }

        fieldset {
            min-width: 0;
            padding: 0;
            margin: 0;
            border: 0
        }

        legend {
            float: left;
            width: 100%;
            padding: 0;
            margin-bottom: .5rem;
            font-size: calc(1.275rem + .3vw);
            line-height: inherit
        }

        legend+* {
            clear: left
        }

        ::-webkit-datetime-edit-day-field,
        ::-webkit-datetime-edit-fields-wrapper,
        ::-webkit-datetime-edit-hour-field,
        ::-webkit-datetime-edit-minute,
        ::-webkit-datetime-edit-month-field,
        ::-webkit-datetime-edit-text,
        ::-webkit-datetime-edit-year-field {
            padding: 0
        }

        ::-webkit-inner-spin-button {
            height: auto
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none
        }

        ::-webkit-color-swatch-wrapper {
            padding: 0
        }

        ::file-selector-button {
            font: inherit
        }

        ::-webkit-file-upload-button {
            font: inherit;
            -webkit-appearance: button
        }

        iframe {
            border: 0
        }

        progress {
            vertical-align: baseline
        }

        [hidden] {
            display: none !important
        }

        .container {
            width: 100%;
            padding-right: var(--bs-gutter-x, .75rem);
            padding-left: var(--bs-gutter-x, .75rem);
            margin-right: auto;
            margin-left: auto;
            position: relative;
            z-index: 9
        }

        @media (min-width:576px) {
            .container {
                max-width: 540px
            }
        }

        @media (min-width:768px) {
            .container {
                max-width: 720px
            }
        }

        @media (min-width:992px) {
            .container {
                max-width: 960px
            }
        }

        @media (min-width:1200px) {
            legend {
                font-size: 1.5rem
            }
        }

        @media (min-width:1400px) {
            .container {
                max-width: 1320px
            }
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(var(--bs-gutter-y) * -1);
            margin-right: calc(var(--bs-gutter-x)/ -2);
            margin-left: calc(var(--bs-gutter-x)/ -2)
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x)/ 2);
            padding-left: calc(var(--bs-gutter-x)/ 2);
            margin-top: var(--bs-gutter-y)
        }

        @media (min-width:576px) {
            .col-sm-6 {
                flex: 0 0 auto;
                width: 50%
            }
        }

        @media (min-width:768px) {
            .col-md-6 {
                flex: 0 0 auto;
                width: 50%
            }
        }

        @media (min-width:992px) {
            .col-lg-3 {
                flex: 0 0 auto;
                width: 25%
            }

            .col-lg-4 {
                flex: 0 0 auto;
                width: 33.3333333333%
            }

            .col-lg-6 {
                flex: 0 0 auto;
                width: 50%
            }

            .col-lg-8 {
                flex: 0 0 auto;
                width: 66.6666666667%
            }

            .col-lg-9 {
                flex: 0 0 auto;
                width: 75%
            }

            .col-lg-12 {
                flex: 0 0 auto;
                width: 100%
            }
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, .05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, .1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, .075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6
        }

        .table>:not(caption)>*>* {
            padding: .5rem;
            background-color: var(--bs-table-bg);
            background-image: linear-gradient(var(--bs-table-accent-bg), var(--bs-table-accent-bg));
            border-bottom-width: 1px
        }

        .table>tbody {
            vertical-align: inherit
        }

        .table>:not(:last-child)>:last-child>* {
            border-bottom-color: currentColor
        }

        .progress {
            display: flex;
            height: 1rem;
            overflow: hidden;
            font-size: .75rem;
            background-color: #e9ecef;
            border-radius: .25rem
        }

        .d-flex {
            display: flex !important
        }

        .border {
            border: 1px solid #dee2e6 !important
        }

        .justify-content-center {
            justify-content: center !important
        }

        @font-face {
            font-display: swap;
            font-family: themify;
            src: url('https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.eot?-fvbane');
            src: url('https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.eot?#iefix-fvbane') format('embedded-opentype'), url('https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.woff?-fvbane') format('woff'), url('https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.ttf?-fvbane') format('truetype'), url('https://149841302.v2.pressablecdn.com/wp-content/themes/theme-promo/assets/fonts/themify.svg?-fvbane#themify') format('svg');
            font-weight: 400;
            font-style: normal
        }

        [class*=" ti-"],
        [class^=ti-] {
            font-family: themify;
            speak: none;
            font-style: normal;
            font-weight: 400;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        .ti-shopping-cart:before {
            content: "\e60d"
        }

        body,
        html {
            height: 100%;
            font-family: Mulish, sans-serif;
            -webkit-font-smoothing: antialiased
        }

        h2,
        h4,
        h5 {
            margin: 0;
            color: #111;
            font-family: Mulish, sans-serif
        }

        h2 {
            font-size: 40px
        }

        h4 {
            font-size: 24px
        }

        h5 {
            font-size: 18px
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 15px;
            color: #687083;
            line-height: 1.7;
            margin-bottom: 15px
        }

        img {
            max-width: 100%;
            height: auto
        }

        button:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: 0
        }

        a:-webkit-any-link,
        a:focus,
        a:hover {
            text-decoration: none;
            outline: 0
        }

        @media (min-width:1200px) {
            .container {
                max-width: 1140px
            }
        }

        section {
            overflow: hidden;
            position: relative
        }

        .set-bg {
            background-repeat: no-repeat;
            background-size: cover;
            background-position: top center
        }

        .spad {
            padding-top: 100px;
            padding-bottom: 100px
        }

        .primary-btn {
            display: inline-block;
            font-size: 16px;
            color: #2c4dfc;
            font-family: Poppins, sans-serif;
            border: 1px solid #3a72ff;
            padding: 18px 66px 16px;
            border-radius: 50px
        }

        .header {
            background: #161d31;
            padding: 20px 0 25px;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            z-index: 1
        }

        @media only screen and (max-width:61.93em) {
            .header {
                padding: 30px 0
            }
        }

        @media only screen and (min-width:103.125em) {
            .header .container {
                max-width: 1600px
            }
        }

        .header__logo {
            padding-top: 8px
        }

        .header__logo a {
            display: inline-block
        }

        .header__nav {
            text-align: right
        }

        .header__nav nav {
            display: inline-block;
            margin-right: 60px
        }

        .header__nav nav ul li {
            list-style: none;
            display: inline-block;
            margin-right: 55px
        }

        .header__nav nav ul li.active a {
            color: #fff
        }

        .header__nav nav ul li:last-child {
            margin-right: 0
        }

        .header__nav nav ul li a {
            display: block;
            font-size: 16px;
            color: #9a9fad;
            font-family: Poppins, sans-serif;
            padding: 10px 5px;
            -webkit-transition: none, .3s;
            -moz-transition: none, .3s;
            -ms-transition: none, .3s;
            -o-transition: none, .3s;
            transition: all, .3s
        }

        .header__nav nav ul li a:hover {
            color: #fff
        }

        .header__nav .header__btn {
            display: inline-block
        }

        .header__nav .header__btn a {
            display: inline-block;
            font-size: 16px;
            color: #fff;
            border: 1px solid #fff;
            padding: 10px 32px;
            border-radius: 50px
        }

        .canvas__open,
        .offcanvas-menu-wrapper {
            display: none
        }

        @media only screen and (max-width:61.93em) {
            .header__logo {
                padding-top: 0
            }

            .header__nav {
                display: none
            }

            .canvas__open {
                display: block;
                font-size: 22px;
                color: #fff;
                height: 35px;
                width: 35px;
                line-height: 35px;
                text-align: center;
                border: 1px solid #fff;
                border-radius: 2px;
                cursor: pointer;
                position: absolute;
                right: 15px;
                top: -4px
            }

            .offcanvas-menu-overlay {
                position: fixed;
                left: 0;
                top: 0;
                height: 100%;
                width: 100%;
                background: rgba(0, 0, 0, .7);
                content: "";
                z-index: 98;
                -webkit-transition: none, .5s;
                -moz-transition: none, .5s;
                -ms-transition: none, .5s;
                -o-transition: none, .5s;
                transition: all, .5s;
                visibility: hidden
            }

            .offcanvas-menu-overlay.active {
                visibility: visible
            }

            .offcanvas-menu-wrapper {
                position: fixed;
                left: -300px;
                width: 300px;
                height: 100%;
                background: #fff;
                padding: 50px 20px 30px 30px;
                display: block;
                z-index: 99;
                overflow-y: auto;
                -webkit-transition: none, .5s;
                -moz-transition: none, .5s;
                -ms-transition: none, .5s;
                -o-transition: none, .5s;
                transition: all, .5s;
                opacity: 0
            }

            .offcanvas-menu-wrapper.active {
                opacity: 1;
                left: 0
            }

            .offcanvas__menu {
                margin-bottom: 25px
            }

            .offcanvas__menu ul li {
                list-style: none
            }

            .offcanvas__menu ul li:last-child {
                margin-right: 0
            }

            .offcanvas__menu ul li a {
                display: block;
                font-size: 16px;
                color: #111;
                font-family: Poppins, sans-serif;
                padding: 10px 5px
            }

            .offcanvas__btn {
                display: inline-block
            }

            .offcanvas__btn a {
                display: inline-block;
                font-size: 16px;
                color: #111;
                border: 1px solid #111;
                padding: 10px 32px;
                border-radius: 50px
            }
        }

        .product-demo {
            background: #fbfaf9;
            padding-top: 140px;
            padding-bottom: 160px
        }

        .demo__item {
            background: #fff;
            border: 1px solid #f0f0f0;
            margin-bottom: 30px;
            border-radius: 10px
        }

        .demo__item__pic img {
            min-width: 100%;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px
        }

        .demo__item__text {
            padding: 36px 35px 25px 30px
        }

        .demo__item__text h5 {
            margin-bottom: 20px
        }

        .demo__item__text h5 a {
            color: #1f3e64;
            font-weight: 800
        }

        .demo__item__text p {
            font-size: 14px;
            color: #687083;
            line-height: 1.8;
            margin-bottom: 30px
        }

        .demo__item__price {
            width: 50%;
            float: left
        }

        .demo__item__price h4 {
            color: #1f3e64;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 8px
        }

        .demo__item__price span {
            color: #a5abb9;
            font-size: 14px;
            display: block;
            font-style: italic
        }

        .demo__item__option {
            border-top: 1px solid #f0ddd9;
            padding-top: 22px;
            overflow: hidden;
            padding-bottom: 15px
        }

        @media only screen and (max-width:75em) {
            .header__nav nav {
                margin-right: 30px
            }

            .header__nav nav ul li {
                margin-right: 25px
            }

            .demo__item__price {
                width: 100%;
                float: none;
                margin-bottom: 15px
            }

            .demo__item__option {
                overflow: visible
            }
        }

        .demo__item__btn {
            width: 50%;
            float: left;
            text-align: right;
            padding-top: 6px
        }

        @media only screen and (max-width:75em) {
            .demo__item__btn {
                width: 100%;
                float: none;
                text-align: left
            }
        }

        .demo__item__btn .preview_btn {
            display: inline-block;
            font-size: 14px;
            color: #fff;
            background: #005fd5;
            padding: 10px 16px 8px;
            border-radius: 5px;
            -webkit-transition: none, .3s;
            -moz-transition: none, .3s;
            -ms-transition: none, .3s;
            -o-transition: none, .3s;
            transition: all, .3s
        }

        .demo__item__btn .preview_btn:hover {
            box-shadow: 0 7px 18px rgba(0, 96, 213, .44)
        }

        .demo__item__btn .cart_btn {
            display: inline-block;
            font-size: 14px;
            color: #005fd5;
            padding: 9px 12px 7px;
            border: 1px solid #005fd5;
            border-radius: 5px
        }

        .footer {
            background: #010a1f;
            padding-top: 145px
        }

        .footer__content {
            padding-bottom: 115px
        }

        .footer__about {
            margin-bottom: 35px
        }

        .footer__about .footer__logo {
            margin-bottom: 24px
        }

        .footer__about p {
            color: #707788;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 0
        }

        .footer__widget {
            margin-bottom: 35px
        }

        .footer__widget.footer__links {
            padding-left: 35px
        }

        .footer__widget h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 22px
        }

        .footer__widget ul li {
            list-style: none;
            line-height: 1.9
        }

        .footer__widget ul li a {
            font-size: 16px;
            color: #707788;
            -webkit-transition: none, .3s;
            -moz-transition: none, .3s;
            -ms-transition: none, .3s;
            -o-transition: none, .3s;
            transition: all, .3s
        }

        .footer__widget ul li a:hover {
            color: #fff
        }

        .footer__widget p {
            color: #707788;
            font-size: 16px;
            margin-bottom: 25px
        }

        .footer__widget form {
            position: relative
        }

        .footer__widget form input {
            width: 100%;
            height: 40px;
            background: #fff;
            border-radius: 5px;
            border: none;
            padding-left: 20px;
            font-size: 13px;
            color: #d8dadf
        }

        .footer__widget form input::placeholder {
            font-size: 13px;
            color: #d8dadf
        }

        .footer__widget form button {
            font-size: 15px;
            color: #fff;
            background: #0d3595;
            position: absolute;
            right: -1px;
            top: 0;
            height: 100%;
            border: none;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            padding: 0 22px
        }

        .footer__copyright {
            border-top: 1px solid rgba(255, 255, 255, .2);
            padding: 46px 0
        }

        .footer__copyright__text p {
            color: #8a91a2;
            font-size: 16px;
            font-family: Poppins, sans-serif;
            font-weight: 300;
            margin-bottom: 0
        }

        .footer__copyright__links {
            text-align: right
        }

        .footer__copyright__links .menu li:last-child {
            padding-right: 0
        }

        .footer__copyright__links .menu li {
            display: initial;
            padding-right: 12px
        }

        @media only screen and (max-width:61.93em) {
            .footer__widget.footer__links {
                padding-left: 0
            }

            .footer__copyright__text p {
                text-align: center;
                margin-bottom: 15px
            }

            .footer__copyright__links {
                text-align: center
            }
        }

        .footer__copyright__links a {
            display: inline-block;
            color: #8a91a2;
            font-size: 16px;
            font-weight: 300;
            margin-right: 34px
        }

        .footer__copyright__links a:last-child {
            margin-right: 0
        }

        .product-demo.premium-page {
            padding-top: 160px;
            padding-bottom: 40px
        }

        .product-demo.premium-page .demo__item {
            margin-bottom: 80px
        }

        .product-demo.premium-page .demo__item .demo__item__text {
            padding: 46px 50px 16px
        }

        .product-demo.premium-page .demo__item .demo__item__text .demo__item__btn .cart_btn {
            border-color: #687083;
            color: #032044
        }

        .cta {
            background: #fbfaf9;
            padding-top: 40px;
            padding-bottom: 160px
        }

        .cta__content {
            padding: 85px 50px 90px;
            border-radius: 10px
        }

        .cta__text {
            text-align: center
        }

        .cta__text h2 {
            color: #fff;
            font-weight: 800;
            font-size: 35px;
            margin-bottom: 42px
        }

        @media only screen and (max-width:21.25em) {
            .product-demo.premium-page .demo__item .demo__item__text {
                padding: 46px 30px 16px
            }

            .cta__content {
                padding: 85px 30px 90px
            }

            .cta__text h2 {
                font-size: 26px
            }
        }

        .cta__text .primary-btn {
            color: #fff;
            border-color: #fff
        }

        .breadcrumb-option {
            padding-top: 214px;
            padding-bottom: 130px
        }

        .breadcrumb__text {
            text-align: center
        }

        .breadcrumb__text h2 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 20px
        }

        .breadcrumb__text p {
            color: #fff;
            font-size: 16px;
            line-height: 1.9;
            margin-bottom: 0
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/poppins/v20/pxiByp8kv8JHgFVrLDz8Z1xlFQ.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 800;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        @font-face {
            font-family: Mulish;
            font-style: normal;
            font-weight: 900;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/mulish/v12/1Ptvg83HX_SGhgqk3wot.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD
        }

        .demo__item__price span {
            display: none
        }
    </style>
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="COnfiguración principal de {{config('data.info')->nombre_info}} V.{{config('data.info')->version_info}}" />
    <meta property="og:site_name" content="{{config('data.info')->nombre_info}} V.{{config('data.info')->version_info}}" />
    <meta property="article:modified_time" content="2022-02-07T11:53:28+00:00" />
    <meta name="twitter:card" content="summary_large_image" />
    <script type="application/ld+json" class="yoast-schema-graph">
        {
            "@context": "https://schema.org",
            "@graph": [{
                "@type": "WebPage",
                "@id": "https://adminlte.io/premium/",
                "url": "https://adminlte.io/premium/",
                "name": "COnfiguración principal de {{config('data.info')->nombre_info}} V.{{config('data.info')->version_info}}",
                "isPartOf": {
                    "@id": "https://adminlte.io/#website"
                },
                "datePublished": "2021-04-10T10:28:41+00:00",
                "dateModified": "2022-02-07T11:53:28+00:00",
                "breadcrumb": {
                    "@id": "https://adminlte.io/premium/#breadcrumb"
                },
                "inLanguage": "en-US",
                "potentialAction": [{
                    "@type": "ReadAction",
                    "target": ["https://adminlte.io/premium/"]
                }]
            }, {
                "@type": "BreadcrumbList",
                "@id": "https://adminlte.io/premium/#breadcrumb",
                "itemListElement": [{
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "item": "https://adminlte.io/"
                }, {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Premium Admin Templates"
                }]
            }, {
                "@type": "WebSite",
                "@id": "https://adminlte.io/#website",
                "url": "https://adminlte.io/",
                "name": "AdminLTE.IO",
                "description": "Most popular Bootstrap admin dashboard template in the world with 5M users. Download it now!",
                "publisher": {
                    "@id": "https://adminlte.io/#organization"
                },
                "potentialAction": [{
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "https://adminlte.io/?s={search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                }],
                "inLanguage": "en-US"
            }, {
                "@type": "Organization",
                "@id": "https://adminlte.io/#organization",
                "name": "AdminLTE.IO",
                "url": "https://adminlte.io/",
                "logo": {
                    "@type": "ImageObject",
                    "inLanguage": "en-US",
                    "@id": "https://adminlte.io/#/schema/logo/image/",
                    "url": "https://adminlte.io/wp-content/uploads/2021/03/logo.png",
                    "contentUrl": "https://adminlte.io/wp-content/uploads/2021/03/logo.png",
                    "width": 160,
                    "height": 27,
                    "caption": "AdminLTE.IO"
                },
                "image": {
                    "@id": "https://adminlte.io/#/schema/logo/image/"
                }
            }]
        }
    </script>

    <style id="wp-emoji-styles-inline-css" type="text/css"></style>
    <style id="classic-theme-styles-inline-css" type="text/css"></style>
    <style id="global-styles-inline-css" type="text/css"></style>
    <script type="text/javascript" src="https://adminlte.io/wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js" defer></script>
    <script type="text/javascript" src="https://adminlte.io/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js" defer></script>
    <script data-minify="1" type="text/javascript" src="https://adminlte.io/wp-content/cache/min/1/wp-content/themes/theme-promo/assets/js/main.js?ver=1664549490" id="main-js" defer></script>
    <link rel="https://api.w.org/" href="https://adminlte.io/wp-json/" />
    <link rel="alternate" type="application/json" href="https://adminlte.io/wp-json/wp/v2/pages/81" />
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://adminlte.io/xmlrpc.php?rsd" />
    <link rel="shortlink" href="https://adminlte.io/?p=81" />
    <link rel="alternate" type="application/json+oembed" href="https://adminlte.io/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fadminlte.io%2Fpremium%2F" />
    <link rel="alternate" type="text/xml+oembed" href="https://adminlte.io/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fadminlte.io%2Fpremium%2F&#038;format=xml" />
    <style type="text/css"></style>
    <link rel="icon" href="https://adminlte.io/wp-content/uploads/2021/03/cropped-logo-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://adminlte.io/wp-content/uploads/2021/03/cropped-logo-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://adminlte.io/wp-content/uploads/2021/03/cropped-logo-180x180.png" />
    <script src="{{url('v3/')}}/plantillas/jquery.min.js"></script>
    <style type="text/css" id="wp-custom-css"></style>
    <noscript>
        <style id="rocket-lazyload-nojs-css">
            .rll-youtube-player,
            [data-lazy-src] {
                display: none !important;
            }
        </style>
    </noscript>
</head>

<body class="page-template page-template-templates page-template-products-page page-template-templatesproducts-page-php page page-id-81 wp-custom-logo">
    <div class="offcanvas-menu-overlay"></div>
    <header class="header ">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="header__logo">
                        <a href="https://adminlte.io/">
                            <img width="160" height="27" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%20160%2027'%3E%3C/svg%3E" alt="AdminLTE.IO" data-lazy-src="https://adminlte.io/wp-content/uploads/2021/03/logo.png"><noscript><img width="160" height="27" src="https://adminlte.io/wp-content/uploads/2021/03/logo.png" alt="AdminLTE.IO"></noscript> </a>
                    </div>
                </div>
            </div>
            <div class="canvas__open"><i class="fa fa-bars"></i></div>
        </div>
    </header>
    <div class="breadcrumb-option spad set-bg 81" data-setbg="https://adminlte.io/wp-content/uploads/2021/04/hero-bg.jpg">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-6">
                    <div class="breadcrumb__text">
                        <input type="hidden" id="base_url" value="{{url('/')}}" />
                        <h2>{{config('data.info')->nombre_info.' V.'.config('data.info')->version_info}}</h2>
                        <p>Bienvenid@ {{ ucwords(strtolower(config('data.usuario')->nombre1)).' '.ucwords(strtolower(config('data.usuario')->apellido1))}} En nombre de nuestro equipo, te doy la bienvenida y espero que disfrutes trabajando con nosotros.<br />a continuación vamos hacer unas pequeñas configuraciones iniciales...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="product-demo premium-page spad">
        <input type="hidden" id="plantilla" />
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="demo__item">
                        <div class="demo__item__pic">
                            <img width="1200" height="972" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20972'%3E%3C/svg%3E" alt data-lazy-src="{{url('/')}}/v3/plantillas/admindek-html.jpg"><noscript><img width="1200" height="972" src="{{url('/')}}/v3/plantillas/admindek-html.jpg" alt=""></noscript>
                        </div>
                        <div class="demo__item__text">
                            <h5><a href="javascript:;" onclick="seleccionar('adminlte')" rel="nofollow noopener">Admin LTE HTML Dashboard</a></h5>
                            <div class=" demo__item__option">
                                <div class="demo__item__price">
                                    <h4>&nbsp;</h4>
                                    <span> &nbsp;</span>
                                </div>
                                <div class="demo__item__btn">
                                    <a href="javascript:;" onclick="seleccionar('adminlte')" rel="nofollow noopener" class=" preview_btn">Seleccionar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="demo__item">
                        <div class="demo__item__pic">
                            <img width="1200" height="972" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20972'%3E%3C/svg%3E" alt data-lazy-src="{{url('/')}}/v3/plantillas/admindek-html.jpg"><noscript><img width="1200" height="972" src="{{url('/')}}/v3/plantillas/admindek-html.jpg" alt=""></noscript>
                        </div>
                        <div class="demo__item__text">
                            <h5><a href="javascript:;" onclick="seleccionar('admindek')" rel="nofollow noopener">Admindek HTML Dashboard</a></h5>
                            <div class=" demo__item__option">
                                <div class="demo__item__price">
                                    <h4>&nbsp;</h4>
                                    <span> &nbsp;</span>
                                </div>
                                <div class="demo__item__btn">
                                    <a href="javascript:;" onclick="seleccionar('admindek')" rel="nofollow noopener" class=" preview_btn">Seleccionar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="demo__item">
                        <div class="demo__item__pic">
                            <img width="1200" height="972" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20972'%3E%3C/svg%3E" alt data-lazy-src="{{url('/')}}/v3/plantillas/user-management-dashboard.jpg"><noscript><img width="1200" height="972" src="{{url('/')}}/v3/plantillas/user-management-dashboard.jpg" alt=""></noscript>
                        </div>
                        <div class="demo__item__text">
                            <h5><a href="javascript:;" onclick="seleccionar('user')" rel="nofollow noopener">User Management Dashboard</a></h5>
                            <div class=" demo__item__option">
                                <div class="demo__item__price">
                                    <h4>&nbsp;</h4>
                                    <span> &nbsp;</span>
                                </div>
                                <div class="demo__item__btn">
                                    <a href="javascript:;" onclick="seleccionar('user')" rel="nofollow noopener" class=" preview_btn">Seleccionar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="demo__item">
                        <div class="demo__item__pic">
                            <img width="1200" height="972" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201200%20972'%3E%3C/svg%3E" alt data-lazy-src="{{url('/')}}/v3/plantillas/adminty-html-dashboard.jpg"><noscript><img width="1200" height="972" src="{{url('/')}}/v3/plantillas/adminty-html-dashboard.jpg" alt=""></noscript>
                        </div>
                        <div class="demo__item__text">
                            <h5><a href="javascript:;" onclick="seleccionar('adminty')" rel="nofollow noopener">Adminty HTML Dashboard</a></h5>
                            <div class=" demo__item__option">
                                <div class="demo__item__price">
                                    <h4>&nbsp;</h4>
                                    <span> &nbsp;</span>
                                </div>
                                <div class="demo__item__btn">
                                    <a href="javascript:;" onclick="seleccionar('adminty')" rel="nofollow noopener" class=" preview_btn">Seleccionar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer" id="footerSection">
        <div class="footer__copyright">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="footer__copyright__text">
                            <p>Copyright © 2014-2023 {{config('data.info')->nombre_info}} V.{{config('data.info')->version_info}}.{{config('data.info')->mayor_info}}.{{config('data.info')->menor_info}}. All rights reserved. Privacy Policy</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="footer__copyright__links">
                            <div class="menu-sub-footer-container">
                                <ul id="menu-sub-footer" class="menu">
                                    <li id="menu-item-253" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-253"><a href="#">Terms &#038; Conditions</a></li>
                                    <li id="menu-item-225" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-225"><a href="#">Privacy Policy</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script type="text/javascript" id="rocket-browser-checker-js-after">
        /* <![CDATA[ */
        "use strict";
        var _createClass = function() {
            function defineProperties(target, props) {
                for (var i = 0; i < props.length; i++) {
                    var descriptor = props[i];
                    descriptor.enumerable = descriptor.enumerable || !1, descriptor.configurable = !0, "value" in descriptor && (descriptor.writable = !0), Object.defineProperty(target, descriptor.key, descriptor)
                }
            }
            return function(Constructor, protoProps, staticProps) {
                return protoProps && defineProperties(Constructor.prototype, protoProps), staticProps && defineProperties(Constructor, staticProps), Constructor
            }
        }();

        function _classCallCheck(instance, Constructor) {
            if (!(instance instanceof Constructor)) throw new TypeError("Cannot call a class as a function")
        }
        var RocketBrowserCompatibilityChecker = function() {
            function RocketBrowserCompatibilityChecker(options) {
                _classCallCheck(this, RocketBrowserCompatibilityChecker), this.passiveSupported = !1, this._checkPassiveOption(this), this.options = !!this.passiveSupported && options
            }
            return _createClass(RocketBrowserCompatibilityChecker, [{
                key: "_checkPassiveOption",
                value: function(self) {
                    try {
                        var options = {
                            get passive() {
                                return !(self.passiveSupported = !0)
                            }
                        };
                        window.addEventListener("test", null, options), window.removeEventListener("test", null, options)
                    } catch (err) {
                        self.passiveSupported = !1
                    }
                }
            }, {
                key: "initRequestIdleCallback",
                value: function() {
                    !1 in window && (window.requestIdleCallback = function(cb) {
                        var start = Date.now();
                        return setTimeout(function() {
                            cb({
                                didTimeout: !1,
                                timeRemaining: function() {
                                    return Math.max(0, 50 - (Date.now() - start))
                                }
                            })
                        }, 1)
                    }), !1 in window && (window.cancelIdleCallback = function(id) {
                        return clearTimeout(id)
                    })
                }
            }, {
                key: "isDataSaverModeOn",
                value: function() {
                    return "connection" in navigator && !0 === navigator.connection.saveData
                }
            }, {
                key: "supportsLinkPrefetch",
                value: function() {
                    var elem = document.createElement("link");
                    return elem.relList && elem.relList.supports && elem.relList.supports("prefetch") && window.IntersectionObserver && "isIntersecting" in IntersectionObserverEntry.prototype
                }
            }, {
                key: "isSlowConnection",
                value: function() {
                    return "connection" in navigator && "effectiveType" in navigator.connection && ("2g" === navigator.connection.effectiveType || "slow-2g" === navigator.connection.effectiveType)
                }
            }]), RocketBrowserCompatibilityChecker
        }();
        /* ]]> */
    </script>
    <script type="text/javascript" id="rocket-preload-links-js-extra">
        /* <![CDATA[ */
        var RocketPreloadLinksConfig = {
            "excludeUris": "\/(?:.+\/)?feed(?:\/(?:.+\/?)?)?$|\/(?:.+\/)?embed\/|\/(index.php\/)?(.*)wp-json(\/.*|$)|\/refer\/|\/go\/|\/recommend\/|\/recommends\/",
            "usesTrailingSlash": "1",
            "imageExt": "jpg|jpeg|gif|png|tiff|bmp|webp|avif|pdf|doc|docx|xls|xlsx|php",
            "fileExt": "jpg|jpeg|gif|png|tiff|bmp|webp|avif|pdf|doc|docx|xls|xlsx|php|html|htm",
            "siteUrl": "https:\/\/adminlte.io",
            "onHoverDelay": "100",
            "rateThrottle": "3"
        };
        /* ]]> */
    </script>
    <script type="text/javascript" id="rocket-preload-links-js-after">
        /* <![CDATA[ */
        (function() {
            "use strict";
            var r = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
                    return typeof e
                } : function(e) {
                    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
                },
                e = function() {
                    function i(e, t) {
                        for (var n = 0; n < t.length; n++) {
                            var i = t[n];
                            i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                        }
                    }
                    return function(e, t, n) {
                        return t && i(e.prototype, t), n && i(e, n), e
                    }
                }();

            function i(e, t) {
                if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
            }
            var t = function() {
                function n(e, t) {
                    i(this, n), this.browser = e, this.config = t, this.options = this.browser.options, this.prefetched = new Set, this.eventTime = null, this.threshold = 1111, this.numOnHover = 0
                }
                return e(n, [{
                    key: "init",
                    value: function() {
                        !this.browser.supportsLinkPrefetch() || this.browser.isDataSaverModeOn() || this.browser.isSlowConnection() || (this.regex = {
                            excludeUris: RegExp(this.config.excludeUris, "i"),
                            images: RegExp(".(" + this.config.imageExt + ")$", "i"),
                            fileExt: RegExp(".(" + this.config.fileExt + ")$", "i")
                        }, this._initListeners(this))
                    }
                }, {
                    key: "_initListeners",
                    value: function(e) {
                        -1 < this.config.onHoverDelay && document.addEventListener("mouseover", e.listener.bind(e), e.listenerOptions), document.addEventListener("mousedown", e.listener.bind(e), e.listenerOptions), document.addEventListener("touchstart", e.listener.bind(e), e.listenerOptions)
                    }
                }, {
                    key: "listener",
                    value: function(e) {
                        var t = e.target.closest("a"),
                            n = this._prepareUrl(t);
                        if (null !== n) switch (e.type) {
                            case "mousedown":
                            case "touchstart":
                                this._addPrefetchLink(n);
                                break;
                            case "mouseover":
                                this._earlyPrefetch(t, n, "mouseout")
                        }
                    }
                }, {
                    key: "_earlyPrefetch",
                    value: function(t, e, n) {
                        var i = this,
                            r = setTimeout(function() {
                                if (r = null, 0 === i.numOnHover) setTimeout(function() {
                                    return i.numOnHover = 0
                                }, 1e3);
                                else if (i.numOnHover > i.config.rateThrottle) return;
                                i.numOnHover++, i._addPrefetchLink(e)
                            }, this.config.onHoverDelay);
                        t.addEventListener(n, function e() {
                            t.removeEventListener(n, e, {
                                passive: !0
                            }), null !== r && (clearTimeout(r), r = null)
                        }, {
                            passive: !0
                        })
                    }
                }, {
                    key: "_addPrefetchLink",
                    value: function(i) {
                        return this.prefetched.add(i.href), new Promise(function(e, t) {
                            var n = document.createElement("link");
                            n.rel = "prefetch", n.href = i.href, n.onload = e, n.onerror = t, document.head.appendChild(n)
                        }).catch(function() {})
                    }
                }, {
                    key: "_prepareUrl",
                    value: function(e) {
                        if (null === e || "object" !== (void 0 === e ? "undefined" : r(e)) || !1 in e || -1 === ["http:", "https:"].indexOf(e.protocol)) return null;
                        var t = e.href.substring(0, this.config.siteUrl.length),
                            n = this._getPathname(e.href, t),
                            i = {
                                original: e.href,
                                protocol: e.protocol,
                                origin: t,
                                pathname: n,
                                href: t + n
                            };
                        return this._isLinkOk(i) ? i : null
                    }
                }, {
                    key: "_getPathname",
                    value: function(e, t) {
                        var n = t ? e.substring(this.config.siteUrl.length) : e;
                        return n.startsWith("/") || (n = "/" + n), this._shouldAddTrailingSlash(n) ? n + "/" : n
                    }
                }, {
                    key: "_shouldAddTrailingSlash",
                    value: function(e) {
                        return this.config.usesTrailingSlash && !e.endsWith("/") && !this.regex.fileExt.test(e)
                    }
                }, {
                    key: "_isLinkOk",
                    value: function(e) {
                        return null !== e && "object" === (void 0 === e ? "undefined" : r(e)) && (!this.prefetched.has(e.href) && e.origin === this.config.siteUrl && -1 === e.href.indexOf("?") && -1 === e.href.indexOf("#") && !this.regex.excludeUris.test(e.href) && !this.regex.images.test(e.href))
                    }
                }], [{
                    key: "run",
                    value: function() {
                        "undefined" != typeof RocketPreloadLinksConfig && new n(new RocketBrowserCompatibilityChecker({
                            capture: !0,
                            passive: !0
                        }), RocketPreloadLinksConfig).init()
                    }
                }]), n
            }();
            t.run();
        }());
        /* ]]> */
    </script>
    <script type="rocketlazyloadscript" data-rocket-type="text/javascript">
        (function() {
			var t   = document.createElement( 'script' );
			t.type  = 'text/javascript';
			t.async = true;
			t.id    = 'gauges-tracker';
			t.setAttribute( 'data-site-id', '63336f816027a727865f4392' );
			t.src = '//secure.gaug.es/track.js';
			var s = document.getElementsByTagName( 'script' )[0];
			s.parentNode.insertBefore( t, s );
			})();
		</script>
    <script>
        window.lazyLoadOptions = [{
            elements_selector: "img[data-lazy-src],.rocket-lazyload",
            data_src: "lazy-src",
            data_srcset: "lazy-srcset",
            data_sizes: "lazy-sizes",
            class_loading: "lazyloading",
            class_loaded: "lazyloaded",
            threshold: 300,
            callback_loaded: function(element) {
                if (element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible") {
                    if (element.classList.contains("lazyloaded")) {
                        if (typeof window.jQuery != "undefined") {
                            if (jQuery.fn.fitVids) {
                                jQuery(element).parent().fitVids()
                            }
                        }
                    }
                }
            }
        }, {
            elements_selector: ".rocket-lazyload",
            data_src: "lazy-src",
            data_srcset: "lazy-srcset",
            data_sizes: "lazy-sizes",
            class_loading: "lazyloading",
            class_loaded: "lazyloaded",
            threshold: 300,
        }];
        window.addEventListener('LazyLoad::Initialized', function(e) {
            var lazyLoadInstance = e.detail.instance;
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    var image_count = 0;
                    var iframe_count = 0;
                    var rocketlazy_count = 0;
                    mutations.forEach(function(mutation) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            if (typeof mutation.addedNodes[i].getElementsByTagName !== 'function') {
                                continue
                            }
                            if (typeof mutation.addedNodes[i].getElementsByClassName !== 'function') {
                                continue
                            }
                            images = mutation.addedNodes[i].getElementsByTagName('img');
                            is_image = mutation.addedNodes[i].tagName == "IMG";
                            iframes = mutation.addedNodes[i].getElementsByTagName('iframe');
                            is_iframe = mutation.addedNodes[i].tagName == "IFRAME";
                            rocket_lazy = mutation.addedNodes[i].getElementsByClassName('rocket-lazyload');
                            image_count += images.length;
                            iframe_count += iframes.length;
                            rocketlazy_count += rocket_lazy.length;
                            if (is_image) {
                                image_count += 1
                            }
                            if (is_iframe) {
                                iframe_count += 1
                            }
                        }
                    });
                    if (image_count > 0 || iframe_count > 0 || rocketlazy_count > 0) {
                        lazyLoadInstance.update()
                    }
                });
                var b = document.getElementsByTagName("body")[0];
                var config = {
                    childList: !0,
                    subtree: !0
                };
                observer.observe(b, config)
            }
        }, !1)
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="{{ url('v3/') }}/plantillas/functions_plantilla.js"></script>
    <script>
        function seleccionar(plantilla) {
            $('#plantilla').val(plantilla);
            mensajes('question', '¿Esta segur@ de la plantilla seleccionada?');
        }
    </script>
    <script data-no-minify="1" async src="https://adminlte.io/wp-content/plugins/wp-rocket/assets/js/lazyload/17.8.3/lazyload.min.js"></script>
</body>

</html>