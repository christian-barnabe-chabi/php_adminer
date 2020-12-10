const lang = "fr";

var refresh = {
    SELF: 0, // refresh on self
    BACK: -1, // go back
    NONE: 1, // do nothing
    DISC: 401, // discnnect
};

const translation = {
    error: {
        fr: "Erreur",
        en: "Error"
    },
    success: {
        fr: "Succès",
        en: "Succcess"
    },
    continue: {
        fr: "Continuer",
        en: "Continue"
    },
    ok: {
        fr: "OK",
        en: "OK"
    },
    deleted_done: {
        fr: "Supprimé",
        en: "Deleted"
    },
    select_row_error: {
        fr: "Veuillez sélectionner une ou plusieurs lignes pour appliquer cette action",
        en: "Please select at least one row to apply that action"
    },
    an_error_occurs: {
        fr: "Une erreur est survenue. Assurez-vous d'être connecté à Internet et réesayer",
        en: "An error occurs"
    },
    an_error_occurs_interner: {
        fr: "Une erreur est survenue. Assurez-vous d'être connecté à Internet et réesayer",
        en: "An error occurs"
    },
    operation_done: {
        fr: "Opération terminé",
        en: "Operation done"
    },
}

var SuccessModal = class {

    modal;
    title = "Succès";
    buttonText = "Continuer";

    constructor(message, title = "Erreur", buttonText = "Continuer", redirect = refresh.NONE) {

        this.title = title;

        this.buttonText = buttonText;

        let button = '<button onclick="$(\'.ui.error.modal\').modal(\'hide\')" class="ui mini green button">' + this.buttonText + '</button>';

        if (redirect === refresh.BACK) {

            let referrer = '/' + document.location.pathname.split('/')[1];
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini green button">' + this.buttonText + '</a>';
        } else if (redirect === refresh.SELF) {
            let referrer = document.location.href;
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini green button">' + this.buttonText + '</a>';
        } else if (redirect === refresh.DISC) {
            let referrer = "/logout";
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini green button external-link">' + this.buttonText + '</a>';
        }

        this.modal = $('\
            <div class="ui modal compact mini error" id="success_modal">\
                <div class="header">' + this.title + '</div>\
                <div class="content">' + message +
            '</div>\
                <div class="actions">' + button + '</div>\
            </div>\
        ');
    }

    show() {
        this.modal.modal({
            transition: 'fly',
            closable: false,
        }).modal('show');
    }
}


var ErrorModal = class {

    modal;
    title;
    buttonText;

    constructor(message, title = "Erreur", buttonText = "Continuer", redirect = refresh.NONE) {
        this.title = title;

        this.buttonText = buttonText;

        let button = '<button onclick="$(\'.ui.error.modal\').modal(\'hide\')" class="ui mini orange button">' + this.buttonText + '</button>';

        if (redirect === refresh.BACK) {

            let referrer = '/' + document.location.pathname.split('/')[1];
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini orange button">' + this.buttonText + '</a>';
        } else if (redirect === refresh.SELF) {
            let referrer = document.location.href;
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini orange button">' + this.buttonText + '</a>';
        } else if (redirect === refresh.DISC) {
            let referrer = "/logout";
            button = '<a onclick="$(\'.ui.error.modal\').modal(\'hide\')" href="' + referrer + '" class="ui mini orange button external-link">' + this.buttonText + '</a>';
        }

        this.modal = $('\
            <div class="ui modal compact mini error" id="error_modal">\
                <div class="header">' + this.title + '</div>\
                <div class="content">' + message +
            '</div>\
                <div class="actions">' + button + '</div>\
            </div>\
        ');
    }

    show() {
        this.modal.modal({
            transition: 'fly',
            closable: false,
        }).modal('show');
    }
}

/**
 * Notification
 */
var Notification = class {

    mainNotificationBox = $('<div></div>')

    notificationContainer = $('\
        <div class="ui icon message small">\
        </div>')

    notificationIcon = $('<i class="icon"></i>')

    messageContainer = $('\
        <div class="content">\
        </div>\
        ')

    messageTitle = $('\
        <div class="header"></div>\
        ')

    messageText = $('\
        <p class=""></p>\
        ')

    constructor(title, message, icon = 'inbox', color = 'green') {

        this.messageTitle.text(title);

        this.mainNotificationBox.append(this.notificationContainer)
        this.notificationContainer.addClass(color)
        this.notificationIcon.addClass(icon)

        this.notificationContainer.append(this.notificationIcon)

        this.notificationContainer.append(this.messageContainer)
        this.messageContainer.append(this.messageTitle)
        this.messageContainer.append(this.messageText)
        this.messageText.text(message)

        this.mainNotificationBox.css({
            position: "absolute",
            top: "10px",
            zIndex: 4000,
            width: '100%',
            display: "none",
        })

        this.notificationContainer.css({
            position: "relative",
            margin: "auto",
            width: '50%'
        })

        $(document.body).append(this.mainNotificationBox)

        this.mainNotificationBox.fadeIn(1000)

        setTimeout(() => {
            this.mainNotificationBox.fadeOut(1000)
        }, 5000)
    }
}

var isProcessing = false;

var isSomeProcess = () => {
    return isProcessing;
}

