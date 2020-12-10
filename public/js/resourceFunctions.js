$(document).ready(() => {

    // var refresh = {
    //     SELF: 0, // refresh on self
    //     BACK: -1, // go back
    //     NONE: 1, // do nothing
    //     DISC: 401, // discnnect
    // };

    authToken = $('#authToken').val();
    $('#authToken').remove();
    deletedRow = null;
    targetModal = null;
    selectedIds = [];

    // on one resource delete button clicked
    $(document).on('click', '.resource_delete_button', (event) => {
        target = $(event.currentTarget);
        deletedRow = $(target.parents("tr"));
        targetModal = $("#" + (target.attr('data-target')));

        targetModal.modal({
            transition: 'fly up',
            closable: false,
        }).modal('show');
    });

    // on multitple delete button clicked
    $(document).on('click', '.resource_multiple_delete_button', (event) => {
        target = $(event.currentTarget);
        deletedRow = []; // multitple rows
        selectedIds = [];
        targetModal = $("#confirm_delete");

        $('.selected_ids').each((index, elem) => {
            if ($(elem).is(':checked')) {
                selectedIds.push($(elem).attr('value'));
                deletedRow.push($($(elem).parents('tr')));
            }
        });

        if (selectedIds.length == 0) {
            new ErrorModal(translate('select_row_error', lang), translate('error', lang), translate('close', lang)).show();
            return;
        }

        targetModal.modal({
            transition: 'fly up',
            closable: false,
        }).modal('show');
    });

    // on_show_page_delete_button
    $(document).on('click', '#on_show_page_delete_button', (event) => {
        target = $(event.currentTarget);
        targetModal = $("#confirm_delete");

        targetModal.modal({
            transition: 'fly up',
            closable: false,
        }).modal('show');
    });

    // one resource confirmation modal - - the delete button
    $(document).on('click', '.modal_delete_button', (event) => {
        let deleteButton = $(event.currentTarget);

        deleteButton.attr('disabled', '');
        deleteButton.addClass('loading');

        method = deleteButton.attr('data-method');
        id = deleteButton.attr('data-value');
        url = deleteButton.attr('data-url');

        url = url.replace('{id}', id);

        deleteResource(url, method, deletedRow, deleteButton, refresh.NONE);
    });

    // one resource confirmation modal - - the delete button from show page
    $(document).on('click', '.modal_delete_button_show_page', (event) => {
        let deleteButton = $(event.currentTarget);

        deleteButton.attr('disabled', '');
        deleteButton.addClass('loading');

        method = deleteButton.attr('data-method');
        id = deleteButton.attr('data-value');
        url = deleteButton.attr('data-url');

        url = url.replace('{id}', id);

        deleteResource(url, method, deletedRow, deleteButton, refresh.BACK);
    });


    // multiple resources confirmation modal - the delete button
    $(document).on('click', '#modal_delete_multitple_button', (event) => {
        let deleteButton = $(event.currentTarget);

        deleteButton.attr('disabled', '');
        deleteButton.addClass('loading');

        method = deleteButton.attr('data-method');
        url = deleteButton.attr('data-url');

        deleteMultitpleResource(selectedIds, url, method, deletedRow, deleteButton);
    });


    $(document).on('click', '#resource_multiple_export_button', (event) => {
        target = $(event.currentTarget);
        deletedRow = []; // multitple rows
        selectedIds = [];
        targetModal = $("#confirm_delete");

        $('.selected_ids').each((index, elem) => {
            if ($(elem).is(':checked')) {
                selectedIds.push($(elem).attr('value'));
                deletedRow.push($($(elem).parents('tr')));
            }
        });

        if (selectedIds.length == 0) {
            event.preventDefault();
            new ErrorModal("Veuillez sélectionner une ou plusieurs lignes pour appliquer cette action", "Erreur", "Fermer").show();
            return;
        }
    });

    //selected leftside menu listener
    $(document).on('click', '.selected_resource_leftside_menu', (event) => {
        event.preventDefault();
        let target = $(event.currentTarget);
        let href = target.attr('href');

    })

    window.onpopstate = function(event) {
        loadPageAt(document.location.href)
    }

    $(document).on('click', 'a:not(.external-link, [target=_blank])', (event) => {
        event.preventDefault();
        let target = $(event.currentTarget);
        let href = target.attr('href');

        if (href && href != '#' && href.length > 1) {
            loadPageAt(href);
        }
    })

    //leftside menu listener
    $(document).on('click', '.resource_leftside_menu', (event) => {
        event.preventDefault();
        let target = $(event.currentTarget);
        let href = target.attr('href');

        loadPageAt(href);
    })

    // create new resource
    $(document).on('click', '.resource_create_submit_button', (event) => {
        event.preventDefault();
        let createButton = $(event.currentTarget);
        form = $(createButton.parents('form'));

        createButton.attr('disabled', '');
        createButton.addClass('loading');

        method = createButton.attr('data-method');
        url = createButton.attr('data-url');

        if (validate_form(form)) {
            var fd = new FormData(form[0]);
            createResource(url, method, fd, createButton);
        } else {
            createButton.removeAttr('disabled', '');
            createButton.removeClass('loading');
        }

    });


    // update a resource
    $(document).on('click', '.resource_update_submit_button', (event) => {
        event.preventDefault();

        let updateButton = $(event.currentTarget);
        form = $(updateButton.parents('form'));;

        updateButton.attr('disabled', '');
        updateButton.addClass('loading');

        method = updateButton.attr('data-method');
        url = updateButton.attr('data-url');
        id = updateButton.attr('data-value');

        url = url.replace('{id}', id);

        if (validate_form(form)) {
            var fd = new FormData(form[0]);
            createResource(url, method, fd, updateButton);
        } else {
            updateButton.removeAttr('disabled', '');
            updateButton.removeClass('loading');
        }
    });

    // create resource
    function createResource(url, method, data, createButton, redirect = refresh.SELF) {

        if (data && url && method) {

            promise = new Promise((resolve, reject) => {

                $.ajax({
                    url: url,
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'accecpt': 'application/json',
                    },
                    method: method,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: (data) => {
                        resolve(data);
                    },
                    error: (data) => {
                        reject(data);
                    }
                });

            }).then((data) => { // resolved

                let responseJson = data;
                let responseMessage = translate('operation_done', lang);

                if (responseJson !== undefined) {
                    responseMessage = responseJson.message ? responseJson.message : translate('operation_done', lang);
                }

                // new SuccessModal(responseMessage, translate('success', lang), 'Continuer', redirect).show();
                new Notification(translate('success', lang), responseMessage, "exclamation", "green");
                $(".ui.modal").modal('hide');
                updateView(redirect);

            }, (data) => { // rejected

                let responseJson = data.responseJSON;
                let responseMessage = "";

                if (responseJson !== undefined) {
                    responseMessage = responseJson.message ? ". Message: " + responseJson.message : "";
                }

                let message = translate('an_error_occurs', lang) + responseMessage;
                if (data.status == 0) {
                    message = translate('an_error_occurs_interner', lang) + responseMessage;
                }

                new Notification(translate('error', lang) + ' ' + data.status, message, "exclamation", "orange");

            }).then(() => {

                createButton.removeAttr('disabled');
                createButton.removeClass('loading');
                // targetModal.modal('hide');
                // $(".ui.modal").modal('hide');

            })
        } else {
            createButton.removeAttr('disabled');
            createButton.removeClass('loading');
        }
    }

    // delete single resource
    function deleteResource(url, method, deletedRow, deleteButton, redirect = refresh.NONE) {

        if (url && method) {

            promise = new Promise((resolve, reject) => {

                $.ajax({
                    url: url,
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'accecpt': 'application/json',
                    },
                    method: method,
                    data: [],
                    success: (data) => {
                        resolve(data);
                    },
                    error: (data) => {
                        reject(data);
                    }
                });

            }).then((data) => { // resolved

                if (redirect == refresh.NONE) {
                    deletedRow.remove();
                }

                let responseJson = data;
                let responseMessage = translate('deleted_done', lang);

                if (responseJson !== undefined) {
                    responseMessage = responseJson.message ? responseJson.message : translate('deleted_done', lang);
                }

                // new SuccessModal(responseMessage, translate('success', lang), 'Continuer', redirect).show();
                new Notification(translate('success', lang), responseMessage, "exclamation", "green");
                updateView(redirect);

            }, (data) => { // rejected

                new Notification(translate('error', lang) + ' ' + data.status, message, "exclamation", "orange");

            }).then(() => {

                deleteButton.removeAttr('disabled');
                deleteButton.removeClass('loading');
                targetModal.modal('hide');

            })
        } else {
            deleteButton.removeAttr('disabled');
            deleteButton.removeClass('loading');
        }
    }

    // delete multiple resources
    function deleteMultitpleResource(selectedIds, globalurl, method, deletedRow, deleteButton) {

        if (selectedIds.length == 0) {
            deleteButton.removeAttr('disabled');
            deleteButton.removeClass('loading');
            return;
        }

        promises = [];

        for (id of selectedIds) {
            url = globalurl.replace("{id}", id);

            promises.push(
                new Promise((resolve, reject) => {

                    $.ajax({
                        url: url,
                        headers: {
                            'Authorization': 'Bearer ' + authToken,
                            'accecpt': 'application/json',
                        },
                        method: method,
                        data: [],
                        success: (data) => {
                            resolve(data);
                        },
                        error: (data) => {
                            reject(data);
                        }
                    });

                })
            );
        }

        Promise.all(promises).then((data) => {

            for (row of deletedRow)
                ($(row).remove());

            let responseJson = data;
            let responseMessage = translate('deleted_done', lang);

            if (responseJson !== undefined) {
                responseMessage = responseJson.message ? responseJson.message : translate('delete_done', lang);
            }

            // new SuccessModal(responseMessage, translate('success', lang)).show();
            new Notification(translate('success', lang), responseMessage, "exclamation", "green");
            updateView(redirect);

            selectedIds = [];
            deletedRow = [];

        }, (data) => {

            new Notification(translate('error', lang) + ' ' + data.status, message, "exclamation", "orange");

        }).then(() => {
            deleteButton.removeAttr('disabled');
            deleteButton.removeClass('loading');
            targetModal.modal('hide');
        })

    }

    function validate_form(form) { // TODO validate email, website, patterns

        // inputs
        var empty = form.find('input[required]').filter(function() {
            return this.value == '';
        });

        if (empty.length) {
            new Notification(translate('error', lang), 'Veuillez remplir tous les champs obligatoires', 'exclamation', 'red');
            return false;
        }

        // textarea
        var empty = form.find('textarea[required]').filter(function() {
            return this.value == '';
        });

        if (empty.length) {
            new Notification(translate('error', lang), 'Veuillez remplir tous les champs obligatoires', 'exclamation', 'red');
            return false;
        }

        // email
        var emailPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        var empty = form.find('input[type=email]').filter(function() {
            if (this.value == '')
                return false;
            return emailPattern.test(this.value) == false;
        });

        if (empty.length) {
            new Notification(translate('error', lang), 'Les emails doivent être de type example@domain.com', 'exclamation', 'red');
            return false;
        }

        //website
        var websitePattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/;
        var empty = form.find('input[type=url]').filter(function() {
            if (this.value == '')
                return false;
            return websitePattern.test(this.value) == false;
        });

        if (empty.length) {
            new Notification(translate('error', lang), 'Les URLs doivent être de type (http://|https:://)(www.)domain.com', 'exclamation', 'red');
            return false;
        }

        return true;
    }

    $(document).on('click', '#side-menu .item', (event) => {

        let active = $('#side-menu').find('.item.active');
        active.removeClass('active');
        active.removeClass('selected_resource_leftside_menu');

        $(event.currentTarget).addClass('active');
        $(event.currentTarget).addClass('selected_resource_leftside_menu');

    })


})