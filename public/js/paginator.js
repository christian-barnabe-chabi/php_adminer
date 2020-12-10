Paginator = class {
    constructor(table, page) {
        this.table = table
        this.page = page
        this.table_body = this.table.children('tbody')
        this.table_rows = this.table_body.children('tr')
        this.length = this.table_rows.length


        this.prevBtn = this.table.parent('div').nextAll('div').find('#paginator_prev')
        this.nextBtn = this.table.parent('div').nextAll('div').find('#paginator_next')
        this.currentPageField = this.table.parent('div').nextAll('div').find('#current_page_field')
        this.lastPageField = this.table.parent('div').nextAll('div').find('#last_page_field')
        this.perPageField = this.table.parent('div').nextAll('div').find('#per_page_field')

        this.currentPageField.attr('min', 1)

        this.setLastPage()
        this.fill()



        this.currentPage = this.currentPageField.val()

        this.prevBtn.on('click', (event) => {

            if (this.currentPage <= 1) {

            } else {
                this.currentPage--
                    this.page = this.currentPage
                this.fill()
            }

        })

        this.nextBtn.on('click', (event) => {

            if (this.currentPage == this.last_page) {

            } else {

                this.currentPage++
                    this.page = this.currentPage
                this.fill()
            }

        })

        this.currentPageField.on('keypress', (event) => {
            event.preventDefault()
            if (event.which == 13) {
                this.currentPage = this.currentPageField.val()
                this.page = this.currentPage
                this.fill()
            }
        })

        this.perPageField.on('change', (event) => {
            this.setLastPage()
            this.page = 1
            this.currentPage = 1;
            this.fill()
        })
    }

    fill() {


        if (this.page > 0)
            this.page--;

        this.begin = this.page * this.per_page
        this.end = this.begin + this.per_page

        this.currentPageField.val(this.page + 1)

        this.table_rows.hide(0);
        this.chunk = this.table_rows.slice(this.begin, this.end)
        this.chunk.show(250)
    }

    setLastPage() {

        this.per_page = this.perPageField.val() == undefined ? 10 : parseInt(this.perPageField.val())
        this.last_page = Math.ceil(this.table_rows.length / this.per_page)
        this.currentPageField.attr('max', this.last_page)
        this.lastPageField.val(this.last_page)

    }
}