$(document).ready(()=>{
    return 0;
    let user_token = $('#user_token').val();

    if(user_token.length) {
        
        let baseUrl = "http://45.79.221.17:8000/api"
    
        var tickets_per_reason_count_chart_ctx = document.getElementById('tickets_per_reason_count_chart').getContext('2d');
        var chart_1 = new Chart(tickets_per_reason_count_chart_ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'tickets_per_reason_count',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                ],
            },
        })
    
        var tickets_count_chart_ctx = document.getElementById('tickets_count_chart').getContext('2d');
        var chart_2 = new Chart(tickets_count_chart_ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Rendez-vous',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                    {
                        label: 'Ticket de visite',
                        data: [],
                        backgroundColor: [
                            'rgba(153, 102, 255, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                ],
            }
        })
    
        var tickets_ratios_chart_ctx = document.getElementById('tickets_ratios_chart').getContext('2d');
        var chart_3 = new Chart(tickets_ratios_chart_ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Rendez-vous tenus / Rendez-vous pris',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                ],
            }
        })
    
        var sme_meetings_count_chart_ctx = document.getElementById('sme_meetings_count_chart').getContext('2d');
        var chart_4 = new Chart(sme_meetings_count_chart_ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Partenaires rencontrés par les PMEs',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                ],
            }
        })
    
        var partner_meetings_count_chart_ctx = document.getElementById('partner_meetings_count_chart').getContext('2d');
        var chart_5 = new Chart(partner_meetings_count_chart_ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'PMEs rencontrées par les partenaires',
                        data: [],
                        backgroundColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                    },
                ],
            }
        })
        
        var permanences_count_chart_ctx = document.getElementById('permanences_count_chart').getContext('2d');
        var chart_6 = new Chart(permanences_count_chart_ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Permanences par entreprise',
                        data: [],
                        backgroundColor: [
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                        ],
                    },
                ],
            }
        })
        
        var stats = {};    
        
        // tickets_per_reason_count
        let tickets_per_reason_count = (resolve) => {

            let fn = (data) => {
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
    
                        switch(key) {
                            case 'visit':
                                _labels.push('Visit')
                            break;
                            case 'negotiation':
                                _labels.push('Négociation')
                            break;
                            case 'partnership':
                                _labels.push('Partenariat')
                            break;
                            case 'other':
                                _labels.push('Autre')
                            break;
                            default:
                                _labels.push(key)
                            break;
                        }
    
                        _data.push(data[key])
                        
                    }
                }
        
                stats.tickets_per_reason_count = {
                    labels : _labels,
                    data: _data,
                }

                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/tickets_per_reason_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // tickets_count
        let tickets_count = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
    
                        switch(key) {
                            case 'PENDING':
                                _labels.push('En attente')
                            break;
                            case 'APPROVED':
                                _labels.push('Apprové')
                            break;
                            case 'CANCELED':
                                _labels.push('Annulé')
                            break;
                            case 'CLOSED':
                                _labels.push('Terminé')
                            break;
                            default:
                                _labels.push(key)
                            break;
                        }
    
                        _data.push(data[key])
                        
                    }
                }
        
                stats.tickets_count = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/tickets_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // pme_count
        let pme_count = (resolve) => {
            let fn = (data)=>{
                stats.pme_count = data
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/pme_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
        
        // partner_count
        let partner_count = (resolve) => {
            let fn = (data)=>{
                stats.partner_count = data
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/partner_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // user_count
        let user_count = (resolve) => {
            let fn = (data)=>{
                stats.user_count = data
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/user_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // all_tickets_count
        let all_tickets_count = (resolve) => {
            let fn = (data)=>{
                console.log("here");
                stats.all_tickets_count = data
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/all_tickets_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn,
                error : function (xhr) {
                    console.log(xhr)
                }
            });
        }
    
        // tickets_ratios
        let tickets_ratios = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        switch(key) {
                            case 'taken':
                                _labels.push('Tenus')
                            break;
                            case 'not_taken':
                                _labels.push('Pris')
                            break;
                            default:
                                _labels.push(key)
                            break;
                        }
                        _data.push(data[key])
                        
                    }
                }
        
                stats.tickets_ratios = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/tickets_ratios",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // sme_meetings_count
        let sme_meetings_count = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const value = data[key];
                        _labels.push(key)
                        _data.push(value)
                        
                    }
                }
        
                stats.sme_meetings_count = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/sme_meetings_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // partner_meetings_count
        let partner_meetings_count = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const value = data[key];
                        _labels.push(key)
                        _data.push(value)
                        
                    }
                }
        
                stats.partner_meetings_count = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/partner_meetings_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // permanences_count
        let permanences_count = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const value = data[key];
                        _labels.push(key)
                        _data.push(value)
                        
                    }
                }
        
                stats.permanences_count = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/permanences_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        // untracked_tickets_count
        let untracked_tickets_count = (resolve) => {
            let fn = (data)=>{
                let _labels = []
                let _data =[]
        
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const value = data[key];
                        _labels.push(key)
                        _data.push(value)
                        
                    }
                }
        
                stats.untracked_tickets_count = {
                    labels : _labels,
                    data: _data,
                }
    
                resolve(stats)
            }

            $.ajax({
                url: baseUrl+"/stats/untracked_tickets_count",
                headers: {
                    'Authorization':'Bearer '+user_token,
                },
                method: 'GET',
                data: [],
                success: fn
            });
        }
    
        let promises = {}
    
        promises.tickets_per_reason_count = (new Promise(tickets_per_reason_count))
        promises.tickets_count = (new Promise(tickets_count))
        promises.pme_count = (new Promise(pme_count))
        promises.tickets_ratios = (new Promise(tickets_ratios))
        promises.sme_meetings_count = (new Promise(sme_meetings_count))
        promises.partner_meetings_count = (new Promise(partner_meetings_count))
        promises.permanences_count = (new Promise(permanences_count))
        promises.untracked_tickets_count = (new Promise(untracked_tickets_count))
        promises.user_count = (new Promise(user_count))
        promises.partner_count = (new Promise(partner_count))
        promises.all_tickets_count = (new Promise(all_tickets_count))
    
        Promise.all([promises.tickets_per_reason_count, promises.tickets_count, promises.pme_count, 
            promises.tickets_ratios, promises.sme_meetings_count, promises.partner_meetings_count,
            promises.permanences_count, promises.untracked_tickets_count,
            promises.user_count, promises.all_tickets_count, promises.partner_count
        ])
        .then(()=>{
            chart_1.data.labels = stats.tickets_per_reason_count.labels
            chart_1.data.datasets[0].data = stats.tickets_per_reason_count.data
            chart_1.update()
            
            chart_2.data.labels = stats.tickets_count.labels
            chart_2.data.datasets[0].data = stats.tickets_count.data
            chart_2.data.datasets[1].data = stats.untracked_tickets_count.data
            chart_2.update()
    
            chart_3.data.labels = stats.tickets_ratios.labels
            chart_3.data.datasets[0].data = stats.tickets_ratios.data
            chart_3.update()
            
            chart_4.data.labels = stats.sme_meetings_count.labels
            chart_4.data.datasets[0].data = stats.sme_meetings_count.data
            chart_4.update()
            
            chart_5.data.labels = stats.partner_meetings_count.labels
            chart_5.data.datasets[0].data = stats.partner_meetings_count.data
            chart_5.update()
            
            chart_6.data.labels = stats.permanences_count.labels
            chart_6.data.datasets[0].data = stats.permanences_count.data
            chart_6.update()
    
            $('#pme_count').text(stats.pme_count < 10 ? "0"+stats.pme_count : stats.pme_count)
            $('#partner_count').text(stats.partner_count < 10 ? "0"+stats.partner_count : stats.partner_count)
            $('#user_count').text(stats.user_count < 10 ? "0"+stats.user_count : stats.user_count)
            $('.all_tickets_count').text(stats.all_tickets_count < 10 ? "0"+stats.all_tickets_count : stats.all_tickets_count)
            
        })

    }


})