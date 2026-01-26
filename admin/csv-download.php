
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
<div class="jquery-script-clear"></div>
</div>
</div>
<div class="container" style="margin:150px auto">
<h1>jQuery TableCSVExport Plugin Example</h1>
  <table id='test-table' class="table">
    <thead>
      <tr>
	<th>Name</th>
	<th>Mon</th>
        <th>Tue</th>
	<th>Wed</th>
	<th>Thr</th>
	<th>Fri</th>
	<th>Sat</th>
	<th>Sun</th>
      </tr>
    </thead>
    <tr>
      <td>Zach</td>
      <td>1"s</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
      <td>0</td>
      <td>0</td>
    </tr>
    <tr>
      <td>Mark</td>
      <td>0</td>
      <td>0</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
    </tr>
    <tr>
      <td>Brett</td>
      <td>1</td>
      <td>1</td>
      <td>0</td>
      <td>0</td>
      <td>1</td>
      <td>1</td>
      <td>1</td>
    </tr>
    <tr>
      <td>Kiran</td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
    </tr>    
  </table>
  </div>
</body>
<script src="../excel/jquery.js"></script>
<script src='../excel/jquery.TableCSVExport.js'></script>
<script>
jQuery(document).ready(function() {
     jQuery('#test-table').TableCSVExport({
       header:['Name','Mon','Tue','Wed','Thr','Fri','Sat','Sun'],
       columns:['Name','Sat','Sun'],
       extraHeader: 'Id',
       extraData:['zwi"ck','markatto','bcsquire','ksingri'],
       insertBefore: "Name",
       delivery: 'download',
       filename: 'download-test.csv'
     });
});
jQuery(document).ready(function() {
     jQuery('#test-table').TableCSVExport({
       header:['Name','Mon','Tue','Wed','Thr','Fri','Sat','Sun'],
       columns:['Name','Sat','Sun'],
       extraHeader: 'Id',
       extraData:['zwi"ck','markatto','bcsquire','ksingri'],
       insertBefore: "Name",
       delivery: 'popup',
       filename: 'sales-report.csv'
     });
});
</script>
