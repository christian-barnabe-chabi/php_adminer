$(document).ready(() => {
    init();
    auto_fix();

    var myTable = $('#table_of_resource');


    try {
        paginator = new Paginator(myTable, 1);
    } catch (error) {
        console.error(error)
    }

    try {
        tableSearchable = new TableSearchable(myTable, '', $('#search_value'))

        tableSearchable.is_empty = function() {
            if(paginator)
                paginator.fill()
        }

        $('#search_in').on('change', (e)=> {
            tableSearchable.column = $('#search_in').val()
            // tableSearchable.search()
        })

    } catch (error) {
        console.error(error)
    }

    document_height = $(document).height();
    login_container_height = $('#login_form_container').height();
    login_container_height = login_container_height == 0 ? 272.625 : login_container_height;
    margin_top = document_height - login_container_height - login_container_height/2;
    $('#login_form_container').css('margin-top', margin_top/2);

    $('#side-menu, #main-container').fadeIn(500, ()=>{$('#spinner').hide(100)});

    $(window).resize((e)=> {
        auto_fix()
    })

    all_checked = false;

    if($('#check_all_objcts').prop('checked')) {
        all_checked = true;
    }

    $('#check_all_objcts').on('click', (event)=> {
        let checkboxes = $('.selected_ids');
        
        all_checked = !all_checked;

        for (const checkbox in checkboxes) {
            if (checkboxes.hasOwnProperty(checkbox)) {
                const element = checkboxes[checkbox];
                if($(element).parent().parent().css('display') == 'table-row') {
                    $(element).prop('checked', all_checked);
                }
            }
        }
    })

    $('.selected_ids').on('change', (event)=> {
        if(all_checked) {
            $('#check_all_objcts').prop('checked', false);
            all_checked = !all_checked;
        }
    })

    function init() {

        $('.ui.rating.comment')
            .rating('disable')
            ;

        $('.ui.sticky')
            .sticky({
                context: '#context',
                topOffset: 100,
            });

        $('.message .close')
            .on('click', function() {
                $(this)
                    .closest('.message')
                    .transition('fade');
            });

        $(".dropdown").dropdown();

        $('table:not(.no-sort)').tablesort();
    }

    $('#selected_resource').on('click', (event)=>{
        event.preventDefault();
    })

    function auto_fix() {
        document_height = $(document).height();
        login_container_height = $('#login_form_container').height();
        login_container_height = login_container_height == 0 ? 272.625 : login_container_height;
        margin_top = document_height - login_container_height - login_container_height/2;
        $('#login_form_container').css('margin-top', margin_top/2);
        
        $('#underHeader').css("height", ($('#topNavbar').innerHeight() + 15) + "px");
        $('#side-menu').css("top", ($('#topNavbar').innerHeight()) + 0 +  "px");
    
        $('#side-menu').css("height", ( $(window).height() - $('#topNavbar').innerHeight()) + "px");
        $('#main-container').css("width", ( $(window).width() - $('#side-menu').innerWidth()) + "px");
        $('#main-container').css("left", ($('#side-menu').innerWidth()) + "px");
    }

    var x = $('.menu a.active.item');

    if(x.length != 0) {
        var top = x.position().top;
        x.offsetParent().animate({
            scrollTop: top+'px'
        });
    }

})