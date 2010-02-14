function highlight_datagrid(ctl_id) {
	var objTable = document.getElementById(ctl_id);
	if (objTable) {
	    var arrTds = objTable.getElementsByTagName('td');
	    for (var i=0;i<arrTds.length;i++) {
	        arrTds[i].onmouseover = highlight_datagrid_row;
	        arrTds[i].onmouseout = reset_datagrid_row;
	    }
	}
}

function highlight_datagrid_row() {
	if (this.parentNode.className == 'datagrid_row datagrid_even')
		this.parentNode.className = 'selected datagrid_row datagrid_even';
	else if (this.parentNode.className == 'datagrid_row datagrid_odd')
		this.parentNode.className = 'selected datagrid_row datagrid_odd';

}

function reset_datagrid_row() {
	if (this.parentNode.className == 'selected datagrid_row datagrid_even')
		this.parentNode.className = 'datagrid_row datagrid_even';
	else if (this.parentNode.className == 'selected datagrid_row datagrid_odd')
		this.parentNode.className = 'datagrid_row datagrid_odd';
}
