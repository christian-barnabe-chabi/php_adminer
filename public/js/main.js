$(document).ready(() => {
    init();
    auto_fix();

    // $('a').each((index, ele) => { $(ele).attr('data-tooltip', $(ele).attr('href')) });

    /**
     * validate submits
     */
    // $('form').on('submit', function(e) {

    //     e.preventDefault();

    //     // inputs
    //     var empty = $(this).find('input[required]').filter(function() {
    //         return this.value == '';
    //     });

    //     if (empty.length) {

    //         new Notification('Veuillez remplir tous les champs obligatoires', 'exclamation', 'red')
    //     }

    //     // textarea
    //     var empty = $(this).find('textarea[required]').filter(function() {
    //         return this.value == '';
    //     });

    //     if (empty.length) {
    //         new Notification('Veuillez remplir tous les champs obligatoires', 'exclamation', 'red')
    //     }

    // });
    // END

    var checkboxes = $('.selected_ids');


    // mode loght/dark
    if (localStorage.getItem('mode') == 'night') {
        $('#selected-theme').checkbox('check')
    } else {
        $('#selected-theme').checkbox('uncheck')
    }

    $('#selected-theme').checkbox({
        onChecked: () => {
            localStorage.setItem('mode', 'night');
            setMode();
        },

        onUnchecked: () => {
            localStorage.setItem('mode', 'light');
            setMode();
        }
    })

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

    document_height = $(document).height();
    login_container_height = $('#login_form_container').height();
    login_container_height = login_container_height == 0 ? 272.625 : login_container_height;
    margin_top = document_height - login_container_height - login_container_height / 2;
    $('#login_form_container').css('margin-top', margin_top / 2);

    $('#side-menu, #main-container').show(0, () => { $('#spinner').hide(200) });

    $(window).resize((e) => {
        auto_fix()
    })

    all_checked = false;

    if ($('#check_all_objcts').prop('checked')) {
        all_checked = true;
    }

    $('.selected_ids').each((index, elem) => {
        if ($(elem).is(':checked')) {
            $(elem).parents('tr').addClass('selected_row');
        }
    })

    $(document).on('click', '.selected_ids', (event) => {
        $(event.target).parents('tr').toggleClass('selected_row');
    })

    $(document).on('click', '#check_all_objcts', (event) => {
        // let checkboxes = $('.selected_ids');

        all_checked = !all_checked;

        for (const checkbox in checkboxes) {
            if (checkboxes.hasOwnProperty(checkbox)) {
                const element = checkboxes[checkbox];
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

    $('.selected_ids').on('change', (event) => {
        if (all_checked) {
            $('#check_all_objcts').prop('checked', false);
            all_checked = !all_checked;
        }
    })

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
    }

    $(document).on('click', '#selected_resource', (event) => {
        event.preventDefault();
    })

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
    }

    var x = $('.menu a.active.item');

    if (x.length != 0) {
        var top = x.position().top;
        x.offsetParent().animate({
            scrollTop: (top - 15) + 'px'
        });
    }



    // embeded rows

    $(document).on('click', '.embeded_row_remover', (event) => {
        $(event.target).parents('.embeded_row').fadeOut(200);
        setTimeout(() => {
            $(event.target).parents('.embeded_row').remove();
        }, 500)
    })

    $(document).on('click', '.embeded_row_add_btn', (event) => {
        let container = $(event.target).parents('.embeded_row_add_btn').next().next();
        let sample = $(event.target).parents('.embeded_row_add_btn').next().children('.embeded_row').clone(true);

        let index = container.children().length;
        index++;

        let object = sample.children().find('input');

        for (const key in object) {
            if (object.hasOwnProperty(key)) {
                const element = object[key];


                let name = $(element).attr('name');

                if (name != undefined) {
                    name = name.replace("[index]", "[" + index + "]")
                    $(element).attr('name', name);
                }
            }
        }

        container.append(sample);

        $('.dropdown ').dropdown();
    })

    $(document).on('click', ':submit', (event) => {
        // event.preventDefault();
        $(event.target).parents('form').children('.embeded_row_sample').remove();
        // console.log($(event.target).parents('form').serialize())
    })

})