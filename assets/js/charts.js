document.addEventListener('DOMContentLoaded', function(){
    // Build URL with current query string so charts respect filters
    const url = 'get_chart_data.php' + window.location.search;
    fetch(url).then(r => r.json()).then(data => {
        renderLine('monthlyChart', data.monthly.labels, data.monthly.values, 'Monthly Sales');
        renderBar('categoryChart', data.category.labels, data.category.values, 'Sales by Category');
        renderPie('regionChart', data.region.labels, data.region.values, 'Sales by Region');
    }).catch(e => console.error(e));
});

function renderBar(id, labels, values, label){
    new Chart(document.getElementById(id), { type:'bar', data:{ labels: labels, datasets:[{ label: label, data: values, backgroundColor:'#0d6efd' }]}, options:{responsive:true} });
}
function renderPie(id, labels, values, label){
    new Chart(document.getElementById(id), { type:'doughnut', data:{ labels: labels, datasets:[{ label: label, data: values }]}, options:{responsive:true} });
}
function renderLine(id, labels, values, label){
    new Chart(document.getElementById(id), { type:'line', data:{ labels: labels, datasets:[{ label: label, data: values, fill:true, tension:0.3 }]}, options:{responsive:true} });
}