function loadPageAt(href) {

    let loader = $('.ui.active.loader.waiting');

    $('#main-container').fadeOut(200, () => {
        loader.fadeIn(200);
    });

    if (isProcessing) {
        return;
    }

    new Promise((resolve, reject) => {
        $('#page_progress').fadeIn(100);
        isProcessing = true;
        $.ajax(href, {
            headers: {
                accept: 'text/html'
            },
            success: (data) => {
                resolve(data);
            },
            error: (data) => {
                reject(data);
            }
        })
    }).then((data) => { //resolved

        let title = $(data).filter((index, ele) => { return $(ele).prop('tagName') === 'TITLE' });
        $('title').text($(title).text());
        // console.log(data)

        $('#main-container').html($(data).children("#main-container").html());

        init();
        auto_fix();
        initActionOnTable();

        try {
            history.pushState({}, $('title').text(), href);
        } catch (error) {
            new ErrorModal("Une erreur est survenue. Nous ne pouvons pas charger cette page", translate('error', lang), "Continer", refresh.BACK).show();
            history.pushState({}, $('title').text(), document.location.href);
        }

        return;

    }, (data) => { // rejected
        console.error(data);
        return;
    }).then((data) => {
        isProcessing = false;
        $('#page_progress').hide(300);

        loader.fadeOut(100, () => {
            $('#main-container').fadeIn(500);
        });
    });
}

function init() {

    $('.ui.rating.comment')
        .rating('disable');

    $('.ui.sticky')
        .sticky({
            context: '#context',
            topOffset: 100,
        });

    $(document)
        .on('click', '.message .close', function() {
            $(this)
                .closest('.message')
                .transition('fade');
        });

    $(".dropdown").dropdown();

    $('table:not(.no-sort)').tablesort();


    $('.selected_ids').each((index, elem) => {
        if ($(elem).is(':checked')) {
            $(elem).parents('tr').addClass('selected_row');
        }
    })

    $(document).on('click', '.selected_ids', (event) => {
        $(event.target).parents('tr').toggleClass('selected_row');
    })

    all_checked = false;

    if ($('#check_all_objcts').prop('checked')) {
        all_checked = true;
    }

    var checkboxes = $('.selected_ids');
    $(document).on('click', '#check_all_objcts', (event) => {
        // let checkboxes = $('.selected_ids');

        all_checked = !all_checked;

        for (const checkbox in checkboxes) {
            if (checkboxes.hasOwnProperty(checkbox)) {
                const element = checkboxes[checkbox];
                console.log(element);
                console.log(all_checked);
                if ($(element).parent().parent().css('display') == 'table-row') {
                    $(element).prop('checked', all_checked);
                }

                if ($(element).is(':checked')) {
                    $(element).parents('tr').addClass('selected_row');
                } else {
                    $(element).parents('tr').removeClass('selected_row');
                }
            }
        }
    })

    $(document).on('change', '.selected_ids', (event) => {
        if (all_checked) {
            $('#check_all_objcts').prop('checked', false);
            all_checked = !all_checked;
        }
    })
}

function initActionOnTable() {
    var myTable = $('#table_of_resource');


    if ($('#page_paginator_indicator').val() == 1) {
        try {
            paginator = new Paginator(myTable, 1);
        } catch (error) {
            console.error(error)
        }
    }


    // search
    try {
        tableSearchable = new TableSearchable(myTable, '', $('#search_value'))

        tableSearchable.is_empty = function() {
            if (paginator)
                paginator.fill()
        }

        $('#search_in').on('change', (e) => {
            tableSearchable.column = $('#search_in').val()
                // tableSearchable.search()
        })

        //$('#search_value').on('keydown', ()=> {
        //    all_checked = false;
        //    $('#check_all_objcts').prop('checked', false);
        //})

    } catch (error) {
        console.error(error)
    }
}

function auto_fix() {
    document_height = $(document).height();
    login_container_height = $('#login_form_container').height();
    login_container_height = login_container_height == 0 ? 272.625 : login_container_height;
    margin_top = document_height - login_container_height - login_container_height / 2;
    $('#login_form_container').css('margin-top', margin_top / 2);

    $('#underHeader').css("height", ($('#topNavbar').innerHeight() + 15) + "px");
    $('#side-menu').css("top", ($('#topNavbar').innerHeight()) + 0 + "px");

    $('#side-menu').css("height", ($(window).height() - $('#topNavbar').innerHeight()) + "px");
    // $('#main-container').css("width", ($(window).width() - $('#side-menu').innerWidth()) + "px");
    $('#main-container').css("margin-left", ($('#side-menu').innerWidth()) + "px");

    var x = $('.menu a.active.item');
    if (x.length != 0) {
        setTimeout(() => {
            var top = $(x)[0].offsetTop;

            x.offsetParent().animate({
                scrollTop: (top - 15) + 'px'
            });

        }, 1000);

    }
}

function translate(word, lang) {
    try {
        return translation[word][lang];
    } catch (error) {
        return word;
    }
}

function updateView(redirect = refresh.NONE) {
    let referrer = null;
    setTimeout(() => {
        switch (redirect) {
            case refresh.NONE:

                break;
            case refresh.SELF:
                {
                    referrer = document.location.href;
                }
                break;
            case refresh.BACK:
                {
                    referrer = '/' + document.location.pathname.split('/')[1];
                }
                break;
            case refresh.DISC:
                {
                    referrer = "/logout";
                }
                break;
        }

        if (referrer != null) {
            loadPageAt(referrer);
        }
    }, 1500);
}