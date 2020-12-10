$(document).ready(() => {

    class MeetingStates {
        constructor(element) {
            this.element = $(element);
            this.meeting_method_container = $($(this.element.parents('#_container')).next());

            this.meeting_link_container = $(this.meeting_method_container.next());

            this.meeting_method_dropdown = $(this.meeting_method_container.children('.form').children('.field').children('.ui.input').children('.meeting_method'))
            this.meeting_method_link = $(this.meeting_link_container.children('.form').children('.field').children('.ui.input').children('.meeting_link'))

            this.meeting_link_old = this.meeting_method_link.val();
            this.meeting_method_id_old = this.meeting_method_dropdown.dropdown('get value');

            this.init();
            this.onChange();
        }

        init() {
            if (this.meeting_method_dropdown.dropdown('get value')) { // meeting_id
                this.element.dropdown('set selected', 'online')
            } else {
                this.element.dropdown('set selected', 'physical')
                this.meeting_method_container.hide();
                this.meeting_link_container.hide();
            }
        }

        onChange() {

            let element = this.element;
            let meeting_method_dropdown = this.meeting_method_dropdown;
            let meeting_method_container = this.meeting_method_container;
            let meeting_link_container = this.meeting_link_container;
            let meeting_method_link = this.meeting_method_link;
            let meeting_link_old = this.meeting_link_old;
            let meeting_method_id_old = this.meeting_method_id_old;

            this.element.dropdown({
                onChange: function() {
                    if (element.dropdown('get value') == 'physical') {
                        meeting_method_container.hide();
                        meeting_link_container.hide();

                        meeting_method_link.val('');
                        meeting_method_dropdown.dropdown('set value', null);
                    } else {
                        meeting_method_container.show();
                        meeting_link_container.show();

                        meeting_method_link.val(meeting_link_old);
                        meeting_method_dropdown.dropdown('set value', meeting_method_id_old);
                    }
                }
            })
        }

    }

    $('.meeting_type_for_ticket').each((index, element) => {
        new MeetingStates(element);
    })

    // var old_value;
    // var old_value_meeting_link;

    // old_value_meeting_link = $('.meeting_link').val();
    // old_value = $('.meeting_method').dropdown('get value');

    // $('.meeting_type_for_ticket').dropdown({
    //     onChange : function() {
    //         if($('.meeting_type_for_ticket').dropdown('get value') == 'physical') {
    //             $('.meeting_method_container').hide();
    //             $('.meeting_link_container').hide();

    //             $('.meeting_link').val('');
    //             $('.meeting_method').dropdown('set value', null);
    //         } else {
    //             $('.meeting_method_container').show();
    //             $('.meeting_link_container').show();

    //             $('.meeting_link').val(old_value_meeting_link);
    //             $('.meeting_method').dropdown('set value', old_value);
    //         }
    //     }
    // })

    // if($('.meeting_method').dropdown('get value')) {
    //     $('.meeting_type_for_ticket').dropdown('set selected', 'online')
    // } else {
    //     $('.meeting_type_for_ticket').dropdown('set selected', 'physical')
    //     $('.meeting_method_container').hide();
    //     $('.meeting_link_container').hide();
    // }
})