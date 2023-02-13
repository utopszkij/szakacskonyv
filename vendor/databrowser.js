/**
 * data browser class
 */
class DataBrowser {
    constructor(backendUrl, pageCount, total, listDomId, paginatorDomId) {
        this.backendUrl = backendUrl;
        this.pageCount = pageCount;
        this.total = total;
        this.listDomId = listDomId;
        this.paginatorDomId = paginatorDomId;
    }
    showItems(items, domId) {
        // abstract method, mindig átirandó
    }	
    showPaginator(page) {
        // paginátort jelenít meg a #domId  elemben
        page = parseInt(page); // azért, hogy biztosan számolni lehessen vele
        var pointCounter = 0; // megjelenitett '.' számláló
        var k = (window.innerWidth * 0.6) / 36; // össesen ennyi .paginatorItem fér ki
        k = (k - 5) / 2; // az aktuáis elemtől jobbra/balra ennyi fér ki.
        if (page == 1) {
            k = k + 2; // mivel nincs az első két elem; lehet több is
        } 
        if (page == this.pageCount) {
            k = k + 2; // mivel nincs az utolsó két elem; lehet több is
        } 
        var s='Összesen:'+this.total+' elem <br /><ul class="paginator">';
        if (page > 1) {	
            s += '<li><span class="paginatorItem2"><var href="#" onclick="dataBrowser.paginatorClick(1)" title="első">&lt;&lt;</var></span></li>';
            s += '<li><span class="paginatorItem2"><var href="#" onclick="dataBrowser.paginatorClick('+(page - 1)+')" title="elöző">&lt;</var></span></li>';
        }
        var p = 1;
        while (p <= this.pageCount) {
            if (p == page) {
                s += '<li><span class="actPaginatorItem"><strong>'+p+'</strong></span></li>';
                pointCounter = 0;
            } else {
                if (((p >= (page - k)) & (p < page)) | 
                    ((p <= (page + k)) & (p > page))) {	
                    s += '<li><span class="paginatorItem2"><var href="#" onclick="dataBrowser.paginatorClick('+p+')" title="'+p+'">'+p+'</var></span></li>';
                } else {
                    if (pointCounter == 0) {
                        s += '<li class="kimarad">.</li>';
                        pointCounter = pointCounter + 1;
                    }
                }	
            }	
            p = p + 1;
        }
        if (page < this.pageCount) {
            s += '<li><span class="paginatorItem2"><var href="#" onclick="dataBrowser.paginatorClick('+(page + 1)+')" title="következő">&gt;</var></span></li>';
            s += '<li><span class="paginatorItem2"><var href="#" onclick="dataBrowser.paginatorClick('+this.pageCount+')" title="utolsó">&gt;&gt;</var></span></li>';
        }
        s += '</ul>'
        document.getElementById(this.paginatorDomId).innerHTML = s; 
    }
    paginatorClick(page) {
        app.page = page;	
        document.getElementById(this.listDomId).innerHTML = '<tr><td colspan="15" style="text-align:center">'+
        '<img src="images/loader.gif" />'+
        '</td><Tr>';
        window.axios.get(this.backendUrl+'&page='+page+'&sid='+this.sid)
                .then(function(response) {
                    dataBrowser.showItems(response.data, dataBrowser.listDomId);
                });
        dataBrowser.showPaginator(page);
        return false;
    }
};