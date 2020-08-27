class TableSearchable {

    constructor(table, column, search_field) {
        this.table = table
        this.column = column
        this.search_field = search_field
        this.table_head = this.table.children('thead')
        this.table_columns = this.table_head.children('tr').children('th') 
        this.table_body = this.table.children('tbody')
        this.table_rows = this.table_body.children('tr')

        this.search_field.on('keyup', (event)=>{
            this.search()
        })
    }

    search() {

        if(this.column == '') {
            return
        }
        else if(this.search_field.val().length == 0) {
            this.is_empty()
            return
        } 

        if(this.search_field.val().length == 0) {
            return;
        }

        let search_column_index = -1;
        for(let i = 0; i < this.table_columns.length; i++) {
            const column = $(this.table_columns[i]).text()

            if(column.toLowerCase() == this.column.toLowerCase()) {
                search_column_index = i
                break;
            }
        }

        if(search_column_index != -1) {
            let result = new Array()

            this.table_rows.each((index, row) => {

                let current_row_columns = $(row).children('td')
                let target_cell = $(current_row_columns[search_column_index])
    
                let search_keyword = this.search_field.val().trim().toLowerCase()
                let cell_value = target_cell.text().trim().toLowerCase()

                search_keyword = search_keyword.replace("'", "\\\\'");
                search_keyword = search_keyword.replace("?", "\\\\?");

                // regex
                var regex = new RegExp(search_keyword, 'iug');
                if(!regex.test(cell_value)) {
                    if($(row).is('tr'))
                        $(row).hide(0);
                } else {
                    if($(row).is('tr'))
                        $(row).show(50);
                }
            });

        }
    }

    is_empty = function() {
        console.log('Empty')
    }

}